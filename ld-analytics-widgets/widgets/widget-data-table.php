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
        return [ 'table', 'data', 'report', 'learndash', 'analytics' ];
    }

    /**
     * Registra os controles do widget no Elementor.
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
                'label' => __( 'Métrica da Tabela', 'ld-analytics-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'student_report',
                'options' => [
                    'student_report'      => __( 'Relatório de Alunos', 'ld-analytics-widgets' ),
                    'course_effectiveness' => __( 'Cursos Mais Eficazes', 'ld-analytics-widgets' ),
                ],
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

        // Define as colunas esperadas para cada métrica
        $columns_config = [];
        if ( 'student_report' === $metric ) {
            $columns_config = [
                ['data' => 'display_name', 'title' => esc_html__( 'Aluno', 'ld-analytics-widgets' )],
                ['data' => 'user_email', 'title' => esc_html__( 'Email', 'ld-analytics-widgets' )],
                ['data' => 'course_count', 'title' => esc_html__( 'Cursos', 'ld-analytics-widgets' )],
                ['data' => 'last_activity', 'title' => esc_html__( 'Última Atividade', 'ld-analytics-widgets' )],
            ];
        } elseif ( 'course_effectiveness' === $metric ) {
            $columns_config = [
                ['data' => 'course_name', 'title' => esc_html__( 'Curso', 'ld-analytics-widgets' )],
                ['data' => 'enrollments', 'title' => esc_html__( 'Inscrições', 'ld-analytics-widgets' )],
                ['data' => 'completions', 'title' => esc_html__( 'Concluídos', 'ld-analytics-widgets' )],
                ['data' => 'completion_rate', 'title' => esc_html__( 'Taxa de Conclusão (%)', 'ld-analytics-widgets' )],
            ];
        }

        ?>
        <div class="ld-analytics-widget ld-data-table-widget" data-widget-type="data_table" data-metric="<?php echo esc_attr( $metric ); ?>">
            <?php if ( ! empty( $title ) ) : ?>
                <h3 class="ld-widget-title"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            <div class="ld-data-table-container">
                <table id="<?php echo esc_attr( $table_id ); ?>"
                       class="display responsive nowrap ld-analytics-data-table"
                       data-widget-type="data_table"
                       data-metric="<?php echo esc_attr($metric); ?>"
                       data-columns='<?php echo json_encode($columns_config); ?>'
                       style="width:100%">
                    <thead>
                        <tr>
                            <!-- O cabeçalho será preenchido dinamicamente pelo DataTables com base em data-columns -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- O corpo da tabela será preenchido via AJAX e DataTables -->
                    </tbody>
                </table>
                <div class="ld-analytics-loader"></div> <!-- Loader para indicar carregamento -->
            </div>
        </div>
        <?php
    }

    /**
     * O template de conteúdo é deixado vazio intencionalmente, pois este widget
     * não precisa de uma pré-visualização complexa no editor do Elementor e sua
     * renderização é baseada em dados dinâmicos.
     */
    protected function content_template() {}
}
