<?php
/**
 * Plugin Name: Elementor Movie Widgets
 * Description: Add Movie Wiget to Elementor
 * Plugin URI: https://www.evilgeniusdevel.com
 * Version: 0.1.0
 * Author: Drew Wiltjer
 * Author URI: https://www.evilgeniusdvel.com
 * text-domain: elementor-movie-widgets
 */

 if( ! defined( 'ABSPATH') ) exit; //exit if accessed directly

 final class Elementor_Movie_Widgets {
     const VERSION = '0.1.0';
     const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
     const MINIMUM_PHP_VERSION = '7.0';

     public function __construct() {
        add_action( 'init', array( $this, 'i18n' ) ); //loads translation
        add_action( 'plugins_loaded', array( $this, 'init') ); //init plugin
     }

     public function i18n() {
         load_plugin_textdomain( 'elementor-movie-widgets');
     }

     public function init() {
        //Check if Elementor is installed and activated
        if ( !did_action( 'elementor/loaded') ) {
             add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
             return;
        }

        //check for required Elementor version
        if( !version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version') );
            return;
        }

        //check for required PHP version
        if ( !version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>' ) ) {
            add_action( 'admin_notices' , array( $this, 'admin_notice_minimum_php_version' ) );
            return;
        }

        //passed validation, load plugin
        require_once( 'plugin.php' );
     }

     /**
      * Admin notices
      */
      public function admin_notice_missing_main_plugin() {
          if (isset( $_GET['activate'] ) ) {
              unset( $_GET['activate'] );
          }

          $message = sprintf(
              esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-movie-widgets'),
                        '<strong>' . esc_html__( 'Elementor Movie Widgets', 'elementor-movie-widgets') . '</strong>',
                        '<strong>' . esc_html__( 'Elementor', 'elementor-movie-widgets') . '</strong>'
          );

          printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
      }

      public function admin_notice_minimum_elementor_version() {
        if (isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-movie-widgets'),
                      '<strong>' . esc_html__( 'Elementor Movie Widgets', 'elementor-movie-widgets') . '</strong>',
                      '<strong>' . esc_html__( 'Elementor', 'elementor-movie-widgets') . '</strong>',
                      self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    public function admin_notice_minimum_php_version() {
        if (isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-movie-widgets'),
                      '<strong>' . esc_html__( 'Elementor Movie Widgets', 'elementor-movie-widgets') . '</strong>',
                      '<strong>' . esc_html__( 'PHP', 'elementor-movie-widgets') . '</strong>',
                      self::MINIMUM_PHP_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
 }

 new Elementor_Movie_Widgets();

 //update seen status of movies (AJAX)
 function record_movie_seen_status() {
     if (! isset( $_POST ) || empty( $_POST ) || !is_user_logged_in() ) {
        header( 'HTTP/1.1 400 Empty POST Values' );
        echo 'Could Not Verify POST Values.';
        exit;
     }

     $user_id = get_current_user_id();
     $movieMetaKey = 'movie-statuses';
     $movieID = $_POST['movieID'];
     $seen = $_POST['seen'];

     $movieArray = (get_user_meta($user_id, $movieMetaKey, true)) ? get_user_meta($user_id, $movieMetaKey, true) : [] ;
     $movieIndex = array_search($movieID, $movieArray, false);
     if($movieIndex !== false) {
        $movieIndex += 1;
        $movieArray[$movieIndex] = $seen;
     }
     else {
         array_push($movieArray, $movieID, $seen);
     }
     update_user_meta( $user_id, $movieMetaKey, $movieArray);
    
     exit;
 }
 add_action('wp_ajax_nopriv_update_seen_status', 'record_movie_seen_status');
 add_action('wp_ajax_update_seen_status', 'record_movie_seen_status');