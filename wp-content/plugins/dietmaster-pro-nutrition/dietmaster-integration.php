<?php 
/**
 * Plugin Name: Dietmaster Integration
 * Plugin URI: http://dietmastersoftware.com/
 * Description: This plugin allows a seamless integration of Dietmaster Software features within a membership-driven site.
 * Version: 1.3.0
 * Author: Shingo Suzumura <shingo@locuswebmarketing.com>
 * Author URI: http://locuswebmarketing.com/
 * License: GPL2
 *  
 * Copyright 2014 Shingo Suzumura (email: shingo at locuswebmarketing.com)

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * @package DietMaster-Integration
 * @version 1.3.0
 */

define( 'WPDMI_VERSION', '1.3.0' );
define( 'WPDMI_REQUIRED_WP_VERSION', '3.8' );
define( 'WPDMI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPDMI_PLUGIN_NAME', trim( dirname( WPDMI_PLUGIN_BASENAME ), '/' ) );
define( 'WPDMI_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'WPDMI_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'WPDMI_PLUGIN_TXT_DOMAIN', 'dietmaster-integration' );

require_once WPDMI_PLUGIN_DIR . '/includes/functions.php';

if ( is_admin() ) {
	require_once WPDMI_PLUGIN_DIR . '/includes/admin.php';
}
	
/* Initialize */

add_action( 'init', 'wpdmi_init' );

function wpdmi_init() {
	do_action( 'wpdmi_init' );
}

/* Upgrade */

add_action( 'admin_init', 'wpdmi_upgrade' );

function wpdmi_upgrade() {
	
	if ( $_GET[ 'activate' ] == 'true' )
		add_action( 'admin_notices', 'wpdmi_admin_notices' );
	
	$old_ver = get_option( 'wpdmi_version' );

	if ( $old_ver == WPDMI_VERSION )
		return;
		
	update_option( 'wpdmi_version', WPDMI_VERSION);
	
	//handle database upgrade in the future
	//do_action( 'wpdmi_upgrade', $new_ver, $old_ver );
}

/* Install and default settings */

add_action( 'activate_' . WPDMI_PLUGIN_BASENAME, 'wpdmi_install' );

