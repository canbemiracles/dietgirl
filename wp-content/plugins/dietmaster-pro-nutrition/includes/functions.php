<?php 
/**
 * Functions
 *
 * @package DietMaster-Integration
 */
 
// Global variables
$wpdmi_settings = get_option( 'wpdmi' );
$DM_WebURL = 'https://' . $wpdmi_settings[ 'dmwebpro_url' ];
$wpdmi_table = $wpdb->prefix . "dietmaster";

add_action( 'template_redirect', 'wpdmi_process_user' );

function wpdmi_process_user() {
	global $current_user, $wpdmi_settings, $post, $wpdb, $wpdmi_notice, $wpdmi_table, $DM_WebURL;
	
	if ( empty( $wpdmi_settings['passthru_key'] ) || empty( $wpdmi_settings['dmwebpro_url'] ) )
		return;
		
	// Users cannot access Dietmaster unless they are logged in
	if ( ! is_user_logged_in() ) {
	
		//Redirect to access denied page if user is trying to access do pass-thru
		if ( is_page( $wpdmi_settings[ 'passthru_page' ] ) && $wpdmi_settings[ 'page_accessdenied' ] ) {
			wp_redirect( get_permalink( $wpdmi_settings[ 'page_accessdenied' ] ) );
			exit;
		}
		
		return;
	}	
	
	// See if the profile info is being pushed
	$do_profile_update = ( isset( $_POST[ 'wpdmi-profile-update-nonce'] ) || wp_verify_nonce( $_POST[ 'wpdmi-profile-update-nonce' ], 'wpdmi-profile-update' ) ) ? true : false;
	
	// Profile Pass-thru request
	if( $do_profile_update ) {
	
		// Display any form validation errors
		if( count( $wpdmi_notice ) > 0 )
			return;
		
		// Push user profile info to DM if no form validation error	
		do_action( 'wpdmi_process_profile' );
	}
	
	// Page Pass-thru request
	if ( is_page( $wpdmi_settings[ 'passthru_page' ] ) ) {
		
		// Profile method
		if ( $wpdmi_settings[ 'integration_method' ] == 'profile_passthru' ) {
			
			if ( null != $registered_member = wpdmi_user_exists() ) {
				
				$DM_Hash = md5( $registered_member[ 'user' ] . $registered_member[ 'pass' ] . $wpdmi_settings[ 'passthru_key' ] );
				wpdmi_user_login( $registered_member[ 'user' ], $registered_member[ 'pass' ], $DM_Hash );
				
			// Profile Pass-thru has not been run. Tell user to submit the profile form first
			} else {
				add_filter( 'the_content', 'wpdmi_content_error' );
			}
		
		// Pass-thru only method
		} else {
			if ( null != $registered_member = wpdmi_user_exists() ) {
				$DM_Hash = md5( $registered_member[ 'user' ] . $registered_member[ 'pass' ] . $wpdmi_settings[ 'passthru_key' ] );
				wpdmi_user_login( $registered_member[ 'user' ], $registered_member[ 'pass' ], $DM_Hash );
				
			// No DM member found. Do ReserveUser and login
			} else {
				wpdmi_process_reserveuser();
			}
		}
	}
}

function wpdmi_member_profile() {
	global $wpdb, $current_user;
	
	$table_name = $wpdb->prefix . "dietmaster";
	
	return $wpdb->get_row( "SELECT * FROM $table_name WHERE site_userid = $current_user->ID", ARRAY_A );
}
add_shortcode( 'dietmaster-integration-profile-form', 'wpdmi_profile_shortcode' );

