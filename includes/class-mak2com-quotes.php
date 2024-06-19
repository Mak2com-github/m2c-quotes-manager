<?php
/*
 * Class Mak2com_Quotes
 * Classe principale du plugin Mak2com Custom Quotes
 * @author Alexandre Celier
 * @version 1.0
 * @since 1.0
 * @link https://mak2com.fr
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 * @package Mak2com Quotes
 */

class Mak2com_Quotes {

    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the dependencies required for the PHP function.
     */
    private function load_dependencies() {
        require_once plugin_dir_path( __FILE__ ) . 'admin/class-mak2com-quotes-admin.php';
        require_once plugin_dir_path( __FILE__ ) . 'public/class-mak2com-quotes-public.php';
    }

    /**
     * Define admin hooks for the PHP function.
     */
    private function define_admin_hooks() {
        $plugin_admin = new Mak2com_Quotes_Admin( 'mak2com-custom-quotes', '1.0' );
        add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $plugin_admin, 'add_plugin_admin_menu' ) );
    }


    private function define_public_hooks() {
        $plugin_public = new Mak2com_Quotes_Public( 'mak2com-custom-quotes', '1.0' );
        add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts' ) );
        // Shortcodes
        add_shortcode( 'quote_popup', array( $plugin_public, 'quote_popup_shortcode' ) );
        add_shortcode( 'quote_form', array( $plugin_public, 'quote_form_shortcode' ) );
    }


    public function run() {
        // Ici, vous pouvez ajouter d'autres hooks ou logique d'exécution.
    }

}
$mak2com_quotes = new Mak2com_Quotes();
$mak2com_quotes->run();