function wpdmi_install() {
	global $wpdb;
	
	//Options already exist
	if ( $opt = get_option( 'wpdmi_version' ) )
		return;

	wpdmi_upgrade();

	//Create + update plugin table
	$wpdmi_table = $wpdb->prefix . "dietmaster";
	
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}

	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$sql = "CREATE TABLE $wpdmi_table (
		uid int(11) NOT NULL AUTO_INCREMENT,
		site_userid int(11) NOT NULL,
		ip_address varchar(255) NOT NULL,
		email varchar(255) NOT NULL,
		posted datetime NOT NULL,
		user varchar(255) NOT NULL,
		pass varchar(255) NOT NULL,
		hash varchar(255) NOT NULL,
		dm_userid int(11) NOT NULL,
		mobile_token varchar(255) NOT NULL,
		active int(1) unsigned NOT NULL DEFAULT '1',
		general_units int(1) unsigned NOT NULL DEFAULT '0',
		energy_unit int(1) unsigned NOT NULL DEFAULT '0',
		date_format int(1) unsigned NOT NULL DEFAULT '0',
		birthdate text NOT NULL,
		gender int(1) unsigned NOT NULL DEFAULT '1',
		height int(11) NOT NULL DEFAULT '12',
		weight int(11) NOT NULL DEFAULT '1',
		lactation int(1) unsigned NOT NULL DEFAULT '0',
		bmr_calc_method int(1) unsigned NOT NULL DEFAULT '0',
		rmr_value int(11) NULL,
		bmr int(11) NULL,
		body_type int(1) unsigned NOT NULL DEFAULT '0',
		profession int(1) unsigned NOT NULL DEFAULT '0',
		weight_goals int(1) unsigned NOT NULL DEFAULT '0',
		goal_weight int(11) NULL,
		goal_rate float(11) NULL,
		meal_type int(11) NOT NULL DEFAULT '1',
		hide_templates int(1) unsigned NOT NULL DEFAULT '0',
		heart int(1) unsigned NOT NULL DEFAULT '0',
		liver int(1) unsigned NOT NULL DEFAULT '0',
		pancreatic int(1) unsigned NOT NULL DEFAULT '0',
		anemia int(1) unsigned NOT NULL DEFAULT '0',
		kidney int(1) unsigned NOT NULL DEFAULT '0',
		hypoglycemia int(1) unsigned NOT NULL DEFAULT '0',
		diabetes int(1) unsigned NOT NULL DEFAULT '0',
		hypertension int(1) unsigned NOT NULL DEFAULT '0',
		h_heart int(1) unsigned NOT NULL DEFAULT '0',
		h_breast_cancer int(1) unsigned NOT NULL DEFAULT '0',
		h_cancer_other int(1) unsigned NOT NULL DEFAULT '0',
		h_liver int(1) unsigned NOT NULL DEFAULT '0',
		h_stroke int(1) unsigned NOT NULL DEFAULT '0',
		h_osteoporosis int(1) unsigned NOT NULL DEFAULT '0',
		h_hypoglycemia int(1) unsigned NOT NULL DEFAULT '0',
		h_diabetes int(1) unsigned NOT NULL DEFAULT '0',
		h_hypertension int(1) unsigned NOT NULL DEFAULT '0',
		PRIMARY  KEY  (uid),
		UNIQUE KEY uid (uid)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	//Default Plugin Options
	$wpdmi_options = array (
		'integration_method' => 'profile_passthru',	//profile_passthru or reserveuser_passthru
		'page_accessdenied' => '',				//page ID for access denied redirect
		'passthru_key' => '',					//Given by DietMaster
		'dmwebpro_url' => '',				//Your DM Web Pro
		'hide_install_notice' => 0,				//Hide post-installation message
		'mealplan_id' => array( '1' ),			//Default meal plan ID
		'mealplan_name' => array( 'Low Fat' ),	//Default meal plan name
		'mealplan_active' => array( '1' ),		//Default meal plan is active
		//'allowed_levels' => array()			//
	);
	
	//Create a default login page if it doesn't exist
	if ( !get_page_by_title( 'Online Nutrition Login' ) ) {
		
		$inserted_post_ID = wp_insert_post( array(
			'post_type' => 'page',
			'post_title' => __( 'Online Nutrition Login', WPDMI_PLUGIN_TXT_DOMAIN ),
			'post_content' => __( ' Automatically generated by Dietmaster Plugin as its default login page. Edit this page or go to Settings > Dietmaster to choose another page if you\'d like.', WPDMI_PLUGIN_TXT_DOMAIN ),
			'post_status' => 'publish',
		) );
		//$inserted_page = get_post($inserted_post_ID);
		
		if ( $inserted_post_ID ) {
			$wpdmi_options[ 'passthru_page' ] = $inserted_post_ID;
		}
	}
		
	update_option( 'wpdmi', $wpdmi_options );
	
	// Ability for admin to edit email notification template
	
	$wpdmi_default_email_template = "Dear User,

Welcome to your DietMaster Go online and mobile account. This email will provide you with access to both your personalized online account and your mobile phone account. For your convenience and lifestyle, you can use either of these accounts to access your personalized meal plans, grocery lists, and logging tools with your computer's internet browser or your mobile smart phone. If you need help, click on the video tutorial link below to learn how to use your personalized nutrition tools.

Online Internet Account Login:

[DM_URL]

User Name = [DM_USERNAME]
Password = [DM_PASSWORD]

DietMaster Go Mobile App Login:

User Name = [DM_USERNAME]
Password = [DM_MOBILE_TOKEN]

Mobile App Instructions: Download using the options below for your specific device. Launch the app and type in your Mobile Username and Password in the designated fields. PLEASE NOTE: Your password for the 'DietMaster Go' app will be different from your Internet account login. Your web profile will be pushed to your smart phone provided you have a cellular or WiFi connection. All food, activity and weight logged on your iPhone will be sync'd with your web cloud account automatically.

iPhone Users: You must download the 'DietMaster Go' app to your iPhone or iPad by visiting the Apple App Store. Select the Search option and type 'DietMaster Go' to locate the app. Once downloaded, launch the app and type in your Mobile Username and Password in the designated fields. Your web profile, including daily calorie goal, will be pushed to your iPhone provided you have a cellular or WiFi connection. All food, activity and weight logged on your iPhone will be sync'd with your web cloud account automatically.

iPad Users: When searching for the 'DietMaster Go' app, make sure you specify your search to be iPhone and not 'iPad Only' or the app will not be found.

Android Users: Download the 'DietMaster Go' app for free by accessing the Google Play store and search for 'DietMaster Go'.


Tutorial: Visit http://youtu.be/bYb2usmbB9w to view a tutorial.

If you have any questions or need any assistance please feel free to contact us.

--The Team @ [DM_SITE_NAME]";
	
	update_option( 'wpdmi_token_email_text', $wpdmi_default_email_template );
}