<?php 
namespace ElementorMovieWidgets;

class Plugin {
    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }

    public function widget_scripts() {
        $ajax_url = admin_url( 'admin-ajax.php');
        wp_enqueue_script( 'elementor-movie-widgets', plugins_url( '/assets/js/movie-widget.js', __FILE__), array('jquery'), false, true );
        wp_localize_script('elementor-movie-widgets', 'ajax_url', $ajax_url);
    }

    private function include_widgets_files() {
        require_once(__DIR__ . '/widgets/movie-widget.php');
    }

    public function register_widgets() {
        $this->include_widgets_files();

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\MoviePortfolio() );
        
    }

    public function __construct() {
        add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widget_scripts') );

        add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets') );
    }
}

Plugin::instance();