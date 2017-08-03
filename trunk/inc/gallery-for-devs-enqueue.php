<?php

/**
 * Enqueue scripts
 */
function gfdevs_enqueue_admin_style() {

    // Styles
    wp_register_style('gfdevs-admin-css', plugin_dir_url( __FILE__ ) . '../css/admin.css', false, '1.0.0');
    wp_enqueue_style('gfdevs-admin-css');

    // jQuery UI JS
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-tabs' );

    // Plugin JS
    wp_enqueue_script('gfdevs-admin-css', plugin_dir_url( __FILE__ ) . '../js/admin.js');

}

add_action( 'admin_enqueue_scripts', 'gfdevs_enqueue_admin_style' );