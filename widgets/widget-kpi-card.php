<?php
namespace LD_Analytics_Widgets\Widgets;

use Elementor\Controls_Manager;
use LD_Analytics_Widgets\Widgets\Base\LD_Analytics_Base_Widget;

// Sair se acedido diretamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Widget de Cartão de KPI (Key Performance Indicator).
 */
class Widget_KPI_Card extends LD_Analytics_Base_Widget {

    public function get_name() {
        return 'ld_kpi_card';
    }

    public function get_title() {
        return __( 'Cartão de KPI', 'ld-analytics-widgets' );
    }

    public function get_icon() {
        return 'eicon-number-field';
    }

    public function get_keywords() {
        return [ 'kpi', 'learndash', 'analytics', 'card', 'metric' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Conteúdo', 'ld-analytics-widgets' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'metric',
            [
                'label' => __( 'Métrica a Exibir', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'active_students',
                // CORREÇÃO: Adicionadas as novas métricas e corrigidos os nomes para corresponder ao Data_Provider.
                'options' => [
                    'active_students'             => __( 'Alunos Ativos (no período)', 'ld-analytics-widgets' ),
                    'total_enrollments'           => __( 'Total de Inscrições', 'ld-analytics-widgets' ),
                    'completed_courses'           => __( 'Cursos Concluídos (Geral)', 'ld-analytics-widgets' ),
                    'completion_rate'             => __( 'Taxa de Conclusão Média', 'ld-analytics-widgets' ),
                    'specific_course_completions' => __( 'Conclusões de Curso Específico', 'ld-analytics-widgets' ),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $metric = $settings['metric'];
        ?>
        <div class="ld-analytics-widget ld-kpi-card" data-widget-type="kpi_card" data-metric="<?php echo esc_attr( $metric ); ?>">
            <div class="ld-kpi-card-inner">
                <div class="ld-kpi-label">--</div>
                <div class="ld-kpi-value">...</div>
            </div>
            <div class="ld-widget-loader"></div>
        </div>
        <?php
    }
}
