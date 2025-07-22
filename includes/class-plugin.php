<?php
namespace LD_Analytics_Widgets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe principal do Plugin.
 */
final class Plugin {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_components();
        $this->add_hooks();
    }

    private function load_dependencies() {
        require_once LD_ANALYTICS_WIDGETS_PATH . 'includes/class-elementor-init.php';
        require_once LD_ANALYTICS_WIDGETS_PATH . 'admin/class-admin-menu.php';
        require_once LD_ANALYTICS_WIDGETS_PATH . 'includes/class-data-provider.php';
        require_once LD_ANALYTICS_WIDGETS_PATH . 'includes/class-ajax-handler.php';
    }

    private function init_components() {
        new Elementor_Init();
        new Admin\Admin_Menu();
        new Ajax_Handler();
    }

    private function add_hooks() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
    }

    public function enqueue_frontend_scripts() {
        // Biblioteca de Gráficos (Chart.js)
        wp_register_script(
            'ld-analytics-chart-js',
            LD_ANALYTICS_WIDGETS_URL . 'assets/js/chart.js',
            [], '4.4.1', true
        );

        // Biblioteca de Tabelas (DataTables.js)
        wp_register_script(
            'ld-analytics-datatables-js',
            LD_ANALYTICS_WIDGETS_URL . 'assets/js/datatables.min.js',
            ['jquery'], '1.13.6', true
        );

        // Script principal para interatividade
        wp_enqueue_script(
            'ld-analytics-main-js',
            LD_ANALYTICS_WIDGETS_URL . 'assets/js/main.js',
            [ 'jquery', 'ld-analytics-chart-js', 'ld-analytics-datatables-js' ],
            LD_ANALYTICS_WIDGETS_VERSION, true
        );

        wp_localize_script( 'ld-analytics-main-js', 'ldAnalytics', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'ld_analytics_nonce' )
        ]);

        // Estilos dos widgets (inclui DataTables)
        wp_enqueue_style(
            'ld-analytics-widgets-css',
            LD_ANALYTICS_WIDGETS_URL . 'assets/css/widgets.css',
            [], LD_ANALYTICS_WIDGETS_VERSION
        );
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( 'toplevel_page_ld-analytics-dashboard' !== $hook ) {
            return;
        }
        wp_enqueue_style(
            'ld-analytics-admin-css',
            LD_ANALYTICS_WIDGETS_URL . 'assets/css/admin.css',
            [], LD_ANALYTICS_WIDGETS_VERSION
        );
    }
}
