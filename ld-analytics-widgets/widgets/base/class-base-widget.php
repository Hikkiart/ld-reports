<?php
namespace LD_Analytics_Widgets\Widgets\Base;

use Elementor\Widget_Base;

// Sair se acedido diretamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Base Abstrata para os Widgets do LD Analytics.
 * Centraliza funcionalidades comuns como a categoria do widget.
 */
abstract class LD_Analytics_Base_Widget extends Widget_Base {

    /**
     * Obtém as categorias do widget.
     * Define a categoria padrão para todos os widgets que herdam desta classe.
     * @return array Categorias do widget.
     */
    public function get_categories() {
        return [ 'ld-analytics' ];
    }
}
