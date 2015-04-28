<?php

namespace Roots\Sage\Config;

use Roots\Sage\ConditionalTagCheck;

/**
 * Enable theme features
 */
add_theme_support('soil-clean-up');         // Enable clean up from Soil
add_theme_support('soil-relative-urls');    // Enable relative URLs from Soil
add_theme_support('soil-nice-search');      // Enable nice search from Soil
add_theme_support('soil-jquery-cdn');       // Enable to load jQuery from the Google CDN
add_theme_support('bootstrap-gallery');     // Enable Bootstrap's thumbnails component on [gallery]

/*
 * Remove theme features
 */

/*
 * Remove admin menus
 */
add_action( 'admin_menu', 'remove_admin_menus' );
function remove_admin_menus() {
	remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');
    remove_menu_page('link-manager.php');
	remove_menu_page('tools.php');
}

// Remove Comments
function df_disable_comments_post_types_support(){$post_types=get_post_types();foreach($post_types as $post_type){if(post_type_supports($post_type,'comments')){remove_post_type_support($post_type,'comments');remove_post_type_support($post_type,'trackbacks');}}}
add_action('admin_init','df_disable_comments_post_types_support');function df_disable_comments_status(){return false;}
add_filter('comments_open','df_disable_comments_status',20,2);add_filter('pings_open','df_disable_comments_status',20,2);function df_disable_comments_hide_existing_comments($comments){$comments=array();return $comments;}
add_filter('comments_array','df_disable_comments_hide_existing_comments',10,2);function df_disable_comments_admin_menu(){remove_menu_page('edit-comments.php');}
add_action('admin_menu','df_disable_comments_admin_menu');function df_disable_comments_admin_menu_redirect(){global $pagenow;if($pagenow==='edit-comments.php'){wp_redirect(admin_url());exit;}}
add_action('admin_init','df_disable_comments_admin_menu_redirect');function df_disable_comments_dashboard(){remove_meta_box('dashboard_recent_comments','dashboard','normal');}
add_action('admin_init','df_disable_comments_dashboard');function df_disable_comments_admin_bar(){if(is_admin_bar_showing()){remove_action('admin_bar_menu','wp_admin_bar_comments_menu',60);}}
add_action('init','df_disable_comments_admin_bar');

// Remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
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
if (WP_ENV == 'development' || WP_ENV == 'test') {
	show_admin_bar( FALSE );		
}
if (WP_ENV == 'test') {
	add_action('template_redirect', 'protect_whole_site');
}
function protect_whole_site() {
	if ( !is_user_logged_in() ) {
		auth_redirect();
	}
}


/**
 * Define which pages shouldn't have the sidebar
 * COMPLETELY REMOVE THE SIDEBAR
 */
function display_sidebar() {
  static $display;

  if (!isset($display)) {
    $conditionalCheck = new ConditionalTagCheck(
      /**
       * Any of these conditional tags that return true won't show the sidebar.
       * You can also specify your own custom function as long as it returns a boolean.
       *
       * To use a function that accepts arguments, use an array instead of just the function name as a string.
       *
       * Examples:
       *
       * 'is_single'
       * 'is_archive'
       * ['is_page', 'about-me']
       * ['is_tax', ['flavor', 'mild']]
       * ['is_page_template', 'about.php']
       * ['is_post_type_archive', ['foo', 'bar', 'baz']]
       *
       */
      [
        'is_404',
        'is_front_page',
        ['is_page_template', 'template-custom.php']
      ]
    );

    $display = apply_filters('sage/display_sidebar', $conditionalCheck->result);
  }

  //never show the sidebar
  return FALSE;
}
