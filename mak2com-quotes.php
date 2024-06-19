<?php
/**
 * Plugin Name: Mak2com Quotes
 * Plugin URI: https://mak2com.fr
 * Description: Gestion de devis personnalisés, plugin créé pour et par l'agence Mak2com
 * Version: 1.0
 * Author: Alexandre Celier
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mak2com-quotes
 */

// Si ce fichier est appelé directement, alors terminer l'exécution.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Définit le chemin et l'URL du plugin.
define( 'MAK2COM_QUOTES_VERSION', '1.0' );
define( 'MAK2COM_QUOTES_PATH', plugin_dir_path( __FILE__ ) );
define( 'MAK2COM_QUOTES_URL', plugin_dir_url( __FILE__ ) );

// L'activation et la désactivation du plugin.
register_activation_hook( __FILE__, 'activate_mak2com_quotes' );
register_deactivation_hook( __FILE__, 'deactivate_mak2com_quotes' );

/**
 * Activates the mak2com quotes.
 *
 */
function activate_mak2com_quotes(): void
{
    require_once MAK2COM_QUOTES_PATH . 'includes/class-mak2com-quotes-activator.php';
    Mak2com_Quotes_Activator::activate();
}

/**
 * Deactivates the Mak2com quotes feature.
 *
 * @throws void No return value
 */
function deactivate_mak2com_quotes(): void
{
    require_once MAK2COM_QUOTES_PATH . 'includes/class-mak2com-quotes-deactivator.php';
    Mak2com_Quotes_Deactivator::deactivate();
}

// Main plugin Class include
require MAK2COM_QUOTES_PATH . 'includes/class-mak2com-quotes.php';

add_action('wp_ajax_change_status', 'mak2com_change_status');
function mak2com_change_status() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Vous n\'avez pas l\'autorisation d\'accéder à cette page.' );
    }
    if ( ! isset( $_GET['quote_id'], $_GET['status'], $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'change_status_nonce' ) ) {
        wp_redirect( admin_url( 'admin.php?page=mak2com_quotes&status_change=error' ) );
        exit;
    }

    $quote_id = intval( $_GET['quote_id'] );
    $new_status = sanitize_text_field( $_GET['status'] );

    global $wpdb;
    $table_name = $wpdb->prefix . 'mak2com_quotes';

    $updated = $wpdb->update(
        $table_name,
        array( 'status' => $new_status ),
        array( 'id' => $quote_id ),
        array( '%s' ),
        array( '%d' )
    );

    if ( $updated !== false ) {
        wp_redirect( admin_url( 'admin.php?page=mak2com_quotes-admin&status_change=success' ) );
        exit;
    } else {
        wp_redirect( admin_url( 'admin.php?page=mak2com_quotes-admin&status_change=error' ) );
        exit;
    }
}

function mak2com_admin_notices() {
    if ( isset( $_GET['status_change'] ) ) {
        $status_change = sanitize_text_field( $_GET['status_change'] );
        if ( $status_change == 'success' ) {
            echo '<div class="notice notice-success is-dismissible">
                    <p>Le statut de la demande a été modifié avec succès.</p>
                  </div>';
        } elseif ( $status_change == 'error' ) {
            echo '<div class="notice notice-error is-dismissible">
                    <p>Une erreur est survenue lors de la modification du statut de la demande.</p>
                  </div>';
        }
    }
}
add_action( 'admin_notices', 'mak2com_admin_notices' );

/**
 * Display the admin page for custom quotes.
 */
function mak2com_quotes_display_admin_page(): void
{
    include_once plugin_dir_path( __FILE__ ) . 'admin/partials/admin-display.php';
}