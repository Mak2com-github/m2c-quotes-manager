<?php
class Mak2com_Quotes_Activator {

    /**
     * A function to activate and create necessary tables for the plugin to work.
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Création de la table principale des devis
        $sql_quotes = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}m2c_quotes (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        userId bigint(20) UNSIGNED NULL,
        datas longtext NOT NULL,
        status varchar(20) NOT NULL,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (userId) REFERENCES {$wpdb->prefix}users(ID) ON DELETE SET NULL
    ) $charset_collate;";

        // Création de la table des attachements
        $sql_attachments = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}m2c_quotes_attachments (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        quote_id mediumint(9) NOT NULL,
        files longtext NOT NULL,
        user_id bigint(20) UNSIGNED NOT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (quote_id) REFERENCES {$wpdb->prefix}m2c_quotes(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
    ) $charset_collate;";

        // Création de la table des réglages
        $sql_settings = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}m2c_quotes_settings (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NULL DEFAULT '',
        value longtext NULL,
        auto_load varchar(20) NULL DEFAULT 'yes',
        PRIMARY KEY (id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_quotes);
        dbDelta($sql_attachments);
        dbDelta($sql_settings);
    }
}