function wpdmi_profile_shortcode() {
	global $wpdmi_settings;
	
	if( !is_user_logged_in() )
		return __( 'You must be logged in to view your profile form.', WPDMI_PLUGIN_TXT_DOMAIN );
	
	if( $wpdmi_settings[integration_method] == 'reserveuser_passthru' ) 
		return;
		
	global $current_user, $wpdb, $wpdmi_table, $user_profile;
	
	$usermeta = get_user_meta( $current_user->ID );
	$first_name = esc_attr( $usermeta['first_name'][0] );
	$last_name = esc_attr( $usermeta['last_name'][0] );
	
	wp_enqueue_style( 'wpdmi-styles', WPDMI_PLUGIN_URL . '/wpdmi.css' );
	wp_enqueue_script( 'wpdmi-scripts', WPDMI_PLUGIN_URL . '/js/wpdmi.functions.js', array( 'jquery' ) );
	wp_enqueue_script( 'wpdmi-maskedinput', WPDMI_PLUGIN_URL . '/js/jquery.maskedinput.js', array( 'jquery' ) );
	wp_enqueue_script( 'wpdmi-tooltipsy', WPDMI_PLUGIN_URL . '/js/tooltipsy.source.js', array( 'jquery' ) );
	
	ob_start();
		
	do_action( 'wpdmi_form_notice' );
	
	// Current user profile
	$user_profile = wpdmi_member_profile();
	
	// POST values kept on validation
	if( $user_profile ) {
		foreach( $user_profile as $key => $value ) {
			if( isset( $_POST[$key] ) ) {
				$user_profile[$key] = $_POST[$key];
			}
		}
	}
	
	require WPDMI_PLUGIN_DIR . '/includes/view-profile-form.php';
	
	return ob_get_clean();
}

add_action( 'wpdmi_yesno_fields', 'wpdmi_show_yesno_fields', 10, 2 );

function wpdmi_show_yesno_fields( $title, $name ) {
	global $user_profile;
	
	echo '<p><span class="wpdmi-form-label">' . __( $title, WPDMI_PLUGIN_TXT_DOMAIN) . '</span><span class="icon-required">*</span> <input type="radio" name="' . $name . '" class="wpdmi-form-input wpdmi-required" value="0" ' . ( ( $user_profile[ $name ] == '0' || empty($user_profile[ $name ]) ) ? 'checked=checked' : '' )  . ' /> ' . __( 'No', WPDMI_PLUGIN_TXT_DOMAIN). ' <input type="radio" name="' . $name . '" class="Yes" value="1" ' . ( ( $user_profile[ $name ] == '1' ) ? 'checked=checked' : '' )  . ' /> ' . __( 'Yes', WPDMI_PLUGIN_TXT_DOMAIN) . '</p>';
}

add_shortcode( 'dmi_if_profile', 'wpdmi_if_profile_exists' );

function wpdmi_if_profile_exists( $atts, $content = "" ) {
	
	if( !is_user_logged_in() )
		return;
	
	if ( wpdmi_member_profile() != null ) {
		// Found member profile. Display the content
		return do_shortcode( $content );
	} else {
		return;
	}
}

add_shortcode( 'dmi_no_profile', 'wpdmi_if_no_profile' );

function wpdmi_if_no_profile( $atts, $content = "" ) {
	
	if( !is_user_logged_in() )
		return;
	
	if ( wpdmi_member_profile() == null ) {
		// No member profile found. Display the content
		return do_shortcode( $content );
	} else {
		return;
	}
}

add_action ( 'wpdmi_form_notice', 'wpdmi_form_profile_notice' );

function wpdmi_form_profile_notice() {
	global $wpdmi_notice, $current_user;
		
	if( $wpdmi_notice ) {
		?>
		<ul class="wpmdi_notices<?php echo ( array_key_exists( 'update', $wpdmi_notice )  ) ? ' update' : ''; ?>">
		<?php
		
		foreach( $wpdmi_notice as $notice ) {
			echo '<li>' . $notice . '</li>';
		}
		
		echo '</ul>';
	}
	
}

add_action( 'wpdmi_init', 'wpdmi_validate_profile' );

