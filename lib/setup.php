<?php

namespace Setup;

use Assets;

/**
 * Theme setup
 */
function setup() {

  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain('sage', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'sage')
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/main.css'));
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');


/**
 * Theme assets
 */
function assets() {
  wp_deregister_script('wp-embed');
  wp_deregister_script('jquery');
  wp_deregister_script('jquery-migrate');

  wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);



  wp_enqueue_script('sage/js', Assets\asset_path('scripts/main.js'), null, true);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);


// Remove admin menus
add_action( 'admin_menu', __NAMESPACE__.'\remove_admin_menus' );
function remove_admin_menus() {
  remove_menu_page('edit-comments.php');
  remove_menu_page('link-manager.php');
	remove_menu_page('tools.php');
}

// Remove Comments
function df_disable_comments_post_types_support() {
  $post_types=get_post_types();
  foreach($post_types as $post_type) {
    if(post_type_supports($post_type,'comments')) {
      remove_post_type_support($post_type,'comments');
      remove_post_type_support($post_type,'trackbacks');
    }
  }
}
add_action('admin_init', __NAMESPACE__.'\df_disable_comments_post_types_support');

function df_disable_comments_status(){
  return false;
}
add_filter('comments_open', __NAMESPACE__.'\df_disable_comments_status',20,2);
add_filter('pings_open', __NAMESPACE__.'\df_disable_comments_status',20,2);

function df_disable_comments_hide_existing_comments($comments) {
  $comments=array();
  return $comments;
}
add_filter('comments_array', __NAMESPACE__.'\df_disable_comments_hide_existing_comments',10,2);

function df_disable_comments_admin_menu_redirect(){
  global $pagenow;
  if($pagenow==='edit-comments.php') {
    wp_redirect(admin_url());
    exit;
  }
}
add_action('admin_init',__NAMESPACE__.'\df_disable_comments_admin_menu_redirect');

function df_disable_comments_dashboard() {
  remove_meta_box('dashboard_recent_comments','dashboard','normal');
}
add_action('admin_init',__NAMESPACE__.'\df_disable_comments_dashboard');

function df_disable_comments_admin_bar() {
  if (is_admin_bar_showing() ) {
    remove_action('admin_bar_menu','wp_admin_bar_comments_menu',60);
  }
}
add_action('init', __NAMESPACE__.'\df_disable_comments_admin_bar');

function df_remove_recent_comments_style() {
  global $wp_widget_factory;
  remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', __NAMESPACE__ . '\df_remove_recent_comments_style' );


// Remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );




/**
 * Configuration values
 */
if (!defined('WP_ENV')) {
  // Fallback if WP_ENV isn't defined in your WordPress config
  // Used in lib/assets.php to check for 'development' or 'production'
  define('WP_ENV', 'production');
}

if (!defined('DIST_DIR')) {
  // Path to the build directory for front-end assets
  define('DIST_DIR', '/dist/');
}

/*
 * Configure Development and testing environment
 */

show_admin_bar( FALSE );

if (WP_ENV === 'test') {
	add_action('template_redirect', __NAMESPACE__ . '\protect_whole_site');
}
function protect_whole_site() {
	if ( !is_user_logged_in() ) {
		auth_redirect();
	}
}
