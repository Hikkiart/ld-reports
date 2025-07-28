<?php
namespace LD_Analytics_Widgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Sair se acedido diretamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Widget de Filtros Globais do Dashboard do Elementor.
 * Este widget controla os outros widgets de análise na página.
 */
class Widget_Filters extends Widget_Base {

    /**
     * Obtém o nome do widget.
     * @return string Nome do widget.
     */
    public function get_name() {
        return 'ld_filters';
    }

    /**
     * Obtém o título do widget.
     * @return string Título do widget.
     */
    public function get_title() {
        return __( 'Filtros do Dashboard', 'ld-analytics-widgets' );
    }

    /**
     * Obtém o ícone do widget.
     * @return string Ícone do widget.
     */
    public function get_icon() {
        return 'eicon-filter';
    }

    /**
     * Obtém as categorias do widget.
     * @return array Categorias do widget.
     */
    public function get_categories() {
        return [ 'ld-analytics' ];
    }

    /**
     * Obtém as palavras-chave do widget.
     * @return array Palavras-chave do widget.
     */
    public function get_keywords() {
        return [ 'filter', 'filtro', 'learndash', 'analytics', 'data', 'curso' ];
    }

    /**
     * Regista os controlos do widget.
     */
    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Configuração', 'ld-analytics-widgets' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '<strong>' . __( 'Importante:', 'ld-analytics-widgets' ) . '</strong> ' . __( 'Este widget controla todos os outros widgets de Análise do LearnDash nesta página. Posicione-o no topo do seu layout e use apenas uma instância por página.', 'ld-analytics-widgets' ),
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Renderiza o output do widget no frontend.
     */
    protected function render() {
        // Este widget apenas renderiza a estrutura HTML dos filtros.
        // O JavaScript (main.js) irá popular o seletor de cursos e adicionar toda a lógica de eventos.
        ?>
        <div class="ld-analytics-widget ld-analytics-filters">
            <div class="ld-analytics-filters-container">
                <div class="ld-filter-group">
                    <label for="ld-date-start-<?php echo esc_attr($this->get_id()); ?>"><?php echo esc_html__( 'Data de Início', 'ld-analytics-widgets' ); ?></label>
                    <input type="date" class="ld-date-start" id="ld-date-start-<?php echo esc_attr($this->get_id()); ?>" aria-label="<?php esc_attr_e( 'Data de Início', 'ld-analytics-widgets' ); ?>">
                </div>
                <div class="ld-filter-group">
                    <label for="ld-date-end-<?php echo esc_attr($this->get_id()); ?>"><?php echo esc_html__( 'Data de Fim', 'ld-analytics-widgets' ); ?></label>
                    <input type="date" class="ld-date-end" id="ld-date-end-<?php echo esc_attr($this->get_id()); ?>" aria-label="<?php esc_attr_e( 'Data de Fim', 'ld-analytics-widgets' ); ?>">
                </div>
                <div class="ld-filter-group">
                    <label for="ld-course-filter-<?php echo esc_attr($this->get_id()); ?>"><?php echo esc_html__( 'Curso', 'ld-analytics-widgets' ); ?></label>
                    <select class="ld-course-filter" id="ld-course-filter-<?php echo esc_attr($this->get_id()); ?>" aria-label="<?php esc_attr_e( 'Filtro de Curso', 'ld-analytics-widgets' ); ?>">
                        <option value="all"><?php echo esc_html__( 'Todos os Cursos', 'ld-analytics-widgets' ); ?></option>
                        <!-- Os cursos serão adicionados aqui via AJAX -->
                    </select>
                </div>
                <div class="ld-filter-group">
                    <button type="button" id="ld-apply-filters" class="elementor-button elementor-size-sm">
                        <?php echo esc_html__( 'Aplicar Filtros', 'ld-analytics-widgets' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * O template de conteúdo é deixado vazio intencionalmente, pois este widget
     * não precisa de uma pré-visualização complexa no editor do Elementor e sua
     * funcionalidade principal depende do JavaScript no frontend.
     */
    protected function content_template() {}
}
