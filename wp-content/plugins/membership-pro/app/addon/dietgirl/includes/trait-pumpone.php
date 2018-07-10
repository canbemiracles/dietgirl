<?php

trait Pumpone {

    public function login_pumpone( $user, $subscription_type ) {
        $http = new WP_Http();
        $args = array_merge(
            $this->args_pumpone(),
            ['email' => $user->user_email,
             'subscription_type' => $subscription_type,
             'user_id' => intval( $user->ID ) ]);
        // PC::debug( $args , 'Request' );
        $response = $http->request( $this->buildURL( sprintf( self::PUMPONE_URI, 'login' ), $args ) );
        $data = json_decode( $response['body'] );

        // PC::debug( $data, 'Response' );
        $token = get_user_meta( $user->ID, "token_pumpone", true );
        if ( $token ) {
            update_user_meta( $user->ID, "token_pumpone", $data->token );
        } else {
            add_user_meta( $user->ID, 'token_pumpone', $data->token, true );
        }
    }

    public function args_pumpone () {
        return ['api_key' => $this->pumponeApiKey, 'api_secret' => $this->pumponeSecretKey];
    }

    public function has_pumpone_membership ( $user_id )
    {
        if ( ($this->has_membership($user_id, self::DG_BODY_SCULPING) or
              $this->has_membership($user_id, self::DG_FITNESS_CLASS)) ) {
            return true;
        }
        return false;
    }

    public function subscription_type( $membership_id )
    {
        // Plus=1, Pro=2, PT=3, FitnessClass=4
        if ( $membership_id === self::DG_BODY_SCULPING ) {
            return 1;
        } elseif ( $membership_id === self::DG_FITNESS_CLASS ) {
            return 4;
        }
    }

    public function redirect_pumpone () {
        wp_redirect( home_url( $this->pumponeSuccessUrl ) );
        exit;
    }

    public function user_exists_pumpone ( $user ) {
        // Verificar que existe usuario en pumpone
        $http = new WP_Http();
        $args = array_merge( $this->args_pumpone(), ['user_id' => $user->ID] );
        $response = $http->request( $this->buildURL( sprintf( self::PUMPONE_URI, 'user_exists'), $args));
        $body = json_decode($response['body']);
        if ( $response['response']['code'] === 200 and $body->exists ) {
            return true;
        }
        return false;
    }

}
