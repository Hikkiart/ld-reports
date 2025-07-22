<?php
namespace LD_Analytics_Widgets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Provedora de Dados.
 * Busca, processa e formata todos os dados do LearnDash com consultas SQL reais.
 */
class Data_Provider {

    /**
     * Busca dados de KPI.
     */
    public static function get_kpi_data( $metric, $filters = [] ) {
        global $wpdb;
        $activity_table = $wpdb->prefix . 'learndash_user_activity';
        
        $value = 0;
        $label = '';
        
        $course_query = self::get_course_where_clause($filters, 'course_id');

        switch ( $metric ) {
            case 'active_students':
                $label = __( 'Alunos Ativos', 'ld-analytics-widgets' );
                $date_query = self::get_date_where_clause($filters, 'activity_started');
                $value = $wpdb->get_var( "SELECT COUNT(DISTINCT user_id) FROM {$activity_table} WHERE 1=1 {$date_query} {$course_query}" );
                break;
            case 'completed_courses':
                $label = __( 'Cursos Concluídos (Geral)', 'ld-analytics-widgets' );
                $date_query = self::get_date_where_clause($filters, 'activity_completed');
                $value = $wpdb->get_var( "SELECT COUNT(activity_id) FROM {$activity_table} WHERE activity_type = 'course' AND activity_status = 1 {$date_query} {$course_query}" );
                break;
            case 'completion_rate':
                $label = __( 'Taxa Média de Conclusão', 'ld-analytics-widgets' );
                $date_query_started = self::get_date_where_clause($filters, 'activity_started');
                $enrollments_query = "SELECT COUNT(activity_id) FROM {$activity_table} WHERE activity_type = 'course' {$date_query_started} {$course_query}";
                $enrollments = $wpdb->get_var($enrollments_query);

                $date_query_completed = self::get_date_where_clause($filters, 'activity_completed');
                $completions_query = "SELECT COUNT(activity_id) FROM {$activity_table} WHERE activity_type = 'course' AND activity_status = 1 {$date_query_completed} {$course_query}";
                $completions = $wpdb->get_var($completions_query);
                
                $value = ( $enrollments > 0 ) ? ( $completions / $enrollments ) * 100 : 0;
                break;
            case 'total_enrollments':
                $label = __( 'Total de Inscrições', 'ld-analytics-widgets' );
                $date_query = self::get_date_where_clause($filters, 'activity_started');
                $value = $wpdb->get_var( "SELECT COUNT(activity_id) FROM {$activity_table} WHERE activity_type = 'course' {$date_query} {$course_query}" );
                break;
            case 'specific_course_completions':
                $label = __( 'Conclusões no Curso Selecionado', 'ld-analytics-widgets' );
                $date_query = self::get_date_where_clause($filters, 'activity_completed');
                if ( !empty($filters['course_id']) && $filters['course_id'] !== 'all' ) {
                    $value = $wpdb->get_var( "SELECT COUNT(activity_id) FROM {$activity_table} WHERE activity_type = 'course' AND activity_status = 1 {$date_query} {$course_query}" );
                } else {
                    $value = 'N/A';
                }
                break;
        }

        return [
            'label' => $label,
            'value' => $value,
            'is_percentage' => in_array($metric, ['completion_rate']),
        ];
    }

    /**
     * Busca dados para gráficos de linha.
     */
    public static function get_line_chart_data( $metric, $filters = [] ) {
        global $wpdb;
        $activity_table = $wpdb->prefix . 'learndash_user_activity';
        
        $date_query = self::get_date_where_clause($filters, 'activity_started');
        $course_query = self::get_course_where_clause($filters, 'course_id');

        $sql = "SELECT DATE(activity_started) as date, COUNT(activity_id) as count 
                FROM {$activity_table} 
                WHERE activity_type = 'course' {$date_query} {$course_query}
                GROUP BY DATE(activity_started)
                ORDER BY date ASC";

        $results = $wpdb->get_results($sql, ARRAY_A);

        $start_date_str = $filters['start_date'] ?? '-30 days';
        $end_date_str = $filters['end_date'] ?? 'today';

        $start_date = \DateTime::createFromFormat('d/m/Y', $start_date_str) ?: new \DateTime($start_date_str);
        $end_date = \DateTime::createFromFormat('d/m/Y', $end_date_str) ?: new \DateTime($end_date_str);
        $end_date->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start_date, $interval, $end_date);

        $labels = [];
        $data_points = [];
        $db_data = array_column($results, 'count', 'date');

