<?php

class MS_Addon_Dietgirl_Rule extends MS_Controller {

    /**
     * The rule ID.
     *
     * @type string
     */
    const RULE_ID = 'dietgirl';

    const VITABOT   = 'dietgirl_vitabot';

    // Pumpone
    // Plus=1, Pro=2, PT=3, FitnessClass=4
    const PUMPONE_PLUS          = 'dietgirl_pumpone_plus';
    const PUMPONE_PRO           = 'dietgirl_pumpone_pro';
    const PUMPONE_PT            = 'dietgirl_pumpone_pt';
    const PUMPONE_FITNESS_CLASS = 'dietgirl_pumpone_fitness_class';

    /**
     * Setup the rule.
     *
     * @since 4.1.2
     */
    public function prepare_obj() {
        MS_Model_Rule::register_rule(
            self::RULE_ID,
            __CLASS__,
            __( 'Dietgirl', 'membership2' ),
            40
        );
        $this->add_filter(
            'ms_view_protectedcontent_define-' . self::RULE_ID,
            'handle_render_callback', 10, 2
        );
    }

	/**
	 * Tells Membership2 Admin to display this form to manage this rule.
	 *
	 * @since  4.1.2
	 *
	 * @param array $callback (Invalid callback)
	 * @param array $data The data collection.
	 * @return array Correct callback.
	 */
	public function handle_render_callback( $callback, $data ) {
		$view = MS_Factory::load( 'MS_Addon_Dietgirl_Rule_View' );

		$view->data = $data;
		$callback = array( $view, 'to_html' );

		return $callback;
	}
}
