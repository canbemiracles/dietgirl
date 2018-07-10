<?php
include_once 'includes/trait-common.php';
include_once 'includes/trait-pumpone.php';
include_once 'includes/trait-vitabot.php';

/**
 * Add-On controller for: Dietgirl
 *
 * @since 4.1.2
 *
 * @author      Jonatan Rodriguez <jrperdomoz@gmail.com>
 * @package     Membership2
 * @subpackage  Controller
 */
class MS_Addon_Dietgirl extends MS_Addon {

    use Common, Pumpone, Vitabot;

    const PUMPONE_URI = 'https://www.pumpone.com/api/v1/sso/%s?';
    const VITABOT_URI = 'https://secure.rocketos.com/microwavescorp/nutrition/join/rsjoin.php?';

    /**
     * The Add-on ID
     *
     * @since 4.1.2
     */
    const ID = 'dietgirl';

    /**
     * Pumpone Api Key
     *
     * @since  4.1.2
     */
    protected $pumponeApiKey = null;

    /**
     * Pumpone Secret Key
     *
     * @since  4.1.2
     */
    protected $pumponeSecretKey = null;

    /**
     * Pumpone Success Url
     *
     * @since  4.1.2
     */
    protected $pumponeSuccessUrl = null;

    /**
     * Vitabot Email
     *
     * @since  4.1.2
     */
    protected $vitabotEmail = null;

    /**
     * Vitabot Password
     *
     * @since  4.1.2
     */
    protected $vitabotPassword = null;

    /**
     * Vitabot Success Url
     *
     * @since  4.1.2
     */
    protected $vitabotSuccessUrl = null;

    /**
     * Memberships with access to pumpone
     *
     * @since  4.1.2
     */
    protected $memberships_pumpone_fc = null;
    protected $memberships_pumpone_plus = null;
    protected $memberships_pumpone_pro = null;
    protected $memberships_pumpone_pt = null;

    /**
     * Memberships with access to vitabot
     *
     * @since  4.1.2
     */
    protected $memberships_vitabot = null;

    /**
     * Checks if the current Add-on is enabled
     *
     * @since 4.1.2
     * @return bool
     */
    static public function is_active() {
        return MS_Model_Addon::is_enabled( self::ID );
    }

    /**
     * Returns the Add-on ID (self::ID).
     *
     * @since 4.1.2
     * @return string
     */
    public function get_id() {
        return self::ID;
    }

	/**
	 * Initializes the Add-on. Always executed.
	 *
	 * @since  4.1.2
	 */
	public function init() {
        if ( self::is_active() ) {
            // Charge the value of the fields for Pumpone
            $this->pumponeApiKey = $this->get_setting( 'pumpone_api_key' );
            $this->pumponeSecretKey = $this->get_setting( 'pumpone_secret_key' );
            $this->pumponeSuccessUrl = $this->get_setting( 'pumpone_success_url' );
            // Charge the value of the fields for Vitabot
            $this->vitabotEmail = $this->get_setting( 'vitabot_email' );
            $this->vitabotPassword = $this->get_setting( 'vitabot_password' );
            $this->vitabotSuccessUrl = $this->get_setting( 'vitabot_success_url' );

            $this->add_action( 'ms_controller_settings_enqueue_scripts_' . self::ID, 'enqueue_scripts' );

            $this->add_filter( 'ms_controller_settings_get_tabs', 'settings_tabs', 10, 2 );

            $this->add_filter( 'ms_view_settings_edit_render_callback', 'manage_render_callback', 10, 3 );

            $this->add_filter( 'ms_controller_protection_tabs', 'rule_tabs' );

            MS_Factory::load( 'MS_Addon_Dietgirl_Rule' );

            $this->memberships_pumpone_fc = self::load_memberships( MS_Addon_Dietgirl_Rule::PUMPONE_FITNESS_CLASS );
            $this->memberships_pumpone_plus = self::load_memberships( MS_Addon_Dietgirl_Rule::PUMPONE_PLUS );
            $this->memberships_pumpone_pro = self::load_memberships( MS_Addon_Dietgirl_Rule::PUMPONE_PRO );
            $this->memberships_pumpone_pt = self::load_memberships( MS_Addon_Dietgirl_Rule::PUMPONE_PT );
            $this->memberships_vitabot = self::load_memberships( MS_Addon_Dietgirl_Rule::VITABOT );

            PC::debug($this, 'Addon Dietgirl');

            // ------------------------------------------

            # Watch for REGISTER event
            $this->add_action( 'ms_model_event_'. MS_Model_Event::TYPE_MS_REGISTERED, 'user_registered', 10, 2 );

            # Watch for "personal_options_update" event
            // $this->add_action( 'personal_options_update', 'personal_update', 10, 1 );

            # Watch for LOGIN event
            $this->add_action( 'wp_login', 'login', 1, 2 );

            # Watch for LOGOUT event
            $this->add_action( 'wp_logout', 'logout');

            # Watch for TYPE_MS_SIGNED_UP event
            $this->add_action( 'ms_model_event_'. MS_Model_Event::TYPE_MS_SIGNED_UP, 'signed_up', 10, 2 );

            # Watch for TYPE_MS_CANCELED event
            $this->add_action( 'ms_model_event_'. MS_Model_Event::TYPE_MS_CANCELED, 'deactivate_member', 11, 2 );

            # Watch for TYPE_PAID event
            $this->add_action( 'ms_model_event_'. MS_Model_Event::TYPE_PAID, 'add_member_when_paid', 12, 2 );

            # Watch for TYPE_MS_RENEWED event
            $this->add_action( 'ms_model_event_'. MS_Model_Event::TYPE_MS_RENEWED, 'renewed_member', 13, 2 );

        }
    }