        foreach ($period as $date) {
            $formatted_date = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $data_points[] = $db_data[$formatted_date] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => __( 'Novas Inscrições', 'ld-analytics-widgets' ),
                'data' => $data_points,
                'borderColor' => '#3B82F6',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'fill' => true,
                'tension' => 0.4,
            ]]
        ];
    }

    /**
     * Busca dados para gráficos de barra.
     */
    public static function get_bar_chart_data( $metric, $filters = [] ) {
        global $wpdb;
        $activity_table = $wpdb->prefix . 'learndash_user_activity';
        $posts_table = $wpdb->prefix . 'posts';

        $date_query = self::get_date_where_clause($filters, 'ua.activity_started');
        $course_query = self::get_course_where_clause($filters, 'ua.course_id');

        $sql = "SELECT p.post_title, COUNT(ua.user_id) as count 
                FROM {$activity_table} ua
                JOIN {$posts_table} p ON ua.course_id = p.ID
                WHERE ua.activity_type = 'course' {$date_query} {$course_query}
                GROUP BY ua.course_id
                ORDER BY count DESC
                LIMIT 5";
        
        $results = $wpdb->get_results($sql, ARRAY_A);

        return [
            'labels' => array_column($results, 'post_title'),
            'datasets' => [[
                'label' => __( 'Inscrições', 'ld-analytics-widgets' ),
                'data' => array_column($results, 'count'),
                'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
            ]]
        ];
    }

    /**
     * Busca dados para tabelas.
     */
    public static function get_table_data( $metric, $filters = [] ) {
        global $wpdb;
        $users_table = $wpdb->prefix . 'users';
        $activity_table = $wpdb->prefix . 'learndash_user_activity';
        
        $date_query = self::get_date_where_clause($filters, 'ua.activity_started');
        $course_query = self::get_course_where_clause($filters, 'ua.course_id');

        $sql = "SELECT u.ID, u.display_name, u.user_email, MAX(ua.activity_started) as last_activity
                FROM {$users_table} u
                LEFT JOIN {$activity_table} ua ON u.ID = ua.user_id
                WHERE 1=1 {$date_query} {$course_query}
                GROUP BY u.ID
                ORDER BY last_activity DESC";

        $results = $wpdb->get_results($sql, ARRAY_A);
        
        foreach ($results as $key => $row) {
            $results[$key]['course_count'] = $wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(DISTINCT course_id) FROM {$activity_table} WHERE user_id = %d AND activity_type = 'course'", $row['ID'])
            );
        }

        return $results;
    }

    /**
     * NOVA FUNÇÃO: Busca dados para a tabela de cursos mais eficazes.
     */
    public static function get_course_effectiveness_data( $filters = [] ) {
        global $wpdb;
        $activity_table = $wpdb->prefix . 'learndash_user_activity';

        $course_args = [
            'post_type' => 'sfwd-courses',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];
        $courses = get_posts($course_args);
        $course_data = [];

        $date_query_started = self::get_date_where_clause($filters, 'activity_started');
        $date_query_completed = self::get_date_where_clause($filters, 'activity_completed');

        foreach ($courses as $course) {
            $course_id = $course->ID;

            $enrollments_sql = $wpdb->prepare(
                "SELECT COUNT(activity_id) FROM {$activity_table} WHERE activity_type = 'course' AND course_id = %d {$date_query_started}",
                $course_id
            );
            $enrollments = $wpdb->get_var($enrollments_sql);

            $completions_sql = $wpdb->prepare(
                "SELECT COUNT(activity_id) FROM {$activity_table} WHERE activity_type = 'course' AND activity_status = 1 AND course_id = %d {$date_query_completed}",
                $course_id
            );
            $completions = $wpdb->get_var($completions_sql);

            $completion_rate = ($enrollments > 0) ? ($completions / $enrollments) * 100 : 0;

            // Apenas mostra cursos que tiveram pelo menos uma inscrição no período
            if ($enrollments > 0) { 
                $course_data[] = [
                    'course_name'     => $course->post_title,
                    'enrollments'     => (int) $enrollments,
                    'completions'     => (int) $completions,
                    'completion_rate' => round($completion_rate, 2),
                ];
            }
        }

        // Ordena pela taxa de conclusão (do maior para o menor)
        usort($course_data, function($a, $b) {
            return $b['completion_rate'] <=> $a['completion_rate'];
        });

        return $course_data;
    }
    
    /**
     * Busca todos os cursos publicados para usar em filtros.
     */
    public static function get_all_courses() {
        $courses = get_posts([
            'post_type' => 'sfwd-courses',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        
        $options = [];
        if ( ! empty($courses) ) {
            foreach ($courses as $id) {
                $options[$id] = get_the_title($id);
            }
        }
        return $options;
    }

    // --- Funções Auxiliares ---

    private static function get_date_where_clause($filters, $column_name) {
        global $wpdb;
        $start_date_str = $filters['start_date'] ?? null;
        $end_date_str   = $filters['end_date'] ?? null;

        if ( !empty($start_date_str) && !empty($end_date_str) ) {
            $start_date = \DateTime::createFromFormat('d/m/Y', $start_date_str);
            $end_date   = \DateTime::createFromFormat('d/m/Y', $end_date_str);

            if ($start_date && $end_date) {
                return $wpdb->prepare(
                    " AND {$column_name} BETWEEN %s AND %s",
                    $start_date->format('Y-m-d') . ' 00:00:00',
                    $end_date->format('Y-m-d') . ' 23:59:59'
                );
            }
        }
        return '';
    }

    private static function get_course_where_clause($filters, $column_name) {
        global $wpdb;
        $course_id = $filters['course_id'] ?? null;

        if ( !empty($course_id) && $course_id !== 'all' ) {
            return $wpdb->prepare(" AND {$column_name} = %d", $course_id);
        }
        return '';
    }
}
