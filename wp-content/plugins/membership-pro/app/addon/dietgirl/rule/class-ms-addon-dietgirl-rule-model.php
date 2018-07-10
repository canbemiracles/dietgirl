<?php
/**
 * Membership Dietgirl Rule class.
 *
 * Persisted by Membership class.
 *
 * @since 4.1.2
 * @package Membership2
 * @subpackage Model
 */
class MS_Addon_Dietgirl_Rule_Model extends MS_Rule {

    /**
     * Rule type.
     *
     * @since 4.1.2
     * @var string $rule_type
     */
    protected $rule_type = MS_Addon_Dietgirl_Rule::RULE_ID;

    /**
     * Constructor
     *
     * @since 4.1.2
     * @param int $membership_id
     */
    public function __construct( $membership_id ) {
        parent::__construct( $membership_id );
    }

	/**
	 * Prevents button rendering.
	 *
	 * Related Action Hooks:
	 * - bp_get_button
	 *
	 * @since  1.0.0
	 *
	 * @return boolean false to prevent button rendering.
	 */
	public function prevent_button_rendering() {
		$this->remove_filter( 'bp_get_button', 'prevent_button_rendering' );

		return apply_filters(
			'ms_rule_dietgirl_prevent_button_rendering',
			false,
			$this
		);
    }

    /**
     * Get content to protect.
     *
     * @since 4.1.2
     *
     * @param $args Not used, but kept due to method override.
     * @return array The content eligible to procted by this rule domain.
     */
    public function get_contents( $args = null ) {

        $contents = [];

        $contents[ MS_Addon_Dietgirl_Rule::PUMPONE_FITNESS_CLASS ] = (object) [
            'id'            => MS_Addon_Dietgirl_Rule::PUMPONE_FITNESS_CLASS,
            'name'          => __( 'Pumpone Fitness Class', 'membership2' ),
            'type'          => $this->rule_type,
            'description'   => __( 'Protect the access to pumpone service.', 'membership2' ),
            'access'        => $this->get_rule_value( MS_Addon_Dietgirl_Rule::PUMPONE_FITNESS_CLASS ),
        ];
        $contents[ MS_Addon_Dietgirl_Rule::PUMPONE_PLUS ] = (object) [
            'id'            => MS_Addon_Dietgirl_Rule::PUMPONE_PLUS,
            'name'          => __( 'Pumpone Plus', 'membership2' ),
            'type'          => $this->rule_type,
            'description'   => __( 'Protect the access to pumpone service.', 'membership2' ),
            'access'        => $this->get_rule_value( MS_Addon_Dietgirl_Rule::PUMPONE_PLUS ),
        ];
        $contents[ MS_Addon_Dietgirl_Rule::PUMPONE_PRO ] = (object) [
            'id'            => MS_Addon_Dietgirl_Rule::PUMPONE_PRO,
            'name'          => __( 'Pumpone Pro', 'membership2' ),
            'type'          => $this->rule_type,
            'description'   => __( 'Protect the access to pumpone service.', 'membership2' ),
            'access'        => $this->get_rule_value( MS_Addon_Dietgirl_Rule::PUMPONE_PRO ),
        ];
        $contents[ MS_Addon_Dietgirl_Rule::PUMPONE_PT ] = (object) [
            'id'            => MS_Addon_Dietgirl_Rule::PUMPONE_PT,
            'name'          => __( 'Pumpone PT', 'membership2' ),
            'type'          => $this->rule_type,
            'description'   => __( 'Protect the access to pumpone service.', 'membership2' ),
            'access'        => $this->get_rule_value( MS_Addon_Dietgirl_Rule::PUMPONE_PT ),
        ];
        $contents[ MS_Addon_Dietgirl_Rule::VITABOT ] = (object) [
            'id'            => MS_Addon_Dietgirl_Rule::VITABOT,
            'name'          => __( 'Vitabot service', 'membership2' ),
            'type'          => $this->rule_type,
            'description'   => __( 'Protect the access to vitabot service.', 'membership2' ),
            'access'        => $this->get_rule_value( MS_Addon_Dietgirl_Rule::VITABOT ),
        ];
        return apply_filters(
            'ms_rule_dietgirl_get_content',
            $contents,
            $this
        );
    }
}
