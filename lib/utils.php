<?php

namespace WD\Lib\Utils;

/**
 * Tell WordPress to use searchform.php from the templates/ directory
 */
function get_search_form() {
  $form = '';
  locate_template('/templates/searchform.php', true, false);
  return $form;
}
add_filter('get_search_form', __NAMESPACE__ . '\\get_search_form');

/**
 * Make a URL relative
 */
function root_relative_url($input) {
  preg_match('|https?://([^/]+)(/.*)|i', $input, $matches);
  if (!isset($matches[1]) || !isset($matches[2])) {
    return $input;
  } elseif (($matches[1] === $_SERVER['SERVER_NAME']) || $matches[1] === $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']) {
    return wp_make_link_relative($input);
  } else {
    return $input;
  }
}

/**
 * Compare URL against relative URL
 */
function url_compare($url, $rel) {
  $url = trailingslashit($url);
  $rel = trailingslashit($rel);
  if ((strcasecmp($url, $rel) === 0) || root_relative_url($url) == $rel) {
    return true;
  } else {
    return false;
  }
}

/**
 * Check if element is empty
 */
function is_element_empty($element) {
  $element = trim($element);
  return !empty($element);
}

/**
 * Page titles
 */
function title() {
  if (is_home()) {
    if (get_option('page_for_posts', true)) {
      return get_the_title(get_option('page_for_posts', true));
    } else {
      return __('Latest Posts', 'sage');
    }
  } elseif (is_archive()) {
    return get_the_archive_title();
  } elseif (is_search()) {
    return sprintf(__('Search Results for %s', 'sage'), get_search_query());
  } elseif (is_404()) {
    return __('Not Found', 'sage');
  } else {
    return get_the_title();
  }
}