function wpdmi_validate_profile() {
	if( !is_user_logged_in() )
		return;
			
	// Make sure it's a legitimate request
	if( isset( $_POST[ 'wpdmi-profile-update-nonce'] ) || wp_verify_nonce( $_POST[ 'wpdmi-profile-update-nonce' ], 'wpdmi-profile-update' ) ) {
		
		global $current_user, $wpdmi_notice;
		
		$wpdmi_notice = array();
		
		// Required fields
		$required_fields = array (
			'first-name'=>__('First Name'), 
			'last-name'=>__('Last Name'), 
			'gender'=>__('Gender'), 
			'general-units'=>__('General Units'), 
			'energy-unit'=>__('Energy Unit'), 
			'date-format'=>__('Date Format'), 
			'birthdate'=>__('Birth Date'), 
			'height-feet'=>__('Height'), 
			'weight'=>__('Weight'), 
			'bmr_calc_method'=>__('BMR Calc Method'), 
			'body_type'=>__('Body Type'), 
			'profession'=>__('Profession'), 
			'weight_goals'=>__('Weight Goals'), 
			'meal_type'=>__('Meal Type'), 
			'heart'=>__('Heart Disease'), 'liver'=>__('Liver Disease'), 'pancreatic'=>__('Pancreatic Disease'), 'anemia'=>__('Anemia'), 'kidney'=>__('kidney Disease'), 'hypoglycemia'=>__('Hypoglycemia'), 'diabetes'=>__('Diabetes'), 'hypertension'=>__('Hypertension'), 'h_heart'=>__('Heart Disease history'), 'h_breast_cancer'=>__('Breast Cancer History'), 'h_cancer_other'=>__('Other Cancer History'), 'h_liver'=>__('Liver Disease History'), 'h_stroke'=>__('Stroke History'), 'h_osteoporosis'=>__('Osteoporosis History'), 'h_hypoglycemia'=>__('Hypoglycemia History'), 'h_diabetes'=>__('Diabetes History'), 'h_hypertension'=>__('Hypertension History') );
		
		// Validate the form
		foreach( $required_fields as $key => $value ) {
			
			if( !isset( $_POST[$key] ) ) {
				$wpdmi_notice[] = "Value $value is not set";
			}
		}
		
		if( count($wpdmi_notice) > 0 ) // Require fields before further validation
			return;
		
		// Optional fields
		if( $_POST[ 'weight_goals' ] <> 1 ) {
			if( !isset( $_POST['goal_weight'] ) ) {
				$wpdmi_notice[] = __( "Goal weight is required", WPDMI_PLUGIN_TXT_DOMAIN);
			}
			if( !isset( $_POST['goal_rate'] ) ) {
				$wpdmi_notice[] = __( "Choose your goal rate", WPDMI_PLUGIN_TXT_DOMAIN);
			}
		}
		
		//echo '<pre> POST // ';
		//print_r($_POST);
		//echo '</pre>';
		//exit;
		
		// Validate Birth Date
		if( !isset( $_POST['birthdate'] ) || empty( trim ( $_POST['birthdate'] ) ) ) {
			$wpdmi_notice[] = __( "Birth Date is required", WPDMI_PLUGIN_TXT_DOMAIN);
		}
		
		// Validate Height Range
		$height = ( isset( $_POST[ 'height-feet' ] ) ) ? intval( $_POST[ 'height-feet' ] * 12 + $_POST[ 'height-inches' ] ) : 0;
		
		if( false == wpdmi_validate_range( $height, 12, 119 ) ) {
			$wpdmi_notice[] = __( "Height must be between 12 & 119 inches (1 foot = 12 inches)", WPDMI_PLUGIN_TXT_DOMAIN);
		}
		
		// Validate weight ranges
		if( $_POST[ 'general-units' ] == 1 ) { // International
			if( false == wpdmi_validate_range( intval ($_POST[ 'weight' ] ), 1, 450 ) ) {
				$wpdmi_notice[] = __( "Your weight must be between 1 and 450 kgs", WPDMI_PLUGIN_TXT_DOMAIN);
			}
		
		} else { // US
			if( false == wpdmi_validate_range( intval ($_POST[ 'weight' ] ), 1, 990 ) ) {
				$wpdmi_notice[] = __( "Your weight must be between 1 and 999 lbs", WPDMI_PLUGIN_TXT_DOMAIN);
			}
		}
		
		// RMR Calc validation
		switch( $_POST[ 'bmr_calc_method' ] ) {
			
			case 1: // RMR Device
				if( !$_POST[ 'rmr_value' ] )
					$wpdmi_notice[] = __( "RMR Value is required", WPDMI_PLUGIN_TXT_DOMAIN);
				
				break;
			
			case 2: // BMR Custom
				$bmr = intval ($_POST[ 'bmr' ] );
				
				if( $_POST[ 'general-units' ] == '0' && false == wpdmi_validate_range( $bmr, 1, 9999 ) ) { // U.S.
					$wpdmi_notice[] = __( "Your BMR value must be between 1 and 9999 for U.S. general units", WPDMI_PLUGIN_TXT_DOMAIN);
					
				} elseif( $_POST[ 'general-units' ] == '1' && false == wpdmi_validate_range( $bmr, 1, 41835 ) ) { // Int
					$wpdmi_notice[] = __( "Your BMR value must be between 1 and 41835 for International general units", WPDMI_PLUGIN_TXT_DOMAIN);
				}
				
				break;
		}
		
	}
}

