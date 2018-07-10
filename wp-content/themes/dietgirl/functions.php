<?php
function dietgirl_enqueue_styles() {
    $parent_style = 'divi-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'dietgirl_enqueue_styles' );

//wp_enqueue_script( 'divi-custom-script', $template_dir . '/js/custom' . $script_suffix . '.js', $dependencies_array , $theme_version, true );

function load_custom_core_options() {
    if ( ! function_exists( 'et_load_core_options' ) ) {
        function et_load_core_options() {
            $options = require_once( get_stylesheet_directory() . esc_attr( "/custom_options_divi.php" ) );
        }
    }
}
add_action( 'after_setup_theme', 'load_custom_core_options' );

/* added ny my dev */
/* register page redirect */
add_action( 'login_form_register', 'wpse45134_catch_register' );
function wpse45134_catch_register(){
    wp_redirect( home_url( '/register' ) );
    exit(); // always call `exit()` after `wp_redirect`
}
/* end register page redirect */
/* account page redirect */
/*function my_login_redirect() {
	if ( ! is_user_logged_in() && strpos( $_SERVER['REQUEST_URI'], 'account' ) ) {		
		wp_redirect( wp_login_url());
		exit();
	}
}

add_filter( 'template_redirect', 'my_login_redirect');*/
/* end account page redirect */
/* end added ny my dev */
?>
