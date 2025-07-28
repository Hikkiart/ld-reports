<?php
namespace LD_Analytics_Widgets\Widgets;

use Elementor\Controls_Manager;
use LD_Analytics_Widgets\Widgets\Base\LD_Analytics_Base_Widget;

// Sair se acedido diretamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Widget de Gráfico de Linhas.
 */
class Widget_Line_Chart extends LD_Analytics_Base_Widget {

    public function get_name() {
        return 'ld_line_chart';
    }

    public function get_title() {
        return __( 'Gráfico de Linhas', 'ld-analytics-widgets' );
    }

    public function get_icon() {
        return 'eicon-line-chart';
    }

    public function get_keywords() {
        return [ 'chart', 'graph', 'line', 'trend', 'learndash' ];
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
                'label' => __( 'Métrica de Tendência', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'enrollments_trend',
                'options' => [
                    'enrollments_trend' => __( 'Tendência de Inscrições', 'ld-analytics-widgets' ),
                ],
            ]
        );

        $this->add_control(
            'chart_title',
            [
                'label' => __( 'Título do Gráfico', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Tendência de Inscrições', 'ld-analytics-widgets' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="ld-analytics-widget ld-chart-widget" data-widget-type="line_chart" data-metric="<?php echo esc_attr( $settings['metric'] ); ?>">
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