add_action( 'wpdmi_process_profile', 'wpdmi_save_profile' );

function wpdmi_save_profile( ) {
	global $wpdb, $current_user, $wpdmi_notice, $wpdmi_table, $wpdmi_settings;
	
	// Conditional input fields
	$lactation = ( isset( $_POST[ 'lactation' ] ) && $_POST[ 'gender' ] == 1 ) ? 1 : 0;
	$height = ( isset( $_POST[ 'height-feet' ] ) ) ? intval( $_POST[ 'height-feet' ] * 12 + $_POST[ 'height-inches' ] ) : 0;
	$goal_rate = ( isset( $_POST[ 'goal_rate' ] ) ) ? $_POST[ 'goal_rate' ] : '1.00';
	
	$data = array(
		'general_units' => intval( $_POST[ 'general-units' ] ),
		'energy_unit' => intval( $_POST[ 'energy-unit' ] ),
		'date_format' => intval( $_POST[ 'date-format' ] ),
		'birthdate' => sanitize_text_field( $_POST[ 'birthdate' ] ),
		'gender' => intval( $_POST[ 'gender' ] ),
		'height' => $height,
		'weight' => intval( $_POST[ 'weight' ] ),
		'lactation' => $lactation,
		'bmr_calc_method' => intval( $_POST[ 'bmr_calc_method' ] ),
		'body_type' => intval( $_POST[ 'body_type' ] ),
		'profession' => intval( $_POST[ 'profession' ] ),
		'weight_goals' => intval( $_POST[ 'weight_goals' ] ),
		'meal_type' => intval( $_POST[ 'meal_type' ] ),
		'hide_templates' => intval( $_POST[ 'hide_templates' ] ),
		'heart' => intval( $_POST[ 'heart' ] ),
		'liver' => intval( $_POST[ 'liver' ] ),
		'pancreatic' => intval( $_POST[ 'pancreatic' ] ),
		'anemia' => intval( $_POST[ 'anemia' ] ),
		'kidney' => intval( $_POST[ 'kidney' ] ),
		'hypoglycemia' => intval( $_POST[ 'hypoglycemia' ] ),
		'diabetes' => intval( $_POST[ 'diabetes' ] ),
		'hypertension' => intval( $_POST[ 'hypertension' ] ),
		'h_heart' => intval( $_POST[ 'h_heart' ] ),
		'h_breast_cancer' => intval( $_POST[ 'h_breast_cancer' ] ),
		'h_cancer_other' => intval( $_POST[ 'h_cancer_other' ] ),
		'h_liver' => intval( $_POST[ 'h_liver' ] ),
		'h_stroke' => intval( $_POST[ 'h_stroke' ] ),
		'h_osteoporosis' => intval( $_POST[ 'h_osteoporosis' ] ),
		'h_hypoglycemia' => intval( $_POST[ 'h_hypoglycemia' ] ),
		'h_diabetes' => intval( $_POST[ 'h_diabetes' ] ),
		'h_hypertension' => intval( $_POST[ 'h_hypertension' ] )
	);
	
	// Optional fields
	if( $_POST[ 'weight_goals' ] <> 1 ) {
		$data['goal_weight'] = intval( $_POST[ 'goal_weight' ] );
		$data['goal_rate'] = $goal_rate;
	}
	if( $_POST[ 'bmr_calc_method' ] == '1' ) {
		$data['rmr_value'] = intval( $_POST[ 'rmr_value' ] );
		
	} elseif( $_POST[ 'bmr_calc_method' ] == '2' ) {
		$data['bmr'] = intval( $_POST[ 'bmr' ] );
	}
	
	if( !$wpdmi_notice ) {
		
		$is_new_member = false;
		$registered_member = wpdmi_user_exists();

		// Pre-existing member
		if ( $registered_member != null ) {
			$username = $registered_member[ 'user' ];
			$pass = $registered_member[ 'pass' ];
			$DM_Hash = md5( $username . $pass . $wpdmi_settings[ 'passthru_key' ] );
		
		// Insert new member
		} else {
			$is_new_member = true;
			$username = $current_user->data->user_email;
			$pass = wpdmi_generatePassword( 10, 4);
			$DM_Hash = md5( $username . $pass . $wpdmi_settings[ 'passthru_key' ] );
			wpdmi_add_new_user( $pass, $DM_Hash ); // We don't know the DM user_id and mobiletocken yet
		}
		
		// Update table with profile info
		$where = array( 'site_userid' => $current_user->ID );
		$wpdb->update( $wpdmi_table, $data, $where );
		
		// Update name
		update_user_meta( $current_user->ID, 'first_name', $_POST[ 'first-name' ] );
		update_user_meta( $current_user->ID, 'last_name', $_POST[ 'last-name' ] );
		
		// Profile Pass-thru
		do_action( 'wpdmi_profile_passthru', $is_new_member );
		
		$wpdmi_notice = array();
		$wpdmi_notice['update'] = __( 'Your profile was saved successfully!', WPDMI_PLUGIN_TXT_DOMAIN );
	}
	
	// Do login pass-thru unless we are just saving the profile form
	if( !isset( $_POST[ 'just-save' ] ) ) {
		wpdmi_user_login( $username, $pass, $DM_Hash );
	}
}

