<?php
/*
 * Class Mak2com_Quotes_Public
 * Classe Public du plugin Mak2com Custom Quotes
 * @author Alexandre Celier
 * @version 1.0
 * @since 1.0
 * @link https://mak2com.fr
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 * @package Mak2com Quotes
 */

class Mak2com_Quotes_Public {

    private $plugin_name;
    private $version;

    /**
     * Initialise la classe et définit ses propriétés.
     *
     * @param string $plugin_name Le nom du plugin.
     * @param string $version La version du plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->setup_hooks();
    }

    /**
     * Enregistre les fichiers de style pour le front-end.
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../../public/css/public-style.css', array(), $this->version, 'all' );
    }

    /**
     * Enregistre les fichiers JavaScript pour le front-end.
     */
    public function enqueue_scripts(): void
    {
        wp_enqueue_script( $this->plugin_name . '-script', plugin_dir_url( __FILE__ ) . '../../public/js/public-script.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . '-ajax', plugin_dir_url( __FILE__ ) . '../../public/js/public-ajax.js', array( 'jquery' ), $this->version, true );
        wp_localize_script( $this->plugin_name . '-ajax', 'mak2comQuotesAjax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'my_ajax_nonce' ) ) );
    }

    public function setup_hooks() {
        add_action('wp_ajax_process_quote_form', array($this, 'process_quote_submission'));
        add_action('wp_ajax_nopriv_process_quote_form', array($this, 'process_quote_submission'));
    }

    private function load_email_template($template_path, $vars = array()): bool|string {
        if (file_exists($template_path)) {
            extract($vars);
            ob_start();
            include $template_path;
            $content = ob_get_clean();
            return $content;
        } else {
            error_log("Template file not found: $template_path");
            return '';
        }
    }

    public function process_quote_submission() {
        check_ajax_referer('my_ajax_nonce', 'security');

        if (empty($_POST['devisItems'])) {
            wp_send_json_error(array('message' => 'Les détails des devis sont manquants.'));
            return;
        }
        $devisItems = json_decode(stripslashes($_POST['devisItems']), true);
        if (!is_array($devisItems)) {
            wp_send_json_error(array('message' => 'Format des données de devis invalide.'));
            return;
        }
        if (empty($_POST['data'])) {
            wp_send_json_error(array('message' => 'Les données du formulaire sont manquantes.'));
            return;
        }

        $allowedKeys = ['uniqueId', 'productId', 'productName', 'quantity', 'imageUrl', 'link', 'options'];

        foreach ($devisItems as $item) {
            foreach (array_keys($item) as $key) {
                if (!in_array($key, $allowedKeys)) {
                    wp_send_json_error(array('message' => 'Clés non autorisées trouvées dans les données de devis.'));
                    return;
                }
            }
            if (!isset($item['productId'], $item['productName'], $item['quantity']) ||
                !is_numeric($item['productId']) || !is_numeric($item['quantity'])) {
                wp_send_json_error(array('message' => 'Données de devis incorrectes parmi productId, productName et quantity.'));
                return;
            }
            if (isset($item['options']) && is_array($item['options'])) {
                foreach ($item['options'] as $key => $value) {
                    if (!is_string($value)) {
                        wp_send_json_error(array('message' => 'Format invalide pour les options de devis.'));
                        return;
                    }
                    $item['options'][$key] = sanitize_text_field($value);
                }
            } else {
                wp_send_json_error(array('message' => 'Données de devis incorrectes.'));
                return;
            }
        }

        parse_str($_POST['data'], $form_data);
        $form_data['devisItems'] = $devisItems;
        $formDataJson = wp_json_encode($form_data);
        $userId = get_current_user_id() ?: null;

        global $wpdb;

        $table_name = $wpdb->prefix . 'm2c_quotes';
        $data = array(
            'userId' => $userId,
            'datas' => $formDataJson,
            'status' => 'en attente',
        );
        $format = array('%d', '%s', '%s');
        $wpdb->insert($table_name, $data, $format);

        $summaryEmail = sanitize_email($form_data['email']);
        $summarySubject = 'Confirmation de votre demande de devis';
        $summaryMessage = $this->load_email_template(plugin_dir_path(__FILE__) . '../../admin/emails/email-template-summary.php', [
            'form_data' => $form_data]
        );
        $summaryHeaders = [
            'Content-Type: text/html; charset=UTF-8',
            'From: BideanTrail <no-reply@bideantrail.com>',
            'Reply-To: BideanTrail <no-reply@bideantrail.com>',
            'Bcc: alexandre@mak2com.fr'
        ];
        if ($summaryEmail) {
            error_log(print_r($form_data, true));
            $summaryReturn = wp_mail($summaryEmail, $summarySubject, $summaryMessage, $summaryHeaders);
            if (!$summaryReturn) {
                error_log('Une erreur est survenue sur l\'envoi de la notification à l\'utilisateur.');
                wp_send_json_error(array('message' => 'Une erreur est survenue.'));
                return;
            } else {
                error_log('Notification d\'email de récap envoyée à l\'utilisateur.');
            }
        }

        $adminEmail = get_option('admin_email');
        $adminSubject = 'Nouvelle demande de devis reçue';
        $adminMessage = $this->load_email_template(plugin_dir_path(__FILE__) . '../../admin/emails/email-template-summary.php', [
                'form_data' => $form_data]
        );
        $adminHeaders = [
            'Content-Type: text/html; charset=UTF-8',
            'From: BideanTrail <no-reply@bideantrail.com>',
            'Reply-To: ' . sanitize_text_field($form_data['prenom']) . ' ' . sanitize_text_field($form_data['nom']) . ' <' . $summaryEmail . '>',
            'Bcc: alexandre@mak2com.fr'
        ];
        if ($adminEmail) {
            $adminReturn = wp_mail($adminEmail, $adminSubject, $adminMessage, $adminHeaders);
            if (!$adminReturn) {
                error_log('Une erreur est survenue sur l\'envoi de la notification à l\'admin.');
                wp_send_json_error(array('message' => 'Une erreur est survenue.'));
                return;
            } else {
                error_log('Notification d\'email à l\'admin envoyée.');
            }
        }

        wp_send_json_success(array('message' => 'Votre demande de devis a été soumise avec succès.'));
    }

    /**
     * Méthodes supplémentaires pour la logique spécifique au front-end.
     */
    public function display_quote_form() {
        // Logique pour afficher un formulaire de devis.
    }

    public function quote_popup_shortcode() {
        ob_start();
        include MAK2COM_QUOTES_PATH . 'public/partials/components/quote-popup.php';
        return ob_get_clean();
    }

    public function quote_form_shortcode() {
        ob_start();
        include MAK2COM_QUOTES_PATH . 'public/partials/components/quote-form.php';
        return ob_get_clean();
    }
}