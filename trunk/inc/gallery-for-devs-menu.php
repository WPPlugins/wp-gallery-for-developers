<?php

/**
 * Adds plugin admin menu
 * disabled*
 */
function gfdevs_admin_menu() {

    add_menu_page(
        __('WP Gallery for Developers', GALLERY_DEVS_PLUGIN),
        __('WP Gallery', GALLERY_DEVS_PLUGIN),
        'manage_options',
        GALLERY_DEVS_PLUGIN,
        'gfdevs_admin_front_page',
        'dashicons-images-alt2',
        100);
}

/**
 * Adds a sub menu page under plugin admin page.
 */
function gfdevs_sub_menu_settings() {
    add_submenu_page(
        'options-general.php',
        __('WP Gallery Settings', GALLERY_DEVS_PLUGIN),
        __('WP Gallery', GALLERY_DEVS_PLUGIN),
        'manage_options',
        GALLERY_DEVS_PLUGIN.'-settings',
        'gfdevs_admin_settings_page'
    );
}

//add_action('admin_menu', 'gfdevs_admin_menu');
add_action('admin_menu', 'gfdevs_sub_menu_settings');

