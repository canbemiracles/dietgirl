<?php 
/**
 * Admin functions
 *
 * @package DietMaster-Integration
 */

function wpdmi_admin_notices() {
	
	if( get_option( 'wpdmi_hide_notice' ) == 1 )
		return;
	
	if( !empty( $options[ 'passthru_page' ] ) ) {
	$html = '<div class="updated">';
		$html .= '<p>';
		$html .= sprintf( __( 'Dietmaster Integration: The default <a href="%s">login page</a> has been successfully created.', WPDMI_PLUGIN_TXT_DOMAIN ), get_permalink( $options[ 'passthru_page' ] ) );
		$html .= '</p>';
	$html .= '</div>';
	}
	
	$html .= '<div class="updated error">';
		$html .= '<p>';
		$html .= __( 'Dietmaster Integration: You must enter your unique PassThruKey on this <a href="options-general.php?page=dietmaster-menu">settings</a> page.', WPDMI_PLUGIN_TXT_DOMAIN );
		$html .= '</p>';
	$html .= '</div>'; 
	echo $html;
		
	// Hide the post-installation notice
	update_option( 'wpdmi_hide_notice', 1 );
}

add_action( 'admin_menu', 'wpdmi_admin_menu' );

function wpdmi_admin_menu() {
	add_submenu_page( 'options-general.php', 'Dietmaster Integrations', 'Dietmaster', 'manage_options', 'dietmaster-menu', 'wpdmi_admin_menu_callback' );
}

function wpdmi_admin_menu_callback () {
	?><div class="wrap">
		<h2>Dietmaster Integration Settings</h2>
		<form action="options.php" method="post" id="wpdmi-options-form">
		<?php
			settings_fields( 'dietmaster-menu' );
			do_settings_sections( 'dietmaster-menu' );
			submit_button();
		?>
		</form>
	</div>
<?php
}

add_action( 'admin_init', 'wpdmi_admin_init' );

function wpdmi_admin_init() {
	register_setting( 'dietmaster-menu', 'wpdmi', 'wpdmi_validate_options' );
	
	register_setting( 'dietmaster-menu', 'wpdmi_token_email_text ', 'wpdmi_validate_options' );
	
	add_settings_section( 'wpdmi_options_credentials', 'Your Account Credentials', 'wpdmi_credential_section', 'dietmaster-menu' );
	
	add_settings_section( 'wpdmi_options_authentication', 'Member Authentication', 'wpdmi_authentication_section', 'dietmaster-menu' );
	
	add_settings_section( 'wpdmi_options_meal_plans', 'Meal Plans', 'wpdmi_meal_plans_section', 'dietmaster-menu' );
	
	add_settings_section( 'wpdmi_options_integrations', '3rd-party Plugin Integration (optional)', 'wpdmi_integrations_section', 'dietmaster-menu' );
	
	add_settings_section( 'wpdmi_options_notification_email', 'Account Notification Email', 'wpdmi_notification_email_section', 'dietmaster-menu' );
	
	// All setting fields 
	add_settings_field( 'wpdmi_dmwebpro_url', __( 'DM WebPro URL*', WPDMI_PLUGIN_TXT_DOMAIN ), 'wpdmi_input_dmwebpro_url', 'dietmaster-menu', 'wpdmi_options_credentials' );
	
	add_settings_field( 'wpdmi_passthru_key', __( 'Pass-thru Key*', WPDMI_PLUGIN_TXT_DOMAIN ), 'wpdmi_input_passthru_key', 'dietmaster-menu', 'wpdmi_options_credentials' );
	
	add_settings_field( 'wpdmi_integration_method', __( 'Integration Method*', WPDMI_PLUGIN_TXT_DOMAIN ), 'wpdmi_input_integration_method', 'dietmaster-menu', 'wpdmi_options_authentication' );
	
	add_settings_field( 'wpdmi_passthru_page', __('Login Page*', WPDMI_PLUGIN_TXT_DOMAIN), 'wpdmi_input_passthru_page', 'dietmaster-menu', 'wpdmi_options_authentication' );
	
	add_settings_field( 'wpdmi_page_accessdinied', __('Access Denied Page (optional)', WPDMI_PLUGIN_TXT_DOMAIN), 'wpdmi_input_page_accessdinied', 'dietmaster-menu', 'wpdmi_options_authentication' );
	
	add_settings_field( 'wpdmi_detect_mobile', __('Mobile Detection', WPDMI_PLUGIN_TXT_DOMAIN), 'wpdmi_input_detect_mobile', 'dietmaster-menu', 'wpdmi_options_authentication' );
	
	add_settings_field( 'wpdmi_plugin_integration', __('Plugin Integration', WPDMI_PLUGIN_TXT_DOMAIN), 'wpdmi_input_plugin_integration', 'dietmaster-menu', 'wpdmi_options_integrations' );
	
	add_settings_field( 'wpdmi_membership_levels', __('Membership Levels', WPDMI_PLUGIN_TXT_DOMAIN), 'wpdmi_input_membership_levels', 'dietmaster-menu', 'wpdmi_options_integrations' );
	
	add_settings_field( 'wpdmi_meal_plans', __('Your Meal Plans', WPDMI_PLUGIN_TXT_DOMAIN), 'wpdmi_input_meal_plans', 'dietmaster-menu', 'wpdmi_options_meal_plans' );
	
	add_settings_field( 'wpdmi_token_email_text', __('User Notification Email', WPDMI_PLUGIN_TXT_DOMAIN), 'wpdmi_input_token_email_text', 'dietmaster-menu', 'wpdmi_options_notification_email' );
}

