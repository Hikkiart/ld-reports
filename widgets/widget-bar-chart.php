<?php
namespace LD_Analytics_Widgets\Widgets;

use Elementor\Controls_Manager;
use LD_Analytics_Widgets\Widgets\Base\LD_Analytics_Base_Widget;

// Sair se acedido diretamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Widget de Gráfico de Barras.
 */
class Widget_Bar_Chart extends LD_Analytics_Base_Widget {

    public function get_name() {
        return 'ld_bar_chart';
    }

    public function get_title() {
        return __( 'Gráfico de Barras', 'ld-analytics-widgets' );
    }

    public function get_icon() {
        return 'eicon-bar-chart';
    }

    public function get_keywords() {
        return [ 'chart', 'graph', 'bar', 'comparison', 'learndash' ];
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
                'label' => __( 'Métrica de Comparação', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'popular_courses_enrollment',
                'options' => [
                    'popular_courses_enrollment' => __( 'Cursos Mais Populares (por Inscrição)', 'ld-analytics-widgets' ),
                ],
            ]
        );

        $this->add_control(
            'chart_title',
            [
                'label' => __( 'Título do Gráfico', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Top 5 Cursos por Inscrição', 'ld-analytics-widgets' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="ld-analytics-widget ld-chart-widget" data-widget-type="bar_chart" data-metric="<?php echo esc_attr( $settings['metric'] ); ?>">
            <?php if ( ! empty( $settings['chart_title'] ) ) : ?>
                <h3 class="ld-widget-title"><?php echo esc_html( $settings['chart_title'] ); ?></h3>
            <?php endif; ?>
            <div class="ld-chart-container">
                <canvas></canvas>
            </div>
            <div class="ld-widget-loader"></div>
        </div>
        <?php
    }
}
