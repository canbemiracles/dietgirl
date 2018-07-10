<?php 
/**
 * For Advance HTML coders to customize the DietMaster profile form
 *
 * @package DietMaster-Integration
 */
global $DM_WebURL;
 
 // Height conversion
 if( $user_profile[ 'general_units' ] == '0' && $user_profile[ 'height' ] ){
	
	$wpdmi_height_feet = floor( $user_profile[ 'height' ] / 12 );
	$wpdmi_height_inches = fmod( $user_profile[ 'height' ], 12 );
	 
 } elseif( $_POST[ 'height-feet' ] && $_POST[ 'height-inches' ] ){
	
	$wpdmi_height_feet = $_POST[ 'height-feet' ];
	$wpdmi_height_inches = $_POST[ 'height-inches' ];
	 
 }

/*echo 'height-feet :: ';
echo $wpdmi_height_feet;
echo '<br>';
echo 'height-inches :: ';
echo $wpdmi_height_inches;*/

if( isset( $_POST['confirm_mobile'] ) && $_POST['confirm_mobile'] === true ) {
?>
<h3 class="mobile_alert"><?php _e( 'Download Our Mobile App', WPDMI_PLUGIN_TXT_DOMAIN); ?></h3>
<p><?php _e( 'There is a mobile app of our nutrition software called "Pro Diets" that works better on your mobile. Would you like to use it instead? Your mobile app login is found on your account activation email.', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>
<p class="wpdmi-text-center"><a href="https://itunes.apple.com/us/app/dietmaster-go/id507614737?mt=8" class="button">Click Here for iOS (iPhone)</a></p>
<p class="wpdmi-text-center"><a href="https://play.google.com/store/apps/details?id=com.dietmaster.go&hl=en" target="_blank" class="button">Click Here for Android</a></p>

<p class="wpdmi-text-center" style="padding-top:10px;"><a href="#" onclick="document.location.href ='<?php echo $DM_WebURL . "/UserLogin.aspx?Username=" . $_POST['wpdmi_username'] . "&Password=" . $_POST['wpdmi_pass'] . "&Hash=" . $_POST['wpdmi_hash']; ?>';">No thanks, continue &#0187;</a></p>
<?php 
} else {
?>

<form action="" method="post" id="wpdmi-profile-form" class="uk-form">
<div style="display:none;">
	<?php wp_nonce_field( 'wpdmi-profile-update', 'wpdmi-profile-update-nonce' ); ?>
</div>

<p class="wpdmi-instruction"><?php _e( '* Required Fields.', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<h3><?php _e( 'My Profile', WPDMI_PLUGIN_TXT_DOMAIN); ?></h3>

<fieldset>
<p><span class="wpdmi-form-label"><?php _e( 'First Name', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> 
	<?php if($first_name): ?><?php echo $first_name; ?>
	<input type="hidden" name="first-name" value="<?php esc_attr_e( $first_name ); ?>" />
	<?php else: ?>
	<input type="text" name="first-name" size="40" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $first_name ); ?>" />
	<?php endif; ?>
</p>

<p><span class="wpdmi-form-label"><?php _e( 'Last Name', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> 
	<?php if($last_name): ?><?php echo $last_name; ?>
	<input type="hidden" name="last-name" value="<?php esc_attr_e( $last_name ); ?>" />
	<?php else: ?>
	<input type="text" name="last-name" size="40" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $last_name ); ?>" />
	<?php endif; ?>
</p>

<p><span class="wpdmi-form-label"><?php _e( 'Gender', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> 
	<?php
	
	$wpdmi_genders = array( __( 'Male', WPDMI_PLUGIN_TXT_DOMAIN), __( 'Female', WPDMI_PLUGIN_TXT_DOMAIN) );
	
	if( isset($user_profile[ 'gender' ]) ): ?>
	<?php echo $wpdmi_genders[ $user_profile[ 'gender' ] ]; ?>
	<input type="hidden" name="gender" value="<?php esc_attr_e( $user_profile[ 'gender' ] ); ?>" />
	<?php elseif( isset($_POST[ 'gender' ]) ): ?>
	<input type="radio" name="gender" class="wpdmi-form-input wpdmi-required" value="0" <?php  echo ( ( $_POST[ 'gender' ] == '0' ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'Male', WPDMI_PLUGIN_TXT_DOMAIN); ?> <input type="radio" name="gender" class="wpdmi-form-input wpdmi-required" value="1" <?php  echo ( ( $_POST[ 'gender' ] == '1' ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'Female', WPDMI_PLUGIN_TXT_DOMAIN); ?>
	<?php else: ?>
	<input type="radio" name="gender" class="wpdmi-form-input wpdmi-required" value="0" <?php  echo ( ( $user_profile[ 'gender' ] == '0' ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'Male', WPDMI_PLUGIN_TXT_DOMAIN); ?> <input type="radio" name="gender" class="wpdmi-form-input wpdmi-required" value="1" <?php  echo ( ( $user_profile[ 'gender' ] == '1' ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'Female', WPDMI_PLUGIN_TXT_DOMAIN); ?>
	<?php endif; ?>
</p>

<p><input type="checkbox" name="lactation" class="wpdmi-form-input wpdmi-required" value="1" <?php  echo ( ( $user_profile[ 'lactation' ] == '1' ) ? 'checked=checked' : '' ); ?> /> <span class="wpdmi-form-label"><?php _e( 'If female, are you lactating or pregnant?', WPDMI_PLUGIN_TXT_DOMAIN); ?></span></p>

<p><span class="wpdmi-form-label"><?php _e( 'General Units', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> <input type="radio" name="general-units" class="wpdmi-form-input wpdmi-required" value="0" <?php  echo ( ( $user_profile[ 'general_units' ] == '0' || empty($user_profile[ 'general_units' ]) ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'U.S.', WPDMI_PLUGIN_TXT_DOMAIN); ?> <input type="radio" name="general-units" class="wpdmi-form-input wpdmi-required" value="1" <?php  echo ( ( $user_profile[ 'general_units' ] == '1' ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'International', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p><span class="wpdmi-form-label"><?php _e( 'Energy Unit', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> <input type="radio" name="energy-unit" class="wpdmi-form-input wpdmi-required" value="0" <?php  echo ( ( $user_profile[ 'energy_unit' ] == '0' || empty($user_profile[ 'energy_unit' ]) ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'U.S. (Calories)', WPDMI_PLUGIN_TXT_DOMAIN); ?> <input type="radio" name="energy-unit" class="wpdmi-form-input wpdmi-required" value="1" <?php  echo ( ( $user_profile[ 'energy_unit' ] == '1' ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'International (Kilojoules)', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p><span class="wpdmi-form-label"><?php _e( 'Date Format', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> <input type="radio" name="date-format" class="wpdmi-form-input wpdmi-required" value="0" <?php  echo ( ( $user_profile[ 'date_format' ] == '0' || empty($user_profile[ 'date_format' ]) ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'U.S.', WPDMI_PLUGIN_TXT_DOMAIN); ?> <input type="radio" name="date-format" class="wpdmi-form-input wpdmi-required" value="1" <?php  echo ( ( $user_profile[ 'date_format' ] == '1' ) ? 'checked=checked' : '' ); ?> /> <?php _e( 'International', WPDMI_PLUGIN_TXT_DOMAIN); ?><br /><small><?php _e( 'US format: mm/dd/yyyy<br />International format: dd/mm/yyyy' ); ?></small></p>

<p id="wpdmi-birthdate-row"><span class="wpdmi-form-label"><?php _e( 'Birth Date', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> 
	<?php if($user_profile[ 'birthdate' ]): ?><?php echo $user_profile[ 'birthdate' ] ?>
	<input type="hidden" name="birthdate" value="<?php esc_attr_e( $user_profile[ 'birthdate' ] ); ?>" />
	<?php elseif($_POST[ 'birthdate' ]): ?>
	<input type="text" name="birthdate" size="10" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $_POST[ 'birthdate' ] ); ?>" /> <span class="input-format"><small>mm/dd/yyyy</small></span>
	<?php else: ?>
	<input type="text" name="birthdate" size="10" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $user_profile[ 'birthdate' ] ); ?>" /> <span class="input-format"><small>mm/dd/yyyy</small></span>
	<?php endif; ?>
</p>

<p id="wpdmi-height-row"><span class="wpdmi-form-label"><?php _e( 'Height', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> <input type="text" name="height-feet" size="2" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $wpdmi_height_feet ); ?>" /> <?php _e( 'feet', WPDMI_PLUGIN_TXT_DOMAIN); ?> <input type="text" name="height-inches" size="2" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $wpdmi_height_inches ); ?>" /> <?php _e( 'inches', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p id="wpdmi-weight-row"><span class="wpdmi-form-label"><?php _e( 'Current Weight', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> <input type="text" name="weight" size="4" class="wpdmi-form-input wpdmi-required" value="<?php !empty( $user_profile[ 'weight' ] ) ? esc_attr_e( $user_profile[ 'weight' ] ) : esc_attr_e( $_POST[ 'weight' ] ); ?>" /> <span class="input-format"><small>lbs</small></span></p>
</fieldset>

<div class="uk-hidden">

<h3><?php _e( 'RMR Calculation', WPDMI_PLUGIN_TXT_DOMAIN); ?></h3>

<fieldset>
<p class="wpdmi-instruction"><?php _e( 'What is RMR? It is the amount of daily calories your body burns in a resting state.', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p><span class="wpdmi-form-label"><?php _e( 'BMR Calc Method', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span>
	<select name="bmr_calc_method" class="wpdmi-form-input wpdmi-required">
	<?php
	$options = array('0' => 'Default - Mifflin St. Joer method',
		'1' => 'RMR Device',
		'2' => 'Custom - manual entry'
	);
	foreach( $options as $key => $value ):
	?>
	<option value="<?php echo $key;?>"<?php echo ( $user_profile[ 'bmr_calc_method' ] == $key) ? ' selected="selected"' : '' ?>><?php _e( $value, WPDMI_PLUGIN_TXT_DOMAIN); ?></option>	
	<?php endforeach; ?>
	</select>
	<span class="hastip" title="<?php _e( '&lt;strong&gt;Default Calculation&lt;/strong&gt; - Use this option if you are not using a testing device.&lt;br /&gt;&lt;br /&gt;&lt;strong&gt;RMR Testing Device&lt;/strong&gt; - Use this option if you are using a device that measures resting metabolic rate.&lt;br /&gt;&lt;br /&gt;&lt;strong&gt;BMR&lt;/strong&gt; - Use this option if you are a Licensed Nutritional Consultant. This option will override the applications internal formulation used to calculate RMR and deliver a final BMR daily caloric intake goal as displayed on the Profile Summary page.', WPDMI_PLUGIN_TXT_DOMAIN); ?>"></span>
</p>

<p id="bmr_option_1" class="hidden_option" style="display:none"><span class="wpdmi-form-label"><?php _e( 'RMR Value', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> <input type="text" name="rmr_value" size="4" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $user_profile[ 'rmr_value' ] ); ?>" /></p>

<p id="bmr_option_2" class="hidden_option" style="display:none"><span class="wpdmi-form-label"><?php _e( 'BMR', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span> <input type="text" name="bmr" size="4" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $user_profile[ 'bmr' ] ); ?>" /></p>
</fieldset>

</div>

<h3><?php _e( 'Body Type & Professional Activity', WPDMI_PLUGIN_TXT_DOMAIN); ?></h3>

<fieldset>
<p class="wpdmi-instruction"><?php _e( 'Please review the following types (Mouseover the icon). Which best describes you?', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p><span class="wpdmi-form-label"><?php _e( 'Body Type', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span>
	<select name="body_type" class="wpdmi-form-input wpdmi-required">
	<?php
	$options = array('0' => 'Type I',
		'1' => 'Type II',
		'2' => 'Type III'
	);
	foreach( $options as $key => $value ):
	?>
	<option value="<?php echo $key;?>"<?php echo ( $user_profile[ 'body_type' ] == $key) ? ' selected="selected"' : '' ?>><?php _e( $value, WPDMI_PLUGIN_TXT_DOMAIN); ?></option>	
	<?php endforeach; ?>
	</select> <span class="hastip" title="<?php _e( '&lt;strong&gt;Type I&lt;/strong&gt; - I can eat anything I want and not gain weight. I have a very hard time gaining weight.&lt;br /&gt;&lt;br /&gt;&lt;strong&gt;Type II&lt;/strong&gt; - I can lose or gain weight by adjusting my activity level and eating habits.&lt;br /&gt;&lt;br /&gt;&lt;strong&gt;Type III&lt;/strong&gt; - I find it very hard to lose weight. I gain weight very easily and have to watch everything I eat.', WPDMI_PLUGIN_TXT_DOMAIN); ?>"></span>
</p>

<p class="wpdmi-instruction"><?php _e( 'Accurately rate your professional activity level.', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p><span class="wpdmi-form-label"><?php _e( 'Your Profession', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span>
	<select name="profession" class="wpdmi-form-input wpdmi-required">
	<?php
	$options = array('0' => 'Sedentary',
		'1' => 'Moderate',
		'2' => 'Active',
		'3' => 'Very Active'
	);
	foreach( $options as $key => $value ):
	?>
	<option value="<?php echo $key;?>"<?php echo ( $user_profile[ 'profession' ] == $key) ? ' selected="selected"' : '' ?>><?php _e( $value, WPDMI_PLUGIN_TXT_DOMAIN); ?></option>	
	<?php endforeach; ?>
	</select> <span class="hastip" title="<?php _e( 'If your goal is weight loss, for best results select Sedentary. It\'s very important to select the correct option that fits closest to your activity level while working. For example, if you are a carpenter, you are in a profession that requires physical activity during the workday and therefore the &lt;strong&gt;ACTIVE&lt;/strong&gt; option would be selected. If you work in an office and sit most of the day, you would be considered &lt;strong&gt;SEDENTARY&lt;/strong&gt; and that option should be selected. If you are a grocery clerk or auto mechanic then the &lt;strong&gt;MODERATELY ACTIVE&lt;/strong&gt; option should be selected. If the you perform any type of full time manual labor, then the &lt;strong&gt;VERY ACTIVE&lt;/strong&gt; option should be selected.', WPDMI_PLUGIN_TXT_DOMAIN); ?>"></span>
</p>
</fieldset>

<h3><?php _e( 'Set Goals', WPDMI_PLUGIN_TXT_DOMAIN); ?></h3>

<fieldset>
<p class="wpdmi-instruction"><?php _e( 'What are your personal health & fitness goals?', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p><span class="wpdmi-form-label"><?php _e( 'Weight Goals', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span>
	<select name="weight_goals" class="wpdmi-form-input wpdmi-required">
	<?php
	$options = array('0' => 'Weight Loss',
		'1' => 'Maintain Current Weight',
		'2' => 'Gain Weight'
	);
	foreach( $options as $key => $value ):
	?>
	<option value="<?php echo $key;?>"<?php echo ( $user_profile[ 'weight_goals' ] == $key) ? ' selected="selected"' : '' ?>><?php _e( $value, WPDMI_PLUGIN_TXT_DOMAIN); ?></option>	
	<?php endforeach; ?>
	</select> <span class="hastip" title="<?php _e( '&lt;strong&gt;Weight Loss&lt;/strong&gt; - Designed to decrease body fat with minimal loss of lean body tissue.)&lt;br /&gt;&lt;br /&gt;&lt;strong&gt;Maintain&lt;/strong&gt; - Designed to maintain current body composition and develop good eating habits.)&lt;br /&gt;&lt;br /&gt;&lt;strong&gt;Weight Gain&lt;strong&gt; - Designed to increase lean body mass with minimal increase in body fat.)', WPDMI_PLUGIN_TXT_DOMAIN); ?>"></span>
</p>

<div id="wpdmi-set-goals" style="display:none;">
<p class="wpdmi-instruction"><?php _e( 'If you selected Weight Loss or Weight Gain above, please provide the Goal Weight and Goal Rate:', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p id="wpdmi-weight-goal-row"><span class="wpdmi-form-label"><?php _e( 'Goal Weight', WPDMI_PLUGIN_TXT_DOMAIN); ?></span> <input type="text" name="goal_weight" size="4" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $user_profile[ 'goal_weight' ] ); ?>" /> <span class="input-format"><small>lbs</small></span></p>

<p id="wpdmi-goal-rate-row"><span class="wpdmi-form-label"><?php _e( 'Goal Rate', WPDMI_PLUGIN_TXT_DOMAIN); ?></span>
<?php

	// Drew
	if(empty($user_profile[ 'goal_rate' ])) {
		$user_profile[ 'goal_rate' ]	=	'1.00';
	}
	
?>
<select name="goal_rate" class="wpdmi-form-input wpdmi-required" value="<?php esc_attr_e( $user_profile[ 'goal_rate' ] ); ?>">
	<?php
	
	$options = array('.25' => '.25',
		'.50' => '.50',
		'.75' => '.75',
		'1.00' => '1.00',
		'1.25' => '1.25',
		'1.50' => '1.50',
		'1.75' => '1.75',
		'2.00' => '2.00'
	);
	
	foreach( $options as $key => $value ):
	?>
	<option value="<?php echo $key;?>"<?php echo ( $user_profile[ 'goal_rate' ] == $key) ? ' selected="selected"' : '' ?>><?php _e( $value, WPDMI_PLUGIN_TXT_DOMAIN); ?></option>	
	<?php endforeach; ?>
</select> <span class="input-format"><small>lbs/week</small></span></p>
</div>
</fieldset>

<h3><?php _e( 'Meal Type', WPDMI_PLUGIN_TXT_DOMAIN); ?></h3>

<fieldset>
<p class="wpdmi-instruction"><?php _e( 'Select one meal type from the list below', WPDMI_PLUGIN_TXT_DOMAIN); ?></p>

<p><span class="wpdmi-form-label"><?php _e( 'Meal Type', WPDMI_PLUGIN_TXT_DOMAIN); ?><span class="icon-required">*</span></span>

<?php
	
	$meal_plans 			=	wpdmi_get_meal_plans();
	$mealTypeCategoriesArr	=	array();
	$mealTypesArr			=	array();
	
	$API_Endpoint	=	"https://webservice.dmwebpro.com/DMWebProWS.asmx/GetUserMealTypes";
	
	// setting the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// turning off the server and peer verification(TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	// NVPRequest for submitting to server
	$nvpreq = "CompanyUsername=adietgir4&CompanyPassword=u3emaruv";

	// setting the nvpreq as POST FIELD to curl
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	
	// getting response from server
	$httpResponse = curl_exec($ch);
	
	// Success
	if(empty($httpResponse)) {
		//echo 'FAIL';
		//exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	} else {
		
		// Valid Response
		if(stristr($httpResponse,'"MealTypeID":')) {
			
			$responseArr	=	json_decode(trim(strip_tags($httpResponse)));
			
			// Process
			foreach($responseArr->MealTypeCategories as $MealTypeCategory) {
				$mealTypeCategoriesArr[$MealTypeCategory->CategoryID]	=	$MealTypeCategory->CategoryName;
			}
			
			/*
			[0] => Array
			(
				[mealplan_id] => 2461
				[mealplan_name] => Weight Loss
				[mealplan_active] => 1
			)
			*/
			
			foreach($responseArr->MealTypes as $MealType) {
				//$mealTypesArr[$MealType->CategoryID][$MealType->MealTypeID]	=	$MealType;
				//$mealTypesArr[$MealType->MealTypeID]	=	$MealType->Description;
				
				$mealTypesArr[$MealType->MealTypeID]['mealplan_id']		=	$MealType->MealTypeID;
				$mealTypesArr[$MealType->MealTypeID]['mealplan_name']	=	$MealType->Description;
				$mealTypesArr[$MealType->MealTypeID]['mealplan_active']	=	1;
				
			}
			
			$meal_plans		=	$mealTypesArr;
		
		}
		
	}
	
	//echo '<pre>';
	//print_r($mealTypesArr);
	//echo '</pre>';
	
?>

	<select name="meal_type" class="wpdmi-form-input wpdmi-required">
	<?php
	//$meal_plans = wpdmi_get_meal_plans();
	
	/*$options = array(
		'1' => 'Low Fat',
		'2' => 'Low Carb',
		'3' => 'Heart Healthy',
		'4' => 'Low Cholesterol',
		'5' => 'On The Go',
		'6' => 'Energy Booster',
		'7' => 'Teen Scene',
		'8' => 'Healthy Aging',
		'9' => 'Mature Women',
		'10' => 'Lean Body Builder',
		'11' => 'Mass Builder',
		'12' => 'Performance Training',
		'13' => 'Low (am) to High (pm)',
		'14' => 'High (am) to Low (pm)',
		'15' => 'Low Glycemic (all day)',
		'16' => 'Vegetarian / Low Fat',
		'17' => 'Vegan',
		'18' => 'Wheat Free / Low Fat',
		'19' => 'Breast Cancer',
		'20' => 'Stable Blood Sugar',
		'21' => 'Heart Disease',
		'22' => 'Osteoporosis (Bone Health)',
		'23' => 'Stroke Prevention',
		'24' => 'Cancer Prevention (General)'
	);*/
	
	foreach( $meal_plans as $value ):
	if( $value['mealplan_active'] ) {
	?>
	<option value="<?php echo $value['mealplan_id'];?>"<?php echo ( $user_profile[ 'meal_type' ] == $value['mealplan_id']) ? ' selected="selected"' : '' ?>><?php _e( $value['mealplan_name'], WPDMI_PLUGIN_TXT_DOMAIN); ?></option>	
	<?php
	}
	endforeach; ?>
	</select>
    <div class="uk-text-small uk-margin-top">NOTE: You will be able to modify your selection once your profile has been completed</div>
</p>
</fieldset>

<h3><?php _e( 'Medical Conditions', WPDMI_PLUGIN_TXT_DOMAIN); ?></h3>

<fieldset id="medicalconditions">
<p class="wpdmi-instruction"><?php _e( 'Please indicate if you currently have any of the following medical conditions:', WPDMI_PLUGIN_TXT_DOMAIN) ?></p>

<?php
do_action( 'wpdmi_yesno_fields', 'Heart Disease', 'heart' );
do_action( 'wpdmi_yesno_fields', 'Liver Disease', 'liver' );
do_action( 'wpdmi_yesno_fields', 'Pancreatic Disease', 'pancreatic' );	
do_action( 'wpdmi_yesno_fields', 'Anemia', 'anemia' );
do_action( 'wpdmi_yesno_fields', 'Kidney Disease', 'kidney' );
do_action( 'wpdmi_yesno_fields', 'Hypoglycemia', 'hypoglycemia' );
do_action( 'wpdmi_yesno_fields', 'Diabetes', 'diabetes' );
do_action( 'wpdmi_yesno_fields', 'Hypertension', 'hypertension' );
?>
<p class="wpdmi-instruction"><?php _e( 'Please indicate if you have a genetic or family history of any of the following medical conditions:', WPDMI_PLUGIN_TXT_DOMAIN) ?></p>
<?php
do_action( 'wpdmi_yesno_fields', 'Heart Disease', 'h_heart' );
do_action( 'wpdmi_yesno_fields', 'Breast Cancer', 'h_breast_cancer' );
do_action( 'wpdmi_yesno_fields', 'Other Cancer', 'h_cancer_other' );
do_action( 'wpdmi_yesno_fields', 'Liver Disease', 'h_liver' );
do_action( 'wpdmi_yesno_fields', 'Stroke', 'h_stroke' );
do_action( 'wpdmi_yesno_fields', 'Osteoporosis', 'h_osteoporosis' );
do_action( 'wpdmi_yesno_fields', 'Hypoglycemia', 'h_hypoglycemia' );
do_action( 'wpdmi_yesno_fields', 'Diabetes', 'h_diabetes' );
do_action( 'wpdmi_yesno_fields', 'Hypertension', 'h_hypertension' );
?>
</fieldset>

<input id="btn-submit" name="redirect-after-save" type="submit" value="<?php esc_attr_e( 'Save / Update', WPDMI_PLUGIN_TXT_DOMAIN ); ?>" target="_new" />
<input id="btn-update" name="just-save" type="submit" value="<?php esc_attr_e( 'Save / Update', WPDMI_PLUGIN_TXT_DOMAIN ); ?>" class="just-save" /> 
</form>
<?php 
} // End else if no $_GET set