// Validate user input
function wpdmi_validate_options( $input ) {
	return $input;
}

function wpdmi_credential_section() {
	echo '<p>' . __('<p>Enter your credentials given by DietMaster. * Denotes required fields.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_authentication_section() {
	echo '<p>' . __('Options for how to let members access DietMaster,', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_integrations_section() {
	echo '<p>' . __('Set options for one of the supported 3rd-party plugins.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_meal_plans_section() {
	echo '<p>' . __('Configure meal plan options available to your members. Enter the exact "Meal Type ID" given by DietMaster.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_notification_email_section() {
	echo '<p>' . __('This email will go out when a new account is successfully created and the user is notified of their mobile token.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_input_passthru_key() {
	$options = get_option( 'wpdmi' );
	echo "<input type='text' name='wpdmi[passthru_key]' value='{$options['passthru_key']}' size='15' />";
	echo '<p class="description">'. __( 'Your unique key assigned to your account by DietMaster. <b>(Case-sensitive)</b>', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_input_integration_method() {
	$options = get_option( 'wpdmi' );
	echo "<select name='wpdmi[integration_method]'>";
	echo '<option value="profile_passthru"' . ( ( $options['integration_method'] == 'profile_passthru' ) ? ' selected' : '' ) . '>Profile Pass-thru</option>';
	echo '<option value="reserveuser_passthru"' . ( ( $options['integration_method'] == 'reserveuser_passthru' ) ? ' selected' : '' ) . '>Reserve User Pass-thru</option>';
	echo '</select>';
	echo '<p class="description">'. __( 'Profile Pass-thru: Members can use the profile set up form as well as login page to access Dietmaster.<br /><br />Reserve User Pass-thru: Choose this method if you don\'t want members to use the profile set up form at all. (Members can still do that on your Dietmaster site.)', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
	
	/*<script>
	jQuery(function($) {
		$( '[name="wpdmi[integration_method]"]' ).click( function() {
			$( '.hidden_option' ).hide();
			$( '#wpdmi_login_' + $(this).val() ).show();
		});
	});
	</script>*/
	
}

function wpdmi_input_page_accessdinied() {
	$options = get_option( 'wpdmi' );
	
	$args = array(
		'sort_order' => 'ASC',
		'sort_column' => 'post_title',
		'numberposts' => -1,
		'offset' => 0 );	

	$pages = get_pages( $args );
	
	if( !empty( $pages ) ) {
		
		echo '<select name="wpdmi[page_accessdenied]">';
		echo '<option value="">' . __( 'Choose...', WPDMI_PLUGIN_TXT_DOMAIN ) . '</option>';
		
		foreach( $pages as $value ) {
			echo '<option value="'.$value->ID.'"' . ( ($options['page_accessdenied'] == $value->ID) ? ' selected' : '' ) . '>'.($value->post_parent > 0 ? '- ' : '').substr($value->post_title,0,40).'</option>';
		}
		
		echo '</select>';	
		echo '<p class="description">' . __( 'Select a page that members will see when they are not logged in. ', WPDMI_PLUGIN_TXT_DOMAIN ) . '</p>';
		
		echo '<p>' . __( 'Usage: Use this shortcode <strong>[dietmaster-integration-profile-form]</strong> within any page to generate a profile update form. Admin users are excluded from login pass thru.', WPDMI_PLUGIN_TXT_DOMAIN ) . '</p>';
		
	}
}

function wpdmi_input_detect_mobile() {
	$options = get_option( 'wpdmi' );
	echo '<input type="radio" name="wpdmi[detect_mobile]" value="1" ' . ( ( $options['detect_mobile'] ) ? 'checked="checked"' : '' ) . ' /> ' . __( 'Yes', WPDMI_PLUGIN_TXT_DOMAIN ) . ' <input type="radio" name="wpdmi[detect_mobile]" value="0" ' . ( ( $options['detect_mobile'] ) ? '' : 'checked="checked"' ) . ' /> ' . __( 'No', WPDMI_PLUGIN_TXT_DOMAIN );

	echo '<p class="description">'. __( 'Prompt members to use mobile app if they are on mobile devices.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_input_plugin_integration() {
	$options = get_option( 'wpdmi' );
	echo "<select name='wpdmi[membership_integration]'>";
	echo '<option value="">---</option>';
	echo '<option value="wishlist_member"' . ( ( $options['membership_integration'] == 'wishlist_member' ) ? ' selected' : '' ) . '>Wishlist Member</option>';
	//echo '<option value="paid_membership_pro"' . ( ( $options['membership_integration'] == 'paid_membership_pro' ) ? ' selected' : '' ) . '>Paid Membership Pro</option>';
	echo '</select>';
	echo '<p class="description">'. __( 'Select one of these supported membership plugins. It must be installed and activated for the integration to work.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_input_membership_levels() {
	$options = get_option( 'wpdmi' );
	
	if( class_exists( 'WishListMemberCore' ) ) {
		echo '<div class="display none wishlist">';
		
		// Get all exiting levels
		$wlm_levels = wpdmi_get_all_wlm_levels();
		
		if( !$wlm_levels ) {
			echo '<p>'. __( 'You must first create membership levels on Wishlist Member.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
		} else {
			foreach( $wlm_levels as $level ) {
				echo '<input type="checkbox" name="wpdmi[membership_levels][]" value="' . $level['level_id'] . '"' . ( ( $level['level_id'] == $options['membership_levels'] || in_array( $level['level_id'], $options['membership_levels'] ) ) ? ' checked="checked" ' : '' ) .' />' . $level['name'] . ' ';
			}
		}
		
		echo '<p class="description">'. __( 'Select Wishlist Member levels that should be integrated with your Dietmaster account.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
		
		echo '</div>';
	}
}

function wpdmi_input_meal_plans() {
	$options = get_option( 'wpdmi' );
	?>
	<table id="repeatable-fieldset-mealplans" width="100%">
	<thead>
		<tr>
			<th width="2%"></th>
			<th width="20%"><?php _e( 'Meal Type ID', WPDMI_PLUGIN_TXT_DOMAIN ); ?></th>
			<th width="60%"><?php _e( 'Name', WPDMI_PLUGIN_TXT_DOMAIN ); ?></th>
			<th width="10%"><?php _e( 'Active', WPDMI_PLUGIN_TXT_DOMAIN ); ?></th>
			<th width="2%"></th>
		</tr>
	</thead>
	<tbody>
	<?php 
	$meal_plans = wpdmi_get_meal_plans();
	
	if( $meal_plans ) {
		foreach( $meal_plans as $meal_plan ) { ?>
			<tr>
				<td><a class="button remove-row" href="#">-</a></td>
				<td><input type="text" name="wpdmi[mealplan_id][]" value="<?php if($meal_plan['mealplan_id'] != '') echo esc_attr( $meal_plan['mealplan_id'] ); ?>" maxlength="4" size="4" /></td>
				<td><input type="text" name="wpdmi[mealplan_name][]" value="<?php if ($meal_plan['mealplan_name'] != '') echo esc_attr( $meal_plan['mealplan_name'] ); ?>" placeholder="<? _e( 'Your custom name...', WPDMI_PLUGIN_TXT_DOMAIN ); ?>" /></td>
				<td><input type="checkbox" name="wpdmi[mealplan_active][]" value="<? echo $meal_plan['mealplan_id']; ?>" <?php if( $meal_plan['mealplan_active'] ) echo 'checked="checked"'; ?> /></td>
				<td><a class="sort"><span class="dashicons dashicons-menu"></span></a></td>
			</tr>
		<?php }
	} ?>
	<!-- empty hidden one for jQuery -->
	<tr class="empty-row screen-reader-text">
		<td><a class="button remove-row" href="#">-</a></td>
		<td><input type="text" name="wpdmi[mealplan_id][]" maxlength="4" size="4" /></td>
		<td><input type="text" name="wpdmi[mealplan_name][]" /></td>
		<td><input type="checkbox" name="wpdmi[mealplan_active][] value="1" checked="checked" /></td>
		<td><a class="sort"><span class="dashicons dashicons-menu"></span></a></td>
	</tr>
	</tbody>
	</table>
	<p><a id="add-row" class="button" href="#">Add another</a>
	<input type="submit" class="metabox_submit" value="Save" />
	</p>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.metabox_submit').click(function(e) {
			e.preventDefault();
			$('#submit').click();
		});
		$('#add-row').on('click', function() {
			var row = $('.empty-row.screen-reader-text').clone(true);
			row.removeClass('empty-row screen-reader-text');
			row.insertBefore('#repeatable-fieldset-mealplans tbody>tr:last');
			return false;
		});
		$('.remove-row').on('click', function() {
			$(this).closest('tr').remove();
			return false;
		});

		$('#repeatable-fieldset-mealplans tbody').sortable({
			opacity: 0.6,
			revert: true,
			cursor: 'move',
			handle: '.sort'
		});
	});
	</script>
<?php
}
	
function wpdmi_input_passthru_page() {
	$options = get_option( 'wpdmi' );
	
	$args = array(
		'sort_order' => 'ASC',
		'sort_column' => 'post_title',
		'numberposts' => -1,
		'offset' => 0 );	

	$pages = get_pages( $args );
	
	if( !empty( $pages ) ) {
		
		echo '<div id="wpdmi_login_page" class="hidden_option"><select name="wpdmi[passthru_page]">';
		
		foreach( $pages as $value ) {
			echo '<option value="'.$value->ID.'"' . ( ($options['passthru_page'] == $value->ID) ? ' selected' : '' ) . '>'.($value->post_parent > 0 ? '- ' : '').substr($value->post_title,0,40).'</option>';
		}
		
		echo '</select>';
		echo '<p class="description">'. __( 'This is the page members will access DietMaster from.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p></div>';		
		
	}
}

function wpdmi_input_dmwebpro_url() {
	$options = get_option( 'wpdmi' );
	echo "http://<input type='text' name='wpdmi[dmwebpro_url]' value='{$options['dmwebpro_url']}' size='40' />";
	echo '<p class="description">'. __( 'Your DietMaster Web Pro Hostname assigned to your account. ( Example: nutrition.dmwebpro.com - do not include a trailing slash / )', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

function wpdmi_input_token_email_text() {
	$option = get_option( 'wpdmi_token_email_text' );
	echo "<textarea name='wpdmi_token_email_text' class='large-text' rows='15' cols='45'>{$option}</textarea>";
	echo '<p class="description">'. __( 'The account creation email sent to the new user with their mobile token. Tags with "[]" will be replaced by their appropriate data.', WPDMI_PLUGIN_TXT_DOMAIN ). '</p>';
}

add_action( 'admin_head', 'wpdmi_admin_head' );

function wpdmi_admin_head() {
	wp_enqueue_script('jquery-ui-sortable');
}
?>