add_action('wpdmi_profile_passthru', 'wpdmi_process_profile_passthru', 10, 2);

/*
* Processes the new and existing member via a passthru call
* $is_new_member boolean - true will send a welcome email
* $update_expiration boolean - true will set Dietmaster account expiration to today's date
* $user_id integer - targeted WordPress user ID
*/
function wpdmi_process_profile_passthru( $is_new_member = false, $update_expiration = false, $user_id = '', $uncancel = false ) {

	global $current_user, $wpdmi_settings, $wpdb, $wpdmi_table, $DM_WebURL;
	
	$uid = ( $user_id ) ? $user_id : $current_user->ID;
	
	$result = $wpdb->get_row( "SELECT * FROM $wpdmi_table WHERE site_userid = $uid", ARRAY_A );
	
	if ( $result != null ) {

		$postdata['returnUserID']= 1;
		$postdata['returnToken']= 1;
		$postdata['Username']= $result[ 'user' ];
		$postdata['Password']= $result[ 'pass' ];
		$postdata['Hash']= md5( $result[ 'user' ] . $result[ 'pass' ] . $wpdmi_settings[ 'passthru_key' ] );
		$postdata['FirstName']= get_user_meta( $current_user->ID, 'first_name', true );
		$postdata['LastName']= get_user_meta( $current_user->ID, 'last_name', true );
		$postdata['Email']= $result['email'];
		$postdata['GeneralUnits']= $result['general_units'];
		$postdata['EnergyUnit']= $result['energy_unit'];
		$postdata['DateFormat']= $result['date_format'];
		$postdata['BirthDate']= $result['birthdate'];
		$postdata['Gender']= $result['gender'];
		$postdata['Height']= $result['height'];
		$postdata['Weight']= $result['weight'];
		$postdata['Lactation']= $result['lactation'];
		$postdata['BMRCalcMethod']= $result['bmr_calc_method'];
		$postdata['RMRValue']= $result['rmr_value'];
		$postdata['BMR']= $result['bmr'];
		$postdata['BodyType']= $result['body_type'];
		$postdata['Profession']= $result['profession'];
		$postdata['WeightGoals']= $result['weight_goals'];
		$postdata['GoalWeight']= $result['goal_weight'];
		$postdata['GoalRate']= $result['goal_rate'];
		$postdata['MealTypeID']= $result['meal_type'];
		$postdata['HideTemplates']= $result['hide_templates'];
		$postdata['HeartDisease']= $result['heart'];
		$postdata['LiverDisease']= $result['liver'];
		$postdata['PancreaticDisease']= $result['pancreatic'];
		$postdata['Anemia']= $result['anemia'];
		$postdata['KidneyDisease']= $result['kidney'];
		$postdata['Hypoglycemia']= $result['hypoglycemia'];
		$postdata['Diabetes']= $result['diabetes'];
		$postdata['Hypertension']= $result['hypertension'];
		$postdata['HistHeartDisease']= $result['h_heart'];
		$postdata['HistBreastCancer']= $result['h_breast_cancer'];
		$postdata['HistCancerOther']= $result['h_cancer_other'];
		$postdata['HistLiverDisease']= $result['h_liver'];
		$postdata['HistStroke']= $result['h_stroke'];
		$postdata['HistOsteoporosis']= $result['h_osteoporosis'];
		$postdata['HistHypoglycemia']= $result['h_hypoglycemia'];
		$postdata['HistDiabetes']= $result['h_diabetes'];
		$postdata['HistHypertension']= $result['h_hypertension'];
		
		if( $update_expiration ) {
			if( $uncancel ) {
				$postdata['ExpirationDate']= '01-01-2099'; // TODO reflect actual expiration date - handle empty
			} else {
				$postdata['ExpirationDate']= date( 'Y-m-d', time() );
			}
		}

		$output = wpdmi_HTTP_Post($DM_WebURL . "/ProfilePassThru.aspx", $postdata);

		//exit($output);
		$newoutputArray = wpdmi_format_response( $output );
		$newUserID	= $newoutputArray[0];
		$newMobileToken	= substr($newoutputArray[1],0,10);
		
		if( $newMobileToken ) {
			
			// Update plugin table with new mobiletoken and user_id
			$data = array(
			'dm_userid' => intval( $newUserID ),
			'mobile_token' => $newMobileToken
			);
			$where = array( 'site_userid' => $current_user->ID );
			$wpdb->update( $wpdmi_table, $data, $where );
			
			if ( $is_new_member )
				wpdmi_compose( $newMobileToken, $DM_WebURL, $result[ 'user' ], $result[ 'pass' ] );
		
			return true;
			
		} else {
			//Handle errors
			$wpdmi_notice['error'] = __( 'Error passing through your profile', WPDMI_PLUGIN_TXT_DOMAIN );
			
			return false;
		}
		
		//$newoutputArray = wpdmi_format_response( $output );
		//$newUserID= $newoutputArray[0];
		//$newMobileToken= substr($newoutputArray[1],0,10);
	}
	
}

