<?php
/**
 * Admin Dashboard View.
 * Renders the main dashboard page with all the widgets.
 *
 * @package    LD_Analytics_Widgets
 * @subpackage LD_Analytics_Widgets/admin/views
 * @author     Hikki Art <support@hikki.art>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap ld-analytics-dashboard">
    <h1><?php _e( 'Dashboard LearnDash', 'ld-analytics-widgets' ); ?></h1>

    <!-- Filtros -->
    <div class="ld-analytics-widget filter-widget">
        <div class="widget-content">
            <?php
            // Instancia e renderiza o widget de filtros
            $filters_widget = new \LD_Analytics_Widgets\Widget\Filters_Widget();
            $filters_widget->render();
            ?>
        </div>
    </div>

    <!-- Métricas de Saúde Geral da Plataforma -->
    <h2 class="dashboard-section-title"><?php _e( 'Saúde Geral da Plataforma', 'ld-analytics-widgets' ); ?></h2>
    <div class="widgets-grid kpi-grid">
        <!-- Alunos Ativos -->
        <div class="ld-analytics-widget kpi-card-widget" data-widget-type="kpi_card" data-metric="active_students">
            <div class="widget-header">
                <h3 class="widget-title"><?php _e( 'Alunos Ativos', 'ld-analytics-widgets' ); ?></h3>
            </div>
            <div class="widget-content">
                <div class="ld-analytics-loader"></div>
            </div>
        </div>

        <!-- Total de Inscrições (Nova Métrica) -->
        <div class="ld-analytics-widget kpi-card-widget" data-widget-type="kpi_card" data-metric="total_enrollments">
            <div class="widget-header">
                <h3 class="widget-title"><?php _e( 'Total de Inscrições', 'ld-analytics-widgets' ); ?></h3>
            </div>
            <div class="widget-content">
                <div class="ld-analytics-loader"></div>
            </div>
        </div>

        <!-- Cursos Concluídos (Geral) -->
        <div class="ld-analytics-widget kpi-card-widget" data-widget-type="kpi_card" data-metric="completed_courses">
            <div class="widget-header">
                <h3 class="widget-title"><?php _e( 'Cursos Concluídos', 'ld-analytics-widgets' ); ?></h3>
            </div>
            <div class="widget-content">
                <div class="ld-analytics-loader"></div>
            </div>
        </div>

        <!-- Taxa Média de Conclusão (Nova Métrica) -->
        <div class="ld-analytics-widget kpi-card-widget" data-widget-type="kpi_card" data-metric="completion_rate">
            <div class="widget-header">
                <h3 class="widget-title"><?php _e( 'Taxa Média de Conclusão', 'ld-analytics-widgets' ); ?></h3>
            </div>
            <div class="widget-content">
                <div class="ld-analytics-loader"></div>
            </div>
        </div>
    </div>

    <!-- Conclusões de Curso Específico (Nova Métrica) -->
    <div class="widgets-grid kpi-grid single-kpi">
         <div class="ld-analytics-widget kpi-card-widget" data-widget-type="kpi_card" data-metric="specific_course_completions">
            <div class="widget-header">
                <h3 class="widget-title"><?php _e( 'Conclusões no Curso Selecionado', 'ld-analytics-widgets' ); ?></h3>
            </div>
            <div class="widget-content">
                <div class="ld-analytics-loader"></div>
            </div>
        </div>
    </div>


    <!-- Gráfico de Tendências -->
    <div class="ld-analytics-widget chart-widget" data-widget-type="line_chart" data-metric="enrollment_trends">
        <div class="widget-header">
            <h3 class="widget-title"><?php _e( 'Tendência de Inscrições', 'ld-analytics-widgets' ); ?></h3>
        </div>
        <div class="widget-content">
            <div class="ld-analytics-loader"></div>
        </div>
    </div>

    <!-- Análise de Cursos -->
    <h2 class="dashboard-section-title"><?php _e( 'Análise de Cursos', 'ld-analytics-widgets' ); ?></h2>
    <div class="ld-analytics-widget chart-widget" data-widget-type="bar_chart" data-metric="top_courses_by_enrollment">
        <div class="widget-header">
            <h3 class="widget-title"><?php _e( 'Top 5 Cursos por Inscrição', 'ld-analytics-widgets' ); ?></h3>
        </div>
        <div class="widget-content">
            <div class="ld-analytics-loader"></div>
        </div>
    </div>
    
    <!-- NOVO WIDGET: Tabela de Cursos Mais Eficazes -->
<div class="ld-analytics-widget table-widget" data-widget-type="data_table" data-metric="course_effectiveness">
    <div class="widget-header">
        <h3 class="widget-title"><?php _e( 'Cursos Mais Eficazes (por Taxa de Conclusão)', 'ld-analytics-widgets' ); ?></h3>
    </div>
    <div class="widget-content">
        <div class="ld-analytics-loader"></div>
    </div>
</div>

    <!-- Desempenho dos Alunos -->
    <h2 class="dashboard-section-title"><?php _e( 'Desempenho dos Alunos', 'ld-analytics-widgets' ); ?></h2>
    <div class="ld-analytics-widget table-widget" data-widget-type="data_table" data-metric="student_report">
        <div class="widget-header">
            <h3 class="widget-title"><?php _e( 'Relatório de Alunos', 'ld-analytics-widgets' ); ?></h3>
        </div>
        <div class="widget-content">
            <div class="ld-analytics-loader"></div>
        </div>
    </div>

</div>
