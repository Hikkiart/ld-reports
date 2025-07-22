<?php
namespace LD_Analytics_Widgets\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Sair se acedido diretamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Widget de Tabela de Dados do Elementor.
 * Cria uma tabela interativa usando DataTables.js para exibir dados do LearnDash.
 */
class Widget_Data_Table extends Widget_Base {

    /**
     * Obtém o nome do widget.
     * @return string Nome do widget.
     */
    public function get_name() {
        return 'ld_data_table';
    }

    /**
     * Obtém o título do widget.
     * @return string Título do widget.
     */
    public function get_title() {
        return __( 'Tabela de Dados', 'ld-analytics-widgets' );
    }

    /**
     * Obtém o ícone do widget.
     * @return string Ícone do widget.
     */
    public function get_icon() {
        return 'eicon-table';
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
        return [ 'table', 'tabela', 'learndash', 'analytics', 'dados', 'alunos', 'relatorio' ];
    }

    /**
     * Regista os controlos do widget.
     */
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
                'label' => __( 'Tipo de Dados da Tabela', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'student_list',
                'options' => [
                    'student_list' => __( 'Lista de Alunos', 'ld-analytics-widgets' ),
                    'course_effectiveness' => __( 'Cursos Mais Eficazes', 'ld-analytics-widgets' ),
                    // Futuramente: 'course_progress_list' => __( 'Progresso por Curso', 'ld-analytics-widgets' ),
                ],
                'description' => __( 'Selecione o conjunto de dados a ser exibido nesta tabela.', 'ld-analytics-widgets' ),
            ]
        );

        $this->add_control(
            'table_title',
            [
                'label' => __( 'Título da Tabela', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Relatório de Alunos', 'ld-analytics-widgets' ),
                'placeholder' => __( 'Digite o título da sua tabela', 'ld-analytics-widgets' ),
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Renderiza o output do widget no frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $metric = $settings['metric'];
        $title = $settings['table_title'];
        // Gera um ID único para cada instância da tabela para evitar conflitos de JavaScript.
        $table_id = 'ld-data-table-' . $this->get_id();

        ?>
        <div class="ld-analytics-widget ld-data-table-widget" data-widget-type="data_table" data-metric="<?php echo esc_attr( $metric ); ?>">
            <?php if ( ! empty( $title ) ) : ?>
                <h3 class="ld-widget-title"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            <div class="ld-data-table-container">
                <table id="<?php echo esc_attr( $table_id ); ?>" class="display responsive nowrap" style="width:100%">
                    <!-- O cabeçalho e o corpo da tabela serão preenchidos dinamicamente pelo DataTables.js -->
                </table>
            </div>
            <div class="ld-widget-loader"></div>
        </div>
        <?php
    }
}
