<?php
/*
Plugin Name: Fix Charset
Description: Fixes charset issues in the database.
Version: 1.0
*/

function fix_charset_issues() {
    global $wpdb;
    $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);

    foreach ($tables as $table) {
        $table_name = $table[0];
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");

        foreach ($columns as $column) {
            $column_name = $column->Field;
            $column_type = $column->Type;

            if (strpos($column_type, 'char') !== false || strpos($column_type, 'text') !== false) {
                $wpdb->query("UPDATE $table_name SET $column_name = CONVERT(BINARY CONVERT($column_name USING latin1) USING utf8mb4) WHERE 1");
            }
        }
    }

    echo "Character set conversion complete.";
}

add_action('admin_menu', function() {
    add_menu_page('Fix Charset', 'Fix Charset', 'manage_options', 'fix-charset', 'fix_charset_issues');
});
?>