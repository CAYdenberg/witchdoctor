<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/setup.php',                 // Utility functions
  'lib/wrapper.php',               // Theme wrapper class
  'lib/assets.php',                // Scripts and stylesheets
  'lib/titles.php',                // Page titles
  'lib/cpt.php',				           // Custom post types logic
  'lib/nav.php',
  'lib/templating.php',			       // Templating functions. Uses global namespace
  'lib/extras.php',    // Custom functions
  'lib/titles.php',    // Page titles
  'lib/wrapper.php'   // Theme wrapper class
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);