/**
 * Password Generator
 * By Drew Moffitt - drew [at] aestudiosdev.com
 */
function wpdmi_generatePassword($length=8, $strength=4) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}

	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

/**
 * POST to Dietmaster ASP server
 * By Drew Moffitt - drew [at] aestudiosdev.com
 */
function wpdmi_HTTP_Post($URL, $data, $referrer="") {

	// parsing the given URL
	$URL_Info=parse_url($URL);

	// Building referrer
	if($referrer=="") // if not given use this script as referrer
	$referrer=$_SERVER["SCRIPT_URI"];

	// making string from $data
	foreach($data as $key=>$value)
	$values[]="$key=".urlencode($value);
	$data_string=implode("&",$values);

	// Find out which port is needed - if not given use standard (=80)
	if(!isset($URL_Info["port"]))
	$URL_Info["port"]=80;

	// building POST-request:
	$request.="POST ".$URL_Info["path"]." HTTP/1.1\n";
	$request.="Host: ".$URL_Info["host"]."\n";
	$request.="Content-type: application/x-www-form-urlencoded\n";
	$request.="Content-length: ".strlen($data_string)."\n";
	$request.="Connection: close\n";
	$request.="\n";
	$request.=$data_string."\n";

	$fp = fsockopen($URL_Info["host"],$URL_Info["port"]);
	fputs($fp, $request);
	while(!feof($fp)) {
		$result .= fgets($fp, 128);
	}
	fclose($fp);

	return $result;
}

/**
 * Format the HTTP Post to read the result
 * By Drew Moffitt - drew [at] aestudiosdev.com
 */
function wpdmi_format_response( $output ) {

	$newoutput = explode("Connection: close", $output);
	//$output_len = strlen($newoutput[1]);
	
	$newoutput = trim($newoutput[1]);
	$newoutput = explode("<!", $newoutput);
	$newoutput = trim($newoutput[0]);
	
	//Explode output on pipe
	$newoutput = explode("|",$newoutput);
	
	return $newoutput;
}

/**
 * Validate a number between a given range
 * @param int|double $num The number to validate
 * @param int|double $min The lower boundary of the range
 * @param int|double $max The upper boundary of the range
 * @param boolean $inclusive Whether to include the boundary values as valid
 * @return boolean
 * By Drew Moffitt - drew [at] aestudiosdev.com
 */
function wpdmi_validate_range($num, $min, $max = PHP_INT_MAX, $inclusive = true) {

    if ($inclusive) {
       return $num >= $min && $num <= $max;
    }

    return $num > $min && $num < $max;
}

