<?php

/*
 * TEMPLATE FUNCTIONS
*/

//use global namespace so that functions can be called from main theme files

function get_page_title() {
	return Roots\Sage\Titles\title();
}

function the_page_title() {
	echo get_page_title();
}

function get_the_slug() {
	global $post;
	return $post->post_name;
}

function the_slug() {
	echo get_the_slug();
}

function optional_field( $html_output, $optional_field_value ) {
	if ( isset($optional_field_value) && $optional_field_value != '' ) {
		return sprintf($html_output, $optional_field_value);
	}
	else return '';
}

function custom_adjacent_post($query) {
	global $post;
	if ( is_array($query) ) $query['posts_per_page'] = '-1';
	else $query .= '&posts_per_page=-1';
	$result = new WP_Query($query);
	$all_posts = $result->posts;
	foreach ($all_posts as $index => $one_post) {
		if ( $post->post_name == $one_post->post_name ) {
			//found our current post
			if ( $index > 0 ) $prev_post = $all_posts[$index - 1];
			//if NO previous post, get the last post
			else $prev_post = $all_posts[count($all_posts) - 1];
			if ( $index < count($all_posts) - 2 ) $next_post = $all_posts[$index + 1];
			//if NO next post, get the first post
			else $next_post = $all_posts[0];
		}
	}
	return array('prev_post' => $prev_post, 'next_post' => $next_post);
}

function is_subpage($parentID = FALSE) {
	global $post;
	if ( is_page() && $post->post_parent ) {
		//this is a subpage
		if (!$parentID) {
			//not looking for a certain subpage.
			return TRUE;
		} else if ($post->post_parent === $parentID) {
			//looking for a certain subpage and this is it
			return TRUE;
		} else {
			//looking for a certain subpage and this is not it
			return FALSE;
		}
	} else {
		//this is not a subpage
		return FALSE;
	}
}

function if_first($input_if_first, $counter, $input_if_not_first = '') {
	if ( !$counter ) echo $input_if_first;
	else echo $input_if_not_first;
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

function get_post_thumbnail_url() {
	if ( has_post_thumbnail() ) {
	    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail_name');
	    return $thumb[0]; // thumbnail url
	} else {
		return false;
	}
}

function get_post_data( $post_objects, $key ) {
	$data = array();
	if ( !is_array($post_objects) ) {
		//return an empty array
		return $data;
	}
	for ( $n = 0; $n < count($post_objects); $n++ ) {
		$post_object = $post_objects[$n];
		$data[] = $post_object->$key;
	}
	return $data;
}
function get_post_slugs( $post_objects ) {
	return get_post_data( $post_objects, 'post_name' );
}
function get_post_IDs( $post_objects ) {
	return get_post_data( $post_objects, 'ID' );
}

function format_address($fields, $formatting) {
	$output = '';
	foreach ($fields as $key => $value) {
		$output .= optional_field($formatting[$key], $value);
	}
	return $output;
}

?>
