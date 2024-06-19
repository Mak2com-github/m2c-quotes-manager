<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$tables_to_drop = [
    $wpdb->prefix . 'm2c_quotes',
    $wpdb->prefix . 'm2c_quotes_attachments',
    $wpdb->prefix . 'm2c_quotes_settings'
];

foreach ( $tables_to_drop as $table ) {
    $wpdb->query( "DROP TABLE IF EXISTS $table" );
}