function wpdmi_compose( $newMobileToken, $url, $username, $pass ) {
	
	global $current_user;
	
	$to = $current_user->data->user_email;
	
	$subject = __( 'Your Account is Ready!', WPDMI_PLUGIN_TXT_DOMAIN ); //TODO admin can edit this
	
	$email_template = get_option( 'wpdmi_token_email_text' );
	
	$message = str_replace( '[DM_URL]', $url, $email_template );
	$message = str_replace( '[DM_USERNAME]', $username, $message );
	$message = str_replace( '[DM_PASSWORD]', $pass, $message );
	$message = str_replace( '[DM_MOBILE_TOKEN]', $newMobileToken, $message );
	$message = str_replace( '[DM_SITE_NAME]', get_bloginfo( 'name' ), $message );
	
	$headers = "From: " . get_bloginfo( 'admin_email' ) . "\n";
	
	return @wp_mail( $to, $subject, $message, $headers );
}

function wpdmi_add_new_user( $pass, $hash, $newUserID = 0, $newMobileToken = '' ) {
	global $wpdb, $wpdmi_table, $current_user, $DM_WebURL;
	
	//echo '<h1>Successful Setup!</h1>';
	//echo '<h2>User: '.$newUserID . "<br>Mobile Token: " . $newMobileToken . '</h2>';
	
	$wpdb->insert ( $wpdmi_table, array(
		'site_userid' => $current_user->ID,
		'ip_address' => $_SERVER['REMOTE_ADDR'],
		'email' => $current_user->data->user_email,
		'posted' => $current_user->data->user_registered,
		'user' => $current_user->data->user_email,
		'pass' => $pass,
		'hash' => $hash,
		'dm_userid' => $newUserID,
		'mobile_token' => $newMobileToken,
		'active' => 1
	),
	array( '%d','%s','%s','%s','%s','%s','%s','%d','%s','%d')
	);
					
	if ( $newMobileToken ) {
	
		// Store the mobile token into user meta
		update_user_meta( $current_user->ID, 'wpdmi_mobiletoken', $newMobileToken );
	
		// Email the current user about account creation and mobile token
		wpdmi_compose( $newMobileToken, $DM_WebURL, $newUserID, $pass );
	}
}

/**
 * Check if the current logged in user is already registered into the plugin table
 * @return result array or null
 */
function wpdmi_user_exists() {
	global $wpdb, $wpdmi_table, $current_user;
	
	return $wpdb->get_row( "SELECT * FROM $wpdmi_table WHERE site_userid = $current_user->ID", ARRAY_A );
}

/**
 * Dietmaster Login Pass-thru
 * @return none
 */
function wpdmi_user_login( $username, $pass, $hash ) {
	
	global $DM_WebURL, $wpdmi_settings;
	
	if( $wpdmi_settings[ 'detect_mobile' ] == '1' && wpdmi_mobile_detected() === true ) {
		//header( "Location: http://google.com" ); create a cushion
		$_POST['confirm_mobile']  = true;
		$_POST['wpdmi_username']  = $username;
		$_POST['wpdmi_pass']  = $pass;
		$_POST['wpdmi_hash']  = $hash;
		return;
	}
	
	$ptURL = $DM_WebURL . "/UserLogin.aspx?Username=" . $username . "&Password=" . $pass . "&Hash=" . $hash;
	
	//echo '<iframe src="' . $ptURL . '" width="400" height="900">';
	header( "Location: $ptURL" );
	exit;
	
}

function wpdmi_process_reserveuser() {
	global $current_user, $DM_WebURL, $wpdmi_settings;
	
	$usermeta = get_user_meta( $current_user->ID );
	$contents = file_get_contents( $DM_WebURL . "/ReserveUserPassThru.aspx" );

	// Set Viewstate
	$start = strpos( $contents, 'id="__VIEWSTATE" value="' );
	$mac_viewstate = substr( $contents, $start + 24 );
	$mac_viewstate = split( '"', $mac_viewstate );
	$strViewState = $mac_viewstate[0];
	$strPassword = wpdmi_generatePassword(10,4);

	$DM_Hash = md5( $current_user->data->user_email . $strPassword . $wpdmi_settings[ 'passthru_key' ] );

	$postdata 					= array();
	$postdata['Username']		= $current_user->data->user_email;
	$postdata['Password']		= $strPassword;
	$postdata['Hash']			= $DM_Hash;
	$postdata['FirstName']		= $usermeta['first_name'][0];
	$postdata['LastName']		= $usermeta['last_name'][0];
	$postdata['__VIEWSTATE']	= $strViewState;

	// Profile Information
	$postdata['Email']			= $current_user->data->user_email;

	// Default Settings
	$postdata['returnUserID']	= 1;
	$postdata['returnToken']	= 1;
	$postdata['HideTemplates']	= 0;

	$output	= wpdmi_HTTP_Post($DM_WebURL . "/ReserveUserPassThru.aspx", $postdata);

	//exit( $output );

	$newoutputArray = wpdmi_format_response( $output );

	$newUserID	= $newoutputArray[0];
	$newMobileToken	= substr($newoutputArray[1],0,10);

	//Insert current user as existing DM member
	if( is_numeric($newUserID) && $newMobileToken ) {
		
		// Add new user to plugin table
		wpdmi_add_new_user( $strPassword, $DM_Hash, $newUserID, $newMobileToken );

		wpdmi_user_login( $current_user->data->user_email, $strPassword, $DM_Hash );

	} else { // Error Handling: User could not be created
		
		if( count( $wpdmi_notice ) > 0 )
			return;
		
		echo "<script>alert('New user could not be created! ($newoutputArray[0])')</script>";
		
		if($newoutputArray[0] == 'Duplicate Username') {
			// do something
			return;
		}
		
		if($newoutputArray[0] == 'Invalid Hash') {
			// do something
			return;
		}
		
		// TODO: Notify the admin?
		
	} // DM new member creation
}

