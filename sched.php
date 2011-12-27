<?php
/*
Plugin Name: Sched Events API
Plugin URI: http://github.com/gotoht/wpschedapi
Description: WordPress connection to sched.org RESTful API
Version: 1.0.0
Author: Ljubo Canic
Author URI: http://goto.hr/
*/

define('SCHED_API_DIR', dirname(__FILE__));

@include_once SCHED_API_DIR."/library/curl/class.curl.php";
@include_once SCHED_API_DIR."/singletons/sched_api.php";

function sched_api_init() {
  global $sched_api;
  if (phpversion() < 5) {
    add_action('admin_notices', 'sched_api_php_version_warning');
    return;
  }
  if (!class_exists('SCHED_API')) {
    add_action('admin_notices', 'sched_api_class_warning');
    return;
  }
  add_filter('rewrite_rules_array', 'sched_api_rewrites');
  $sched_api = new SCHED_API();
}

function sched_api_php_version_warning() {
  echo "<div id=\"sched-api-warning\" class=\"updated fade\"><p>Sorry, SCHED API requires PHP version 5.0 or greater.</p></div>";
}

function sched_api_class_warning() {
  echo "<div id=\"sched-api-warning\" class=\"updated fade\"><p>Oops, sched_api class not found. If you've defined a sched_api_DIR constant, double check that the path is correct.</p></div>";
}

function sched_api_activation() {
// TODO Add the rewrite rule on activation
//  global $wp_rewrite;
//  add_filter('rewrite_rules_array', 'sched_api_rewrites');
//  $wp_rewrite->flush_rules();
}

function sched_api_deactivation() {
// TODO Remove the rewrite rule on deactivation
//  global $wp_rewrite;
//  $wp_rewrite->flush_rules();
}

function sched_api_rewrites($wp_rules) {
//  $base = get_option('sched_api_base', 'sched');
//  if (empty($base)) {
//    return $wp_rules;
//  }
//  $sched_api_rules = array(
//    "$base\$" => 'index.php?json=info',
//    "$base/(.+)\$" => 'index.php?json=$matches[1]'
//  );
//  return array_merge($sched_api_rules, $wp_rules);
}


// Add initialization and activation hooks
add_action('init', 'sched_api_init');
register_activation_hook(SCHED_API_DIR."/sched-api.php", 'sched_api_activation');
register_deactivation_hook(SCHED_API_DIR."/sched-api.php", 'sched_api_deactivation');

add_action('admin_menu', 'sched_api_admin_add_page');
function sched_api_admin_add_page() {
  add_options_page('SCHED API', 'SCHED API', 
          'manage_options', 'sched-api', 'sched_api_options_page');
}

function sched_api_options_page() {
  ?>
  <div>
  <h2>SCHED API</h2>
  <form action="options.php" method="post">
  <?php settings_fields('sched_api_options'); ?>
  <?php do_settings_sections('sched-api'); ?>

  <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
  </form></div>

  <?php
}

add_action('admin_init', 'sched_api_admin_init');
function sched_api_admin_init(){
  register_setting( 'sched_api_options', 'sched_api_options', 'sched_api_options_validate' );
  add_settings_section('sched_api_main', 'Main Settings', 'sched_api_section_text', 'sched-api');
  add_settings_field('sched_api_dev_key', 'SCHED API Dev key', 'sched_api_setting_dev_key', 
          'sched-api', 'sched_api_main');
}

function sched_api_section_text() {
  echo '<p>Enter dev key.</p>';
}

function sched_api_setting_dev_key() {
  $options = get_option('sched_api_options');
  echo "<input id='sched_api_dev_key' name='sched_api_options[dev_key]' size='40' type='text' value='{$options['dev_key']}' />";
}

function sched_api_options_validate($input) {
  $newinput['dev_key'] = trim($input['dev_key']);
  if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['dev_key'])) {
    $newinput['dev_key'] = '';
  }
  return $newinput;
}
?>