<?php

/**
 * Options pages
 */

add_action('admin_init', 'gfdevs_options_fields');

function gfdevs_options_fields() {

    /**
     * Sections
     */

    add_settings_section(
        'gfdevs_settings_post_types_section',       // section
        __('Registered Post Types', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_post_types_callback',
        'gfdevs_settings_post_types_page'           // page
    );

    add_settings_section(
        'gfdevs_settings_other_sources_section',       // section
        __('Gallery Service Providers', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_other_sources_callback',
        'gfdevs_settings_other_sources_page'           // page
    );

    add_settings_section(
        'gfdevs_settings_flickr_section',       // section
        __('Flickr Settings', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_flickr_callback',
        'gfdevs_settings_flickr_page'           // page
    );

    /**
     * Fields
     */

    add_settings_field(
        'gfdevs_options_post_types',                // id
        __('Post Types', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_post_types_fields_callback',
        'gfdevs_settings_post_types_page',          // page
        'gfdevs_settings_post_types_section'        // section
    );

    add_settings_field(
        'gfdevs_options_providers',                     // id
        __('Providers', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_other_sources_fields_callback',
        'gfdevs_settings_other_sources_page',          // page
        'gfdevs_settings_other_sources_section'        // section
    );

    add_settings_field(
        'gfdevs_options_flickr_username',                   // id
        __('Flickr Username', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_flickr_username_fields_callback',
        'gfdevs_settings_flickr_page',          // page
        'gfdevs_settings_flickr_section'        // section
    );
    
    add_settings_field(
        'gfdevs_options_flickr_key',                   // id
        __('Flickr API Key', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_flickr_key_fields_callback',
        'gfdevs_settings_flickr_page',          // page
        'gfdevs_settings_flickr_section'        // section
    );

    add_settings_field(
        'gfdevs_options_flickr_secret',                   // id
        __('Flickr API Secret', GALLERY_DEVS_PLUGIN),
        'gfdevs_settings_flickr_secret_fields_callback',
        'gfdevs_settings_flickr_page',          // page
        'gfdevs_settings_flickr_section'        // section
    );

    /**
     * Register sections and fields
     */
    register_setting('gfdevs_settings_post_types_page', 'gfdevs_options_post_types');
    register_setting('gfdevs_settings_other_sources_page', 'gfdevs_options_providers');

    register_setting('gfdevs_settings_flickr_page', 'gfdevs_options_flickr_username');
    register_setting('gfdevs_settings_flickr_page', 'gfdevs_options_flickr_key');
    register_setting('gfdevs_settings_flickr_page', 'gfdevs_options_flickr_secret');

}

/**
 * Call back functions
 */

function gfdevs_settings_post_types_callback() {
    echo '<p class="description">' . __('Enable gallery for selected post types.', GALLERY_DEVS_PLUGIN) . '</p>';
}

function gfdevs_settings_post_types_fields_callback() {

    $post_types = gfdevs_get_post_types();
    $options_post_types_values = get_option('gfdevs_options_post_types');

    if (count($post_types) == 0 || $post_types == false ) {
        echo '<em>' . __('No post types found.', GALLERY_DEVS_PLUGIN) . '</em>';
        return false;
    }?>

    <fieldset>
    <legend class="screen-reader-text"><span><?php echo __('Post Types', GALLERY_DEVS_PLUGIN);?></span></legend>

    <?php foreach($post_types as $post_type):?>
        <label for="<?php echo $post_type->name; ?>">
            <input type="checkbox"
                   name="gfdevs_options_post_types[<?php echo $post_type->name; ?>]"
                   id="<?php echo $post_type->name; ?>"
                   value="1" <?php isset($options_post_types_values[$post_type->name]) ? checked(1, $options_post_types_values[$post_type->name]) : false; ?>>
            <?php echo $post_type->labels->singular_name; ?>
        </label>
        <br>

    <?php endforeach; ?>

    </fieldset><?php

}

function gfdevs_settings_other_sources_callback() {
    echo '<p class="description">' . __('Select gallery service providers to use on your site (currently Flickr only supported).', GALLERY_DEVS_PLUGIN) . '</p>';
}

function gfdevs_settings_other_sources_fields_callback() {
    $options_providers_values = get_option('gfdevs_options_providers');
    ?>
    <fieldset>
    <legend class="screen-reader-text"><span><?php echo __('Gallery Service Providers', GALLERY_DEVS_PLUGIN);?></span></legend>
    <label for="flickr">
        <input type="checkbox"
               name="gfdevs_options_providers[flickr]"
               id="flickr"
               value="1" <?php isset($options_providers_values['flickr']) ? checked(1, $options_providers_values['flickr']) : false; ?>>
        Flickr
    </label>
    </fieldset><?php

}

function gfdevs_settings_flickr_callback() {
    echo '<p class="description">' . __('Configure your Flickr API key for your account.', GALLERY_DEVS_PLUGIN) . '</p>';
}

function gfdevs_settings_flickr_key_fields_callback() {
    $options_flickr_key = get_option('gfdevs_options_flickr_key');
    ?>
    <fieldset>
    <legend class="screen-reader-text"><span><?php echo __('Flickr API Key', GALLERY_DEVS_PLUGIN);?></span></legend>
    <input type="text"
           name="gfdevs_options_flickr_key"
           id="gfdevs_options_flickr_key"
           class="regular-text ltr"
           value="<?php echo (isset($options_flickr_key) ? $options_flickr_key : '' );?>">
    <p class="description"><?php echo __('Add your Flickr API Key', GALLERY_DEVS_PLUGIN);?></p>
    </fieldset><?php

}

function gfdevs_settings_flickr_secret_fields_callback() {
    $options_flickr_secret = get_option('gfdevs_options_flickr_secret');
    ?>
    <fieldset>
    <legend class="screen-reader-text"><span><?php echo __('Flickr API Secret', GALLERY_DEVS_PLUGIN);?></span></legend>
    <input type="text"
           name="gfdevs_options_flickr_secret"
           id="gfdevs_options_flickr_secret"
           class="regular-text ltr"
           value="<?php echo (isset($options_flickr_secret) ? $options_flickr_secret : '' );?>">
    <p class="description"><?php echo __('Add your Flickr API Secret', GALLERY_DEVS_PLUGIN);?></p>
    </fieldset><?php

}

function gfdevs_settings_flickr_username_fields_callback() {
    $options_flickr_username = get_option('gfdevs_options_flickr_username');
    ?>
    <fieldset>
    <legend class="screen-reader-text"><span><?php echo __('Flickr Username', GALLERY_DEVS_PLUGIN);?></span></legend>
    <input type="text"
           name="gfdevs_options_flickr_username"
           id="gfdevs_options_flickr_username"
           class="regular-text ltr"
           value="<?php echo (isset($options_flickr_username) ? $options_flickr_username : '' );?>">
    <p class="description"><?php echo __('Add your Flickr Username', GALLERY_DEVS_PLUGIN);?></p>
    </fieldset><?php

}