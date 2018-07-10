<?php

require_once( get_template_directory() . esc_attr( "/options_divi.php" ) );

global $options;

$epanel_key = "name";

$epanel_value = "Show RSS Icon";

$custom_options = array (
	array( "name" =>esc_html__( "Show Pinterest Icon", $themename ),
           "id" => $shortname . "_show_pinterest_icon",
           "type" => "checkbox",
           "std" => "on",
           "desc" =>esc_html__( "Here you can choose to display the Pinterest Icon on your homepage. ", $themename ) ),
	array( "name" =>esc_html__( "Pinterest Profile Url", $themename ),
           "id" => $shortname . "_pinterest_url",
           "std" => "#",
           "type" => "text",
           "validation_type" => "url",
		   "desc" =>esc_html__( "Enter the URL of your Pinterest Profile. ", $themename ) ),	
);

foreach( $options as $index => $value ) {
    if ( isset($value[$epanel_key]) && $value[$epanel_key] === $epanel_value ) {
        foreach( $custom_options as $custom_index => $custom_option ) {
            $options = insertArrayIndex($options, $custom_option, $index+$custom_index+1);
        }
        break;
    }
}

function insertArrayIndex($array, $new_element, $index) {
	$start = array_slice($array, 0, $index);
	$end = array_slice($array, $index);
	$start[] = $new_element;
	return array_merge($start, $end);
}

return $options;