function wpdmi_content_error( $content ) { 
	return '<h5 class="error message">' . __('Please fill out your profile registration form first.', WPDMI_PLUGIN_TXT_DOMAIN ) . '</h5>' . $content;
}

function wpdmi_get_meal_plans() {
	global $wpdmi_settings;
	$meal_plans = array();
	
	// Format meal plan information into a usable array to loop through
	if( $wpdmi_settings[ 'mealplan_id' ] && $wpdmi_settings[ 'mealplan_name' ] ) {
		$count = count( $wpdmi_settings[ 'mealplan_id' ] );
		for( $i = 0; $i <= $count; $i++ ) {
			if( $wpdmi_settings[ 'mealplan_id' ][$i] ) {
				$meal_plans[$i]['mealplan_id'] = $wpdmi_settings[ 'mealplan_id' ][$i];
				$meal_plans[$i]['mealplan_name'] = $wpdmi_settings[ 'mealplan_name' ][$i];
				$meal_plans[$i]['mealplan_active'] = ( isset($wpdmi_settings[ 'mealplan_active' ]) && in_array($wpdmi_settings[ 'mealplan_id' ][$i], $wpdmi_settings[ 'mealplan_active' ] ) ) ? true : false;
			}
		}
	}
	
	return $meal_plans;
}

add_action( 'wpdmi_init', 'wpdmi_expiration_check' );

function wpdmi_expiration_check() {
	if( class_exists( 'WishListMemberCore' ) ) {
		// Set expiration date
		add_action( 'wishlistmember_cancel_user_levels', 'wpdmi_expiration_passthru', 99, 2 );
		
		// Remove expiration date
		add_action( 'wishlistmember_uncancel_user_levels', 'wpdmi_expiration_passthru', 99, 2 );
	}
}

function wpdmi_expiration_passthru( $id, $levels ) {
	global $wpdmi_settings;
	$do_passthru = false;
	$uncancel = false;
	
	// Walk through the cancelled levels and see if we need to do expiration passthru
	foreach( $levels as $level ) {
		if( in_array( $level, $wpdmi_settings['membership_levels'] ) ) {
			// yes we need to do expiration_pass_thru
			$do_passthru = true;
		}
	}
	
	if( $do_passthru ) {
		
		// Set the expiration date to today's date though pass-thru
		$is_new_member = false;
		$update_expiration = true;
		$user_id = $id;
	
		if( current_filter() == 'wishlistmember_uncancel_user_levels' ) {
			$uncancel = true;
		}
		
		wpdmi_process_profile_passthru( $is_new_member, $update_expiration, $user_id, $uncancel );
	}
	
}

/*
* Based from detectmobilebrowser.com
*/
function wpdmi_mobile_detected() {
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	
	if( preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)) ) 
	{
		return true; // detected
	} else {
		return false;
	}
	
}

function wpdmi_get_all_wlm_levels() {
	$response = WishListMemberAPIRequest( '/levels', 'GET' );
	$levels = array();
	
	if( $response['success'] == 1) {
		foreach( $response['levels']['level'] as $level ) {
			$levels[] = array( 'level_id' => $level['id'], 'name' => $level['name'] );
		}
	}
	
	if( $levels )
		return $levels;
}