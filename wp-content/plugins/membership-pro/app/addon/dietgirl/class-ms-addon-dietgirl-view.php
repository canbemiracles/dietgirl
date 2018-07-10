<?php

class MS_Addon_Dietgirl_View extends MS_View_Settings_Edit {

	/**
	 * Overrides parent's to_html() method.
	 *
	 * Creates an output buffer, outputs the HTML and grabs the buffer content before releasing it.
	 * HTML contains the list of available payment gateways.
	 *
	 * @since  4.1.2
	 *
	 * @return string
	 */
    public function to_html() {
        $fields = $this->get_global_dietgirl_fields();
        ob_start(); ?>
        <div id="ms-payment-settings-wrapper">
            <div class="ms-global-payment-wrapper">
                <div class="ms-list-table-wrapper">
                    <?php
                    MS_Helper_Html::settings_tab_header([
                        'title' => __( 'Dietgirl Settings', 'membership2' ),
                        'desc'  => __( 'Configuration data for pumpone and vitabot services.', 'membership2 ')
                    ]);
                    ?>
                    <div class="cf">
                        <div class="ms-half space">
                            <?php MS_Helper_Html::html_element( $fields['pumpone_api_key'] ) ?>
                        </div>
                        <div class="ms-half">
                            <?php MS_Helper_Html::html_element( $fields['pumpone_secret_key'] ) ?>
                        </div>
                        <div class="ms-half space">
                            <?php MS_Helper_Html::html_element( $fields['pumpone_success_url'] ) ?>
                        </div>
                    </div>

                    <?php MS_Helper_Html::html_separator(); ?>

                    <div class="cf">
                        <div class="ms-half space">
                            <?php MS_Helper_Html::html_element( $fields['vitabot_email'] ) ?>
                        </div>
                        <div class="ms-half">
                            <?php MS_Helper_Html::html_element( $fields['vitabot_password'] ) ?>
                        </div>
                        <div class="ms-half space">
                            <?php MS_Helper_Html::html_element( $fields['vitabot_success_url'] ) ?>
                        </div>
                    </div>

                </div>
                <?php MS_Helper_Html::settings_footer(); ?>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    protected function get_global_dietgirl_fields() {
        $settings = $this->data['settings'];
        $action = MS_Controller_Settings::AJAX_ACTION_UPDATE_CUSTOM_SETTING;
		$nonce = wp_create_nonce( $action );

        $fields = [
            'pumpone_api_key' => [
                'id'    => 'pumpone_api_key',
                'name'  => 'custom[dietgirl][pumpone_api_key]',
                'type'  => MS_Helper_Html::INPUT_TYPE_TEXT,
                'title' => __( 'Pumpone Api Key', 'membership2' ),
                'class' => 'inputDietgirl',
                'value' => $settings->get_custom_setting( 'dietgirl', 'pumpone_api_key' ),
                'ajax_data' => [
                    'group'     => 'dietgirl',
                    'field'     => 'pumpone_api_key',
                    'action'    => $action
                ]
            ],

            'pumpone_secret_key' => [
                'id'        => 'pumpone_secret_key',
                'name'  => 'custom[dietgirl][pumpone_secret_key]',
                'type'      => MS_Helper_Html::INPUT_TYPE_TEXT,
                'title'     => __( 'Pumpone Secret Key', 'membership2' ),
                'value'     => $settings->get_custom_setting( 'dietgirl', 'pumpone_secret_key' ),
                'class' => 'inputDietgirl',
                'ajax_data' => [
                    'group'     => 'dietgirl',
                    'field'     => 'pumpone_secret_key',
                    'action'    => $action
                ]
            ],

            'pumpone_success_url' => [
                'id'        => 'pumpone_success_url',
                'name'  => 'custom[dietgirl][pumpone_success_url]',
                'type'      => MS_Helper_Html::INPUT_TYPE_TEXT,
                'title'     => __( 'Pumpone success url', 'membership2' ),
                'value'     => $settings->get_custom_setting( 'dietgirl', 'pumpone_success_url' ),
                'class' => 'inputDietgirl',
                'ajax_data' => [
                    'group'     => 'dietgirl',
                    'field'     => 'pumpone_success_url',
                    'action'    => $action
                ]
            ],

            'vitabot_email' => [
                'id'        => 'vitabot_email',
                'name'  => 'custom[dietgirl][vitabot_email]',
                'type'      => MS_Helper_Html::INPUT_TYPE_TEXT,
                'title'     => __( 'Vitabot email', 'membership2' ),
                'value'     => $settings->get_custom_setting( 'dietgirl', 'vitabot_email' ),
                'class' => 'inputDietgirl',
                'ajax_data' => [
                    'group'     => 'dietgirl',
                    'field'     => 'vitabot_email',
                    'action'    => $action
                ]
            ],

            'vitabot_password' => [
                'id'        => 'vitabot_password',
                'name'  => 'custom[dietgirl][vitabot_password]',
                'type'      => MS_Helper_Html::INPUT_TYPE_TEXT,
                'title'     => __( 'Vitabot password', 'membership2' ),
                'value'     => $settings->get_custom_setting( 'dietgirl', 'vitabot_password' ),
                'class' => 'inputDietgirl',
                'ajax_data' => [
                    'group'     => 'dietgirl',
                    'field'     => 'vitabot_password',
                    'action'    => $action
                ]
            ],

            'vitabot_success_url' => [
                'id'        => 'vitabot_success_url',
                'name'  => 'custom[dietgirl][vitabot_success_url]',
                'type'      => MS_Helper_Html::INPUT_TYPE_TEXT,
                'title'     => __( 'Vitabot success url', 'membership2' ),
                'value'     => $settings->get_custom_setting( 'dietgirl', 'vitabot_success_url' ),
                'class' => 'inputDietgirl',
                'ajax_data' => [
                    'group'     => 'dietgirl',
                    'field'     => 'vitabot_success_url',
                    'action'    => $action
                ]
            ]
        ];
		foreach ( $fields as $key => $field ) {
			if ( is_array( $field['data_ms'] ) ) {
				$fields[ $key ]['data_ms']['_wpnonce'] = $nonce;
				$fields[ $key ]['data_ms']['action'] = $action;
			}
		}
        return $fields;
    }

}
