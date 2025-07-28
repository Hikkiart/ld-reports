<?php
namespace LD_Analytics_Widgets\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe que cria o menu do plugin no painel de admin.
 */
class Admin_Menu {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu_page' ] );
    }

    public function add_admin_menu_page() {
        add_menu_page(
            __( 'LD Analytics', 'ld-analytics-widgets' ),
            __( 'LD Analytics', 'ld-analytics-widgets' ),
            'manage_options',
            'ld-analytics-dashboard',
            [ $this, 'render_dashboard_page' ],
            'dashicons-chart-area',
            30
        );
    }

    public function render_dashboard_page() {
        // Inclui o arquivo de visualização da página do dashboard.
        require_once LD_ANALYTICS_WIDGETS_PATH . 'admin/views/admin-dashboard-view.php';
    }
}
