<?php
/**
 * Plugin Name:       LD Analytics Widgets
 * Plugin URI:        https://github.com/seu-usuario/ld-analytics-widgets
 * Description:       Fornece um conjunto de widgets do Elementor para exibir métricas e análises do LearnDash com dados reais.
 * Version:           1.0.0
 * Author:            Seu Nome & Gemini
 * Author URI:        https://seusite.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ld-analytics-widgets
 * Domain Path:       /languages
 * Elementor tested up to: 3.20.0
 * Elementor Pro tested up to: 3.20.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define constantes úteis
define( 'LD_ANALYTICS_WIDGETS_VERSION', '1.0.0' );
define( 'LD_ANALYTICS_WIDGETS_FILE', __FILE__ );
define( 'LD_ANALYTICS_WIDGETS_PATH', plugin_dir_path( LD_ANALYTICS_WIDGETS_FILE ) );
define( 'LD_ANALYTICS_WIDGETS_URL', plugin_dir_url( LD_ANALYTICS_WIDGETS_FILE ) );

// Inclui a classe principal do plugin
require_once LD_ANALYTICS_WIDGETS_PATH . 'includes/class-plugin.php';

/**
 * Inicia o plugin.
 *
 * Garante que o plugin seja carregado apenas uma vez e só depois que todos os
 * plugins necessários (como o Elementor) já foram carregados.
 */
function ld_analytics_widgets_run() {
    // Verifica se o LearnDash e o Elementor estão ativos
    if ( ! defined( 'LEARNDASH_VERSION' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p><strong>LD Analytics Widgets:</strong> O plugin LearnDash LMS é necessário e não está ativo.</p></div>';
        });
        return;
    }

    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p><strong>LD Analytics Widgets:</strong> O plugin Elementor é necessário e não está ativo.</p></div>';
        });
        return;
    }

    // Inicia a classe principal do plugin
    \LD_Analytics_Widgets\Plugin::instance();
}
add_action( 'plugins_loaded', 'ld_analytics_widgets_run' );
