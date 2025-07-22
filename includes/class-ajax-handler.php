<?php
namespace LD_Analytics_Widgets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Manipuladora de AJAX.
 */
class Ajax_Handler {

    public function __construct() {
        add_action( 'wp_ajax_ld_analytics_get_widget_data', [ $this, 'get_widget_data' ] );
        add_action( 'wp_ajax_ld_analytics_get_courses', [ $this, 'get_courses' ] );
    }

    public function get_widget_data() {
        check_ajax_referer( 'ld_analytics_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Acesso negado.', 'ld-analytics-widgets' ) ], 403 );
        }

        $widget_type = sanitize_text_field( $_POST['widget_type'] );
        $metric = sanitize_text_field( $_POST['metric'] );
        $filters = isset( $_POST['filters'] ) ? (array) $_POST['filters'] : [];
        
        // Sanitizar filtros
        $sanitized_filters = [
            'start_date' => isset($filters['start_date']) ? sanitize_text_field($filters['start_date']) : null,
            'end_date' => isset($filters['end_date']) ? sanitize_text_field($filters['end_date']) : null,
            'course_id' => isset($filters['course_id']) ? sanitize_text_field($filters['course_id']) : null,
        ];

        try {
            $data = [];
            switch ( $widget_type ) {
                case 'kpi_card':
                    $data = Data_Provider::get_kpi_data( $metric, $sanitized_filters );
                    break;
                case 'line_chart':
                    $data = Data_Provider::get_line_chart_data( $metric, $sanitized_filters );
                    break;
                case 'bar_chart':
                    $data = Data_Provider::get_bar_chart_data( $metric, $sanitized_filters );
                    break;
                case 'data_table':
    if ($metric === 'student_report') {
        $data = Data_Provider::get_table_data( $metric, $filters );
    } elseif ($metric === 'course_effectiveness') {
        $data = Data_Provider::get_course_effectiveness_data( $filters );
    }
    break;
                default:
                    throw new \Exception(__( 'Tipo de widget desconhecido.', 'ld-analytics-widgets' ));
            }
            wp_send_json_success( $data );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ], 500 );
        }
    }
    
    public function get_courses() {
        check_ajax_referer( 'ld_analytics_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Acesso negado.', 'ld-analytics-widgets' ) ], 403 );
        }
        
        try {
            $courses = Data_Provider::get_all_courses();
            wp_send_json_success($courses);
        } catch (\Exception $e) {
            wp_send_json_error( [ 'message' => $e->getMessage() ], 500 );
        }
    }
}