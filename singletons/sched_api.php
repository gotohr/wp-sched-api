<?php

class SCHED_API {
  
  static $client_path = '/wp-content/plugins/wp-sched-api/client/';
  
  function __construct() {
    add_action('wp_print_styles', array($this, 'add_css') );
    add_action('wp_print_scripts', array($this, 'add_js') );
    add_shortcode( 'conference', array($this, 'shortcode_conference') );
    add_shortcode( 'embeds', array($this, 'shortcode_embeds') );
  }

  function add_css() {
    wp_enqueue_style('sched', SCHED_API::$client_path . 'sched.css');
  }

  function add_js() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('sched', SCHED_API::$client_path . 'sched.js');
  }
    
  //[conference]
  function shortcode_conference( $atts ){
    $events_json = $this->get_events($atts['name']);
    
    $out = '<div id="conference"></div><script type="text/javascript">
      var sched_data = %s;      
      jQuery(document).ready(function() {
        var c = new Conference(jQuery("#conference"), sched_data);
      });</script>';
    return sprintf($out, $events_json);
  }
  
  //[embeds]
  function shortcode_embeds( $atts ){
    $out = '<script type="text/javascript" src="http://%s.sched.org/embed"></script>';
    return sprintf($out, $atts['conference']);
  }
  
  function get_key() { 
    $options = get_option('sched_api_options');
    return $options['dev_key']; 
  }
  
  function get_events($conference) {
    $queryarray = array('format' => 'json');
        
    return $this->request($this->get_url($conference, 'api/event/list?', $queryarray));
  }
  
  function get_url($conference, $action, $queryarray) {
    $queryarray['api_key'] = $this->get_key();
    return 'http://'.$conference.'.sched.org/'.$action.http_build_query($queryarray);
  }
  
  function request($url) {
    $hash = md5($url);
    $cache = SCHED_API_DIR.'/cache/'.$hash;
    // TODO check if cache is expired; make expired admin option
    if (file_exists($cache)) {
      $out = file_get_contents($cache);
    } else {
      $c = new curl($url);
      $out = $c->exec();
      $c->close();
      file_put_contents($cache, $out);
    }
    return $out;
  }
}
?>