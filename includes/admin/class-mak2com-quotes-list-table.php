<?php
/*
 * Class Mak2com_Quotes List Table class for displaying a list of quotes in the admin area.
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

class Mak2com_Quotes_List_Table extends WP_List_Table {
    private $admin;

    public function __construct($admin_instance) {
        $this->admin = $admin_instance;
        parent::__construct([
            'singular' => __('Devis', 'mak2com-quotes'),
            'plural'   => __('Devis', 'mak2com-quotes'),
            'ajax'     => false // does this table support ajax?
        ]);
    }

    /**
     * Retrieve quotes data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_quotes($per_page = 5, $page_number = 1) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}m2c_quotes";
        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY created_at DESC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Delete a quote record.
     *
     * @param int $id quote ID
     */
    public static function delete_quote($id) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}m2c_quotes",
            ['id' => $id],
            ['%d']
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Supprimer'
        );
        return $actions;
    }

    public function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural'])) {
                die('Go get a life script kiddies');
            }
            else {
                if (isset($_POST['bulk-delete'])) {
                    $delete_ids = esc_sql($_POST['bulk-delete']);
                    $deletion_made = false;
                    foreach ($delete_ids as $id) {
                        self::delete_quote($id);
                        $this->admin->set_deletion_success(true);
                        $deletion_made = true;
                    }
                    if ($deletion_made) {
                        wp_redirect(add_query_arg('deletion_success', 'true', $_SERVER['REQUEST_URI']));
                        exit;
                    }
                }
            }
        }
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}m2c_quotes";

        return $wpdb->get_var($sql);
    }

    /**
     * Text displayed when no quote data is available
     */
    public function no_items() {
        _e('Aucun devis disponible.', 'mak2com-quotes');
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'nom':
            case 'prenom':
            case 'association' :
            case 'email' :
            case 'telephone' :
                $datas = json_decode($item['datas'], true);
                if ($datas && is_array($datas)) {
                    return isset($datas[$column_name]) ? esc_html($datas[$column_name]) : '';
                }
                return '';
            case 'status':
                return $this->render_status_button($item);
            case 'created_at' :
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $item[$column_name]);
                return $date ? $date->format('d/m/Y à H:i') : '';
            case 'actions':
                $view_url = admin_url('admin.php?page=mak2com_quotes_view&quote_id=' . $item['id']);
                $delete_url = admin_url('admin-ajax.php?action=delete_quote&quote_id=' . $item['id'] . '&_wpnonce=' . wp_create_nonce('delete_quote_nonce'));
                return sprintf(
                    '<a href="%s" class="button">Voir la demande</a> <a href="%s" class="button delete-quote-button">Supprimer</a>',
                    esc_url($view_url),
                    esc_url($delete_url)
                );
            default:
                return print_r($item, true);
        }
    }

    function render_status_button($item) {
        $status = $item['status'];
        $next_status = $this->get_next_status($status);
        $button_label = $this->get_status_button_label($status);
        $nonce = wp_create_nonce('change_status_nonce');
        $url = admin_url('admin-ajax.php?action=change_status&quote_id=' . $item['id'] . '&status=' . $next_status . '&_wpnonce=' . $nonce);

        return sprintf(
            '<p>'. $item['status'] .'</p><a href="%s" class="button">%s</a>',
            esc_url($url),
            esc_html($button_label)
        );
    }

    function get_next_status($status) {
        switch ($status) {
            case 'en attente':
                return 'en cours';
            case 'en cours':
                return 'terminé';
            case 'terminé':
                return 'en attente';
            default:
                return 'en attente';
        }
    }

    function get_status_button_label($status) {
        switch ($status) {
            case 'en attente':
                return 'Passer à en cours';
            case 'en cours':
                return 'Passer à terminé';
            case 'terminé':
                return 'Repasser à en attente';
            default:
                return 'Changer le statut';
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     *  Define the columns that are going to be used in the table
     *
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns() {
        $columns = [
            'cb'         => '<input type="checkbox" />',
            'nom' => __('Nom', 'mak2com-quotes'),
            'prenom'   => __('Prénom', 'mak2com-quotes'),
            'association'   => __('Association', 'mak2com-quotes'),
            'email'   => __('Mail', 'mak2com-quotes'),
            'telephone'   => __('Téléphone', 'mak2com-quotes'),
            'status'     => __('Statut', 'mak2com-quotes'),
            'created_at'     => __('Date de creation', 'mak2com-quotes'),
            'details'  =>  __('Actions', 'mak2com-quotes'),
        ];

        return $columns;
    }

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    public function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();
        $this->process_bulk_action();
//        $this->set_pagination_args('');

        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page     = $this->get_items_per_page('quotes_per_page', 25);
        $current_page = $this->get_pagenum();
        $total_items  = $this->get_total_items_count();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page
        ]);

        $this->items = self::get_quotes($per_page, $current_page);
    }

    function get_total_items_count() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'm2c_quotes';
        $sql = "SELECT COUNT(*) FROM $table_name";
        return $wpdb->get_var($sql);
    }

    protected function column_details($item) {
        $details_url = admin_url('admin.php?page=quote-details&devis_id=' . $item['id']);
        $delete_url = admin_url('admin-ajax.php?action=delete_quote&quote_id=' . $item['id'] . '&_wpnonce=' . wp_create_nonce('delete_quote_nonce'));

        return sprintf(
            '<a href="%s" class="button">Voir la demande</a> <a href="%s" class="button delete-quote-button">Supprimer</a>',
            esc_url($details_url),
            esc_url($delete_url));
    }
}
