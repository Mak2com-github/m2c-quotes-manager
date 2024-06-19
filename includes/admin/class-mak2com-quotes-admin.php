<?php
/*
 * Class Mak2com_Quotes
 * Classe Admin du plugin Mak2com Custom Quotes
 * @author Alexandre Celier
 * @version 1.0
 * @since 1.0
 * @link https://mak2com.fr
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 * @package Mak2com Quotes
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
require_once plugin_dir_path( __FILE__ ) . 'class-mak2com-quotes-list-table.php';

class Mak2com_Quotes_Admin {
    private $plugin_name;
    private $version;
    private $deletion_success = false;

    /**
     * Initialise la classe et définit ses propriétés.
     *
     * @param string $plugin_name Le nom du plugin.
     * @param string $version La version du plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('admin_notices', array($this, 'm2c_admin_notices'));
        add_action('wp_ajax_delete_quote', [$this, 'handle_delete_quote']);
    }

    /**
     * Enregistre les fichiers de style pour l'administration.
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../../admin/css/admin-style.css', array(), $this->version, 'all' );
    }

    /**
     * Enregistre les fichiers JavaScript pour l'administration.
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../../admin/js/admin-script.js', array( 'jquery' ), $this->version, false );
    }

    /**
     * Enregistre les pages de menu pour le plugin.
     */
    public function add_plugin_admin_menu() {
        // Ajouter un menu principal pour le plugin
        add_menu_page(
            'Gestion des Devis', // Titre de la page
            'Devis Manager', // Titre du menu
            'manage_options', // Capacité requise pour voir le menu
            'mak2com-quotes-admin', // Slug du menu
            array( $this, 'display_plugin_admin_dashboard' ), // Fonction pour afficher le contenu de la page
            'dashicons-clipboard', // Icône du menu
            6 // Position dans le menu
        );
        add_submenu_page(
            'mak2com-quotes', // Slug du menu parent
            'Détails du Devis', // Titre de la page
            'Détails', // Titre du menu
            'manage_options', // Capacité requise pour voir le menu
            'quote-details', // Slug du menu de la page de détails
            array($this, 'display_quote_details_page') // Fonction pour afficher le contenu de la page
        );
    }


    public function set_deletion_success($value) {
        $this->deletion_success = $value;
    }

    public function get_deletion_success() {
        return $this->deletion_success;
    }

    /**
     * Affiche le tableau de bord principal de l'administration du plugin.
     */
    public function display_plugin_admin_dashboard() {
        $quotesListTable = new Mak2com_Quotes_List_Table($this);
        $quotesListTable->search_box('Rechercher des devis', 'search_id');
        $quotesListTable->prepare_items(); ?>

        <div class="wrap">
            <h1>Gestion des Devis</h1>
            <form method="post">
                <input type="hidden" name="page" value="my_list_test" />
                <?php
                $quotesListTable->search_box('search', 'search_id');
                $quotesListTable->display();
                ?>
            </form>
        </div>
        <?php
    }

    public function display_quote_details_page() {
        $devis_id = isset($_GET['devis_id']) ? intval($_GET['devis_id']) : 0;

        if ($devis_id > 0) {
            global $wpdb;

            $devis = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}m2c_quotes WHERE id = %d", $devis_id));

            if ($devis) {
                $template_path = plugin_dir_path(__FILE__) . '../../admin/partials/admin-quote-single.php';
                if (file_exists($template_path)) {
                    include($template_path);
                } else {
                    echo '<p>Le fichier de template pour les détails du devis est introuvable.</p>';
                }
            } else {
                echo '<p>Devis non trouvé.</p>';
            }
        } else {
            echo '<p>ID de devis invalide.</p>';
        }
    }

    public function handle_delete_quote() {
        check_ajax_referer('delete_quote_nonce');

        if (!current_user_can('delete_posts')) {
            wp_send_json_error(['message' => 'Permissions insuffisantes.']);
        }

        $quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : 0;
        if ($quote_id > 0) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'm2c_quotes';
            $result = $wpdb->delete($table_name, ['id' => $quote_id], ['%d']);
            error_log($result);
            if ($result) {
                wp_send_json_success();
            } else {
                wp_send_json_error(['message' => 'Erreur lors de la suppression de la demande de devis.']);
            }
        } else {
            wp_send_json_error(['message' => 'ID de demande de devis invalide.']);
        }
    }
}