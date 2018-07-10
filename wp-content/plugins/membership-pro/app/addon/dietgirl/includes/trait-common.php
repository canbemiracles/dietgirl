<?php

trait Common {
    public function get_membership_id( $user_id ) {
        $api = ms_api();
        $list_ms = $api->list_memberships();
        foreach ( $list_ms as $_m ) {
            $m = MS_Model_Relationship::get_subscription( $user_id, $_m->id );
            if ( $m and strcmp( $m->status, "active") === 0 ) return $m->membership_id;
        }
    }

    /**
     * Login
     *
     * @since 4.1.2
     * @param mixed $login
     * @param mixed $user
     */
    public function login( $login, $user ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            $this->login_pumpone( $user->data, 1 );
            $this->add_vitabot_member( $user, false );
        } else {
            // obtener la membresia del usuario
            // ver a que servicios tiene acceso su membresia
            $membership = $this->get_membership_id( $user->ID );
            $this->link_up( $membership, $user->data );
        }
    }

    public function link_up( $membership, $userdata ) {
        // Plus=1, Pro=2, PT=3, FitnessClass=4
        // Depending on your All Access license, there can be up to four screens within this tab: "Plus", Pro, PT and FC.
        // Plus, Pro and PT refer to FitnessBuilder access
        // And FC refers to Pass in FitnessClass.
        // Clicking on a tab will list the users for that access, whether they've been activated or not.
        // Possible combinations:
        // Plus - FitnessClass
        // Pro - FitnessClass
        // PT - FitnessClass
        if ( in_array( $membership, $this->memberships_pumpone_fc ) )
        {   
            // Fitness Class
            $this->login_pumpone( $userdata, 4 );
        }
        
        if ( in_array( $membership, $this->memberships_pumpone_plus ) )
        {
            // Plus
            $this->login_pumpone( $userdata, 1 );
        }
        elseif ( in_array( $membership, $this->memberships_pumpone_pro ) )
        {
            // Pro
            $this->login_pumpone( $userdata, 2 );
        }
        elseif ( in_array( $membership, $this->memberships_pumpone_pt ) )
        {
            // PT
            $this->login_pumpone( $userdata, 3 );
        }

        if ( in_array( $membership, $this->memberships_vitabot )) {
            $this->add_vitabot_member( $userdata );
        }
    }


    /**
     * Memberships are established with access to pumpone or vitabot.
     *
     * @since   4.1.2
     * @return  array
     */
    public static function load_memberships( $str_rule ) {
        $rule = new MS_Addon_Dietgirl_Rule_Model( MS_Addon_Dietgirl_Rule::RULE_ID );
        return array_keys( $rule->get_memberships( $str_rule ) );
    }

    /**
     * Signed up
     *
     * @since 4.1.2
     * @param $event
     * @param $relationship
     */
    public function signed_up( $event, $relationship ) {

        $userdata = get_userdata( intval( $event->user_id ) );


        // Plus=1, Pro=2, PT=3, FitnessClass=4
        // PT >> PRO

        $membership = $relationship->membership_id;
        $this->link_up( $membership, $userdata );
    }

    /**
     * A new user registered (not a Member yet).
     *
     * @since 4.1.2
     * @param mixed $event
     * @param mixed $member
     */
    public function user_registered ( $event, $member ) {
        add_user_meta( $member->id, 'has_vitabot', 'false', true );
        add_user_meta( $member->id, 'mafteaj', $member->password, true );
    }

    /**
     * Personal Update
     *
     * @since 4.1.2
     */
    function personal_update ( $user_id ) {
        $user = get_user_by( 'id', $user_id );
        $password = $_POST['pass1'];
        if ( $this->has_vitabot_membership( $user_id ) ) {
            $this->update_password_vitabot( $user, $password );
        }
        update_user_meta( $user_id, 'mafteaj', $password );

    }

    /**
     * Logout
     *
     * @since 4.1.2
     */
    public function logout () {
        $session = WP_Session_Tokens::get_instance( $user->ID );

        unset( $_SESSION['pumpone'] );
    }

    /**
     * Deactivate Member
     *
     * @since 4.1.2
     */
    public function deactivate_member ($event, $data) {
        $user = get_userdata( $event->user_id );
        $http = new WP_Http();
        $membership_id = intval($event->membership_id);

        if ( in_array($membership_id, [self::DG_BODY_SCULPING, self::DG_FITNESS_CLASS]) ) {
            $args = array_merge( $this->args_pumpone(), ['subscription_type' => $this->subscription_type($membership_id), 'user_id' => $user->ID] );
            $response = $http->request( $this->buildURL( sprintf( self::PUMPONE_URI, 'remove_access' ), $args ) );
            unset($_SESSION['pumpone']);
        } elseif ( $membership_id === self::VITABOT_MEMBERSHIP ) {
            $args = array_merge( $this->args_vitabot(), ['action' => 'deactivate', 'username' => $user->user_login] );
            $response = $http->request( $this->buildURL( self::VITABOT_URI, $args ) );
        }
    }

    /**
     * Add Member When Paid
     *
     * @since 4.1.2
     */
    public function add_member_when_paid ( $event, $data ) {
        $user = get_userdata( $event->user_id );
        $membership_id = intval($event->membership_id);

        if ( in_array($membership_id, [self::DG_BODY_SCULPING, self::DG_FITNESS_CLASS]) ) {
            $this->login_pumpone( $user, $membership_id );
        } elseif ( in_array($membership_id, [self::VITABOT_MEMBERSHIP, self::FREE_TRIAL_MEMBERSHIP]) ) {
            $this->add_vitabot_member( $user );
        }
    }

    /**
     * Renewed Member
     *
     * @since 4.1.2
     */
    public function renewed_member ($event, $data) {
        $user = get_userdata( $event->user_id );
        $http = new WP_Http();
        $membership_id = intval($event->membership_id);

        if ( in_array($membership_id, [self::DG_BODY_SCULPING, self::DG_FITNESS_CLASS]) ) {
            $args = array_merge( $this->args_pumpone(), ['subscription_type' => $this->subscription_type($membership_id), 'user_id' => $user->ID] );
            $http->request( $this->buildURL( sprintf( self::PUMPONE_URI, 'grant_access' ), $args ) );
            $this->login_pumpone( $user, $membership_id );
        } elseif ( in_array( $membership_id, [self::VITABOT_MEMBERSHIP, self::FREE_TRIAL_MEMBERSHIP] ) ) {
            $args = array_merge( $this->args_vitabot(), ['action' => 'reactivate', 'username' => $user->user_login] );
            $response = $http->request( $this->buildURL( self::VITABOT_URI, $args ) );
            $this->redirect_vitabot();
        }
    }

    /**
     * Has Membership?
     *
     * @since 4.1.2
     * @param int $user_id
     * @param int $membership_id
     * @return bool
     */
    public function has_membership( $user_id, $membership_id )
    {
        $membership = MS_Model_Relationship::get_subscription($user_id, $membership_id);
        if ( $membership and strcmp( $membership->status, "active" ) === 0 )
        {
            return true;
        }
        return false;
    }

    /**
     * Build url
     *
     * @since 4.1.2
     * @param string $url
     * @param mixed $args
     * @return string
     */
    public function buildURL ( $url, $args )
    {
    	foreach ( $args as $k => $v ) {
        	$url = "{$url}{$k}={$v}&";
    	}
    	return $url;
    }
}