    /**
	 * Registers the Add-On
	 *
	 * @since  4.1.2
	 * @param  array $list The Add-Ons list.
	 * @return array The updated Add-Ons list.
	 */
	public function register( $list ) {
		$list[ self::ID ] = (object) array(
			'name' 			=> __( 'Dietgirl Integration', 'membership2' ),
			'description' 	=> __( 'Enable Dietgirl integration.', 'membership2' ),
            'icon' 			=> 'dashicons dashicons-admin-tools',
            'details'       => [[
                'type'      => MS_Helper_Html::TYPE_HTML_TEXT,
                'title'     => __( 'Protection Rules',  'membership2' ),
                'desc'      => __( 'Adds Dietgirl rules in the "Protection Rules" page.', 'membership2' ),
            ], [
                'type'      => MS_Helper_Html::TYPE_HTML_TEXT,
                'title'     => __( 'Settings',  'membership2' ),
                'desc'      => __( 'Adds Dietgirl settings in the "Settings" page.', 'membership2' ),
            ]]
		);

		return $list;
    }

	/**
	 * Add dietgirl rule tabs in membership level edit.
	 *
	 * @since  4.1.2
	 *
	 * @filter ms_controller_membership_get_tabs
	 *
	 * @param array $tabs The current tabs.
	 * @param int $membership_id The membership id to edit
	 * @return array The filtered tabs.
	 */
    public function rule_tabs( $tabs ) {
        $rule = MS_Addon_Dietgirl_Rule::RULE_ID;
        $tabs[ $rule ] = true;

        return $tabs;
    }

	/**
	 * Add dietgirl settings tab in settings page.
	 *
	 * @since  4.1.2
	 *
	 * @filter ms_controller_membership_get_tabs
	 *
	 * @param  array $tabs The current tabs.
	 * @param  int $membership_id The membership id to edit
	 * @return array The filtered tabs.
	 */
	public function settings_tabs( $tabs ) {
		$tabs[ self::ID  ] = array(
			'title' => __( 'Dietgirl', 'membership2' ),
			'url' 	=> MS_Controller_Plugin::get_admin_url(
				'settings',
				array( 'tab' => self::ID )
			),
		);

		return $tabs;
    }

	/**
	 * Enqueue admin scripts in the settings screen.
	 *
	 * @since  4.1.2
	 */
	public function enqueue_scripts() {
        $plugin_url = MS_Plugin::instance()->url;
		wp_enqueue_style(
			'ms-addon-dietgirl',
			$plugin_url . '/app/addon/dietgirl/assets/css/dietgirl.css'
		);
    }

	/**
	 * Add dietgirl views callback.
	 *
	 * @since  4.1.2
	 *
	 * @filter ms_view_settings_edit_render_callback
	 *
	 * @param  array $callback The current function callback.
	 * @param  string $tab The current membership rule tab.
	 * @param  array $data The data shared to the view.
	 * @return array The filtered callback.
	 */
	public function manage_render_callback( $callback, $tab, $data ) {
		if ( self::ID == $tab ) {
			$view 		= MS_Factory::load( 'MS_Addon_Dietgirl_View' );
			$view->data = $data;
			$callback 	= array( $view, 'to_html' );
		}
		return $callback;
    }

    /**
     * Dietgirl Error logging
     *
     * @since 4.1.2
     */
    static public function dietgirl_log ( $message ) {
        if( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            lib3()->debug->log( '[M2] Dietgirl Error : ' . $message );
        }
    }

}
