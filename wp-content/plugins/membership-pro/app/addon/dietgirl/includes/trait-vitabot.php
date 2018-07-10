<?php

trait Vitabot
{
    public function args_vitabot() {
        return [ 'rs_username' => $this->vitabotEmail, 'rs_password' => $this->vitabotPassword ];
    }

    public function add_vitabot_member ( $user, $redirect = true ) {
        // PC::debug('ok', 'add_vitabot_member');
        $has_vitabot = get_user_meta( $user->ID, 'has_vitabot', true ) === 'true' ? true : false;
        if ( $has_vitabot !== true ) {
            $http = new WP_Http();
            $password = get_user_meta( $user->ID, 'mafteaj', true );
            $args = array_merge(
                $this->args_vitabot(),
                ['action'    => 'add',
                 'email'     => $user->user_email,
                 'last_name' => $user->user_login,
                 'password'  => $password,
                 'username'  => $user->user_login,
                 'orderID'   => null]  // Falta el orderID
            );
            $response = $http->request( $this->buildURL( self::VITABOT_URI, $args ) );
            if ( !strpos($response['body'], 'Error') ) {
                update_user_meta( $user->ID, 'has_vitabot', 'true' );
            } else {
                // PC::debug($response);
            }
        }
        if ( $redirect ) $this->redirect_vitabot();
    }

    public function has_vitabot_membership ( $user_id )
    {
        if ( $this->has_membership($user_id, self::VITABOT_MEMBERSHIP) ) {
            return true;
        }
        return false;
    }

    public function redirect_vitabot () {
        wp_redirect( home_url( $this->vitabotSuccessUrl ) );
        exit;
    }

    public function update_password_vitabot ( $user, $new_pass )
    {
        $old_pass = get_user_meta( $user->ID, 'mafteaj', true );
        if ( $new_pass !== $old_pass)
        {
            $http = new WP_Http();
            $args = array_merge(
                $this->args_vitabot(),
                [
                    'action'    => 'set',
                    'parameter' => 'password',
                    'username'  => $user->user_login,
                    'value'     => $new_pass,
                ]
            );
            $res = $http->request( $this->buildURL( self::VITABOT_URI, $args) );
        }
    }
}
