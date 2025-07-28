<?php
namespace LD_Analytics_Widgets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe de inicialização do Elementor.
 */
class Elementor_Init {

    public function __construct() {
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category' ] );
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
    }

    public function register_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'ld-analytics',
            [
                'title' => __( 'LearnDash Analytics', 'ld-analytics-widgets' ),
                'icon'  => 'eicon-chart',
            ]
        );
    }

    public function register_widgets( $widgets_manager ) {
        // Inclui a classe base
        require_once LD_ANALYTICS_WIDGETS_PATH . 'widgets/base/class-base-widget.php';

        $widgets = [
            'widget-kpi-card.php',
            'widget-line-chart.php',
            'widget-bar-chart.php',
            'widget-data-table.php',
            'widget-filters.php',
        ];

        foreach ( $widgets as $widget_file ) {
            require_once LD_ANALYTICS_WIDGETS_PATH . 'widgets/' . $widget_file;
            $class_name = __NAMESPACE__ . '\Widgets\\' . str_replace( '-', '_', ucwords( basename( $widget_file, '.php' ), '-' ) );
            if ( class_exists( $class_name ) ) {
                $widgets_manager->register( new $class_name() );
            }
        }
    }
}
