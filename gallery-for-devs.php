<?php
/**
 * Plugin Name: WP Gallery for Developers
 * Plugin URI: http://lapuvieta.lv
 * Description: A plugin to add gallery metabox to post types for theme developers to easily display list of images on posts, pages or any post type you like.
 * Version: 1.1
 * Author: Janis Itkacs (janis@fwsx.co)
 * Author URI: http://lapuvieta.lv
 * License: GPL2
 */

if (!defined( 'ABSPATH')) {
    exit;
} // Exit if accessed directly

define('GALLERY_DEVS_PLUGIN', 'gallery-for-devs');

// WP Backend
if (is_admin()) :
    // Helpers
    require_once "inc/gallery-for-devs-helpers.php";

    // Admin pages and settings
    require_once "inc/gallery-for-devs-menu.php";
    require_once "inc/gallery-for-devs-admin-settings-page.php";
    require_once "inc/gallery-for-devs-setting-fields.php";

    // Scripts and WP Gallery meta box
    require_once "inc/gallery-for-devs-enqueue.php";
    require_once "inc/gallery-for-devs-metabox.php";

    // API
    require_once "inc/gallery-for-devs-api-flickr.php";
endif;

// WP Frontend
require_once "inc/gallery-for-devs-frontend.php";
require_once "inc/gallery-for-devs-shortcodes.php";