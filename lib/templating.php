<?php

/*
 * TEMPLATE FUNCTIONS
*/

//use global namespace so that functions can be called from main theme files

function get_page_title() {
	return WD\Lib\Utils\title();
}

function the_page_title() {
	echo get_page_title();
}



function the_image($image_arr, $default_size = 'full', $classes = '', $size_attr = FALSE) {

	$srcset = function_exists('tevkori_get_srcset_string') ? tevkori_get_srcset_string($image_arr['ID'], $default_size) : '';

	if ( $size_attr ) $sizes = 'sizes="'.$size_attr.'"';
	else if ( function_exists('tevkori_get_sizes') ) $sizes = 'sizes="'.tevkori_get_sizes($image_arr['ID'], $default_size).'"';
	else $sizes = '';

	if (!$default_size || $default_size == 'full') $url = $image_arr['url'];
	else $url = $image_arr['sizes'][$default_size];

	$alt = $image_arr['alt'];

	return sprintf('<img src="%s" alt="%s" class="%s" %s %s />',
		$url,
		$alt,
		$classes,
		$sizes,
		$srcset
	);
}

?>
