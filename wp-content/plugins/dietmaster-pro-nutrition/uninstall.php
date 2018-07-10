<?php 
/**
 * Functions
 *
 * @package DietMaster-Integration
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

function wpdmi_delete_plugin() {
	global $wpdb;

	delete_option( 'wpdmi_version' );
	delete_option( 'wpdmi' );
	delete_option( 'wpdmi_token_email_text' );
	delete_option( 'wpdmi_hide_notice' );

	$table_name = $wpdb->prefix . "dietmaster";

	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

wpdmi_delete_plugin();

?>