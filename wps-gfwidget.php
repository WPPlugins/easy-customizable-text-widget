<?php
/*
Plugin name: Easy Customizable Text Widget
Author: Premium WP Suite
Author URI: http://www.premiumwpsuite.com
Version: 2.8.3
Description: Display your widgets in fancy style by using many customizable options this plugin offers.
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

use \WPS_ECTW\ECTW_MailChimp\ECTW_MailChimp;

define('WPS_GFW_SLUG', 'wps_gfw');
define('WPS_GFW_FONTS', 'wps_gfw_fonts');

require_once 'widget.php';
require_once 'api/MailChimp.php';

class wps_gfw {

  static $version = '2.8.3';

  static function init() {

    if (is_admin()) {
      add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));
      add_action('wp_ajax_get_font_preview', array(__CLASS__, 'ajax_preview_font'));

      // menu
      add_action('admin_menu', array(__CLASS__, 'menu'));

      // ajax
      add_action('wp_ajax_close_subscribe_notice', array(__CLASS__, 'ajax_close_subscribe'));

    } else {
      add_action('wp_enqueue_scripts', array(__CLASS__, 'frontend_enqueue_scripts'));
      add_action('wp_print_footer_scripts', array('wps_gfwidget', 'after_page_load'));
    }

  } // init


  static function ajax_close_subscribe() {
    update_option(WPS_GFW_SLUG . '-hide-subscribe', 'true');
    wp_send_json_error();
    die();
  } // ajax_close_subscribe


  static function is_subscribed() {
    $subscribed = get_option(WPS_GFW_SLUG . '-subscribed');
    if (empty($subscribed) || $subscribed == '0') {
      return false;
    } else {
      return true;
    }
  } // is_subscribed

  
  static function reset_sub() {
    delete_option(WPS_GFW_SLUG . '-hide-subscribe');
    delete_option(WPS_GFW_SLUG . '-subscribed');
  } // reset_sub
  

  static function menu() {
    $hide_subscribe = get_option(WPS_GFW_SLUG . '-hide-subscribe');

    if (!self::is_subscribed()) {
      #if (!empty($hide_subscribe)) {
        add_menu_page('ECTW Premium', 'ECTW Premium', 'manage_options', 'wps-ectw-premium', array(__CLASS__, 'premium_subscribe'));
      #} else {
      #  add_submenu_page(null, 'Easy Customizable Text Widget - PREMIUM', 'Easy Customizable Text Widget - PREMIUM', 'manage_options', 'wps-ectw-premium', array(__CLASS__, 'premium_subscribe'));
      #}
    }
  } // menu


  static function premium_subscribe() {
    $error = false;

    if (!empty($_POST)) {
      $MailChimp = new ECTW_MailChimp('690d2bf91ca1beebe1e328f28b3aab87-us12');

      $list_id = '29b532b6c8';

      $data = array('email_address' => $_POST['email'], 'status' => 'subscribed');
      $result = $MailChimp->post("lists/$list_id/members", $data);

      if ($result['status'] == 'subscribed' || $result['title'] == 'Member Exists') {
        update_option(WPS_GFW_SLUG . '-subscribed', 'yes');
      } else {
        $error = true;
        // error
        update_option(WPS_GFW_SLUG . '-subscribed', 'no');
      }
    } 

    if (self::is_subscribed()) {
      echo '<div class="wrap">';

      echo '<h1 class="page-title">Thanks for your subscription!</h1>';
      echo '<p>We will deliver your free premium updates to specified e-mail as soon as we launch!</p>';

      echo '</div>';
    } else {
      echo '<div class="wrap">';

      if ($error) {
        echo '<p class="error">There has been an error! Please double check your e-mail address!</p>';
      }

      echo '<h1 class="page-title">Subscribe to our Premium list</h1>';
      echo '<p>By subscribing to our <strong>premium</strong> list you will get all premium updates ASAP we relase them.. for <strong>FREE</strong>!</p>';

      echo '<form method="POST" action="admin.php?page=wps-ectw-premium" class="wps-mm-newsletter">';
      echo '<input type="text" name="email" class="widefat" value="" placeholder="Your e-mail addresss here..."/>';
      echo '<input type="submit" name="submit" id="submit" value="Subscribe" class="button button-primary" />';
      echo '<p class="important">* We will not spam your e-mail address or abuse it in any way.</p>';
      echo '</form>';

      echo '</div>';
    }

  } // premium_subscribe


  static function ajax_preview_font() {

    if (empty($_POST['font'])) {
      wp_send_json_error();
    }

    $font = trim($_POST['font']);
    $font = explode(':', $font);

    $font_family = str_replace('+', ' ', $font[0]);
    $variant = $font[1];

    $find_font = wps_gfwidget::find_font($font_family);
    if ($find_font) {
      wp_send_json_success(array('family' => $find_font['name'], 'variant' => $find_font['category']));
    } else {
      wp_send_json_error();
    }

    die();
  } // ajax_preview_font


  static function get_fonts() {
    $fonts = wp_remote_get(plugins_url('fonts/fonts.txt', __FILE__));
    $fonts = unserialize($fonts['body']);
    update_option(WPS_GFW_FONTS, $fonts);
  } // get_fonts


  static function frontend_enqueue_scripts() {
    wp_enqueue_style(WPS_GFW_SLUG . '-widget', plugins_url('/css/frontend.css', __FILE__), array(), self::$version);
  } // frontend_enqueue_scripts


  static function admin_enqueue_scripts() {
    $screen = get_current_screen();

    $hide_subscribe = get_option(WPS_GFW_SLUG . '-hide-subscribe');
    if (empty($hide_subscribe) && $screen->base == 'widgets' && !self::is_subscribed()) {
      // Add subscribe notice
      # wp_enqueue_script(WPS_GFW_SLUG . '-subscribe', plugins_url('/js/subscribe.scripts.js', __FILE__), array('jquery'), self::$version, true);
    }
    
    // WP Dependencies - JS
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tabs');
    // WP Dependencies - CSS
    wp_enqueue_style('jquery-ui');
    wp_enqueue_style('jquery-ui-core');
    wp_enqueue_style('jquery-ui-tabs');

    // Various enqueues
    wp_enqueue_style(WPS_GFW_SLUG . '-widget-area', plugins_url('/css/widget-area.css', __FILE__), array(), self::$version);
    wp_enqueue_script(WPS_GFW_SLUG . '-widget-area', plugins_url('/js/widget-area.scripts.js', __FILE__), array('jquery'), self::$version, true);

    // All pages
    wp_enqueue_style('wp-color-picker'); 
    wp_enqueue_script('wp-color-picker'); 
    wp_enqueue_script(WPS_GFW_SLUG . '-widgets-js', plugins_url('/js/widgets.scripts.js', __FILE__), array('jquery'), self::$version, true);

    wp_enqueue_media();

  } // admin_enqueue_scripts


  static function install() {
    self::get_fonts();
  } // install


} // wps_gfwidget

add_action('init', array('wps_gfw', 'init'));
register_activation_hook(__FILE__, array('wps_gfw', 'install'));