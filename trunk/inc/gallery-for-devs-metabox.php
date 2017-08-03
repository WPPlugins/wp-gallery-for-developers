<?php

/**
 * Save gallery post meta values
 * @param $post_id
 */
function gfdevs_save_gallery_meta_values($post_id) {

    // Remove old record
    delete_post_meta($post_id, '_gfdevs_gallery');
    delete_post_meta($post_id, '_gfdevs_gallery_flickr');

    // Save local media gallery
    if (isset($_POST['gfdevs_gallery'])) {
        add_post_meta($post_id, '_gfdevs_gallery', $_POST['gfdevs_gallery']);
    }

    if (isset($_POST['gfdevs_gallery_flickr'])) {
        add_post_meta($post_id, '_gfdevs_gallery_flickr', $_POST['gfdevs_gallery_flickr']);
    }
}

add_action('save_post', 'gfdevs_save_gallery_meta_values');

/**
 * Add metabox for post types
 * @return bool
 */
function gfdevs_add_metabox() {

    $post_types = get_option('gfdevs_options_post_types');

    if (count($post_types)==0 || $post_types == false) {
        return false;
    }

    foreach($post_types as $post_type_name => $post_type) {
        add_meta_box('gfdevs_gallery_metabox', __('WP Gallery for Developers', GALLERY_DEVS_PLUGIN), 'gfdevs_gallery_render_callback', $post_type_name, 'normal', 'default');
    }

    return true;

}

add_action( 'add_meta_boxes', 'gfdevs_add_metabox' );

/**
 * Render gallery metabox screen
 */
function gfdevs_gallery_render_callback($post)
{

    wp_nonce_field(plugin_basename(__FILE__), 'gfdevs_nonce_gallery');
    wp_enqueue_media();

    $image_template = '<li class="gfdevs-admin-gallery-thumb" data-gallery-item-id="{id}">
            <input type="hidden" name="gfdevs_gallery[]" value="{id}">
            <div class="gfdevs-admin-gallery-thumb-content">
                <div class="gfdevs-admin-gallery-thumb-image"><img src="#{url}" alt="{alt}" data-image-id="{id}" ></div>
                <div class="gfdevs-admin-gallery-thumb-options">
                    <a href="#" class="gfdevs-remove-media-item" data-gallery-item-id="{id}">' . __('Remove', GALLERY_DEVS_PLUGIN) . '</a>
                </div>
            </div>
        </li>';

    $quick_help_template = <<< EOF
<pre><code>&lt;?php if( &#36;images = function_exists('gfd_gallery') ? &#36;images = gfd_gallery() : false ): ?&gt;
    &lt;ul&gt;
        &lt;?php foreach( &#36;images as &#36;image ): ?&gt;
            &lt;li&gt;
                &lt;a href=&quot;&lt;?php echo &#36;image['url']; ?&gt;&quot;&gt;
                    &lt;img src=&quot;&lt;?php echo &#36;image['sizes']['thumbnail']; ?&gt;&quot; alt=&quot;&lt;?php echo &#36;image['alt']; ?&gt;&quot; /&gt;
                &lt;/a&gt;
                &lt;p&gt;&lt;?php echo &#36;image['caption']; ?&gt;&lt;/p&gt;
            &lt;/li&gt;
        &lt;?php endforeach; ?&gt;
    &lt;/ul&gt;
&lt;?php endif;?&gt;</code></pre>
EOF;

    // Get providers list
    $providers = get_option('gfdevs_options_providers');

    // Get saved images
    $post_gallery = get_post_meta($post->ID, '_gfdevs_gallery', true);
    $post_gallery_image_count = ($post_gallery != '') ? count($post_gallery) : false;

    $photosets = false;
    if (isset($providers['flickr']) && $providers['flickr'] == '1') {
        $photosets = get_option('gfdevs_flickr_photosets');
    }

    $post_gallery_flickr = get_post_meta($post->ID, '_gfdevs_gallery_flickr', true);
    $post_gallery_flickr_image_count = gfdevs_gallery_flickr_photo_count($photosets, $post_gallery_flickr);

    // 0 - for Local Media
    // 1 - for Flickr Media
    $active_tab_index = ( intval($post_gallery_image_count) == 0 && intval($post_gallery_flickr_image_count) > 0 ) ? 1 : 0;
    ?>

    <script id="image-template" type="text/x-custom-template">
        <?php echo $image_template; ?>
    </script>

    <p><em><?php echo __('Local media library gallery has priority over gallery service providers (like Flickr, etc.).', GALLERY_DEVS_PLUGIN);?></em></p>

    <div id="gfdevs-admin-gallery-tabs" class="categorydiv" data-active-tab="<?php echo $active_tab_index; ?>">

        <!-- UI tabs -->
        <ul id="gfdevs-admin-gallery-tab-list" class="category-tabs">
            <li><a href="#gfdevs-gallery-tab-local"><?php echo __('Local Media', GALLERY_DEVS_PLUGIN); ?>
                <?php echo ($post_gallery_image_count != false) ? '(' . $post_gallery_image_count . ($post_gallery_image_count == 1 ? __(' image',GALLERY_DEVS_PLUGIN) : __(' images',GALLERY_DEVS_PLUGIN)) . ')' : ''; ?></a></li>
            <?php if (isset($providers['flickr']) && $providers['flickr']== '1'):?>
            <li><a href="#gfdevs-gallery-tab-flickr"><?php echo __('Flickr Media', GALLERY_DEVS_PLUGIN); ?>
                <?php echo ($post_gallery_flickr_image_count != false) ? '(' . $post_gallery_flickr_image_count . ($post_gallery_flickr_image_count == 1 ? __(' image',GALLERY_DEVS_PLUGIN) : __(' images',GALLERY_DEVS_PLUGIN)) . ')' : ''; ?></a></li>
            <?php endif; ?>
            <li><a href="#gfdevs-gallery-tab-help"><?php echo __('Quick Help', GALLERY_DEVS_PLUGIN); ?></a></li>
        </ul>

        <!-- UI Tab: Local Media  -->
        <div id="gfdevs-gallery-tab-local" class="tabs-panel">

            <div>
                <p><?php echo __('Select images from local media library to add them to gallery.',GALLERY_DEVS_PLUGIN);?></p>
                <input type="button" id="gfdevs-add-media" class="button button-primary" data-uploader_title="<?php echo __('Select Images', GALLERY_DEVS_PLUGIN);?>"  data-uploader_button_text="<?php echo __('Select Images', GALLERY_DEVS_PLUGIN);?>" value="<?php echo __('Add Images', GALLERY_DEVS_PLUGIN);?>" />
                <input type="button" id="gfdevs-remove-media" class="button" data-remove-message="<?php echo __('Are you sure you want to remove all images from gallery?', GALLERY_DEVS_PLUGIN);?>" value="<?php echo __('Remove Images', GALLERY_DEVS_PLUGIN);?>" />
            </div>

            <div id="gfdevs-admin-gallery">
                <ul class="ui-sortable">
                    <?php if ($post_gallery != false): ?>
                        <?php foreach($post_gallery as $post_gallery_image): ?>
                            <?php
                            $wp_post_gallery_image_id = $post_gallery_image;
                            $wp_post_gallery_image_url = wp_get_attachment_image_src($wp_post_gallery_image_id, 'thumbnail');
                            $wp_post_gallery_image_alt = get_post_meta($wp_post_gallery_image_id, '_wp_attachment_image_alt', true);
                            echo preg_replace(
                                array('/{id}/', '/#\{url\}/', '/\{alt\}/'),
                                array($wp_post_gallery_image_id, $wp_post_gallery_image_url[0], $wp_post_gallery_image_alt),
                                $image_template);
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

        </div>

        <?php if (isset($providers['flickr']) && $providers['flickr']== '1'):?>
        <!-- UI Tab: Flickr Media  -->
        <div id="gfdevs-gallery-tab-flickr" class="tabs-panel hidden-tab">

            <?php if (is_array($photosets) && count($photosets)>0): ?>
                <?php $albums = $photosets['photosets']; ?>
                <div>
                    <p><?php echo __('List of all albums synced with Flickr servers.', GALLERY_DEVS_PLUGIN);?></p>
                    <select name="gfdevs_gallery_flickr" id="gfdevs_gallery_flickr">
                        <option value="0">—</option>
                        <?php foreach( $albums as $album ): ?>
                            <option value="<?php echo $album['ID'];?>"<?php echo ($post_gallery_flickr==$album['ID'] ? ' selected="selected"' : '');?>>
                                <?php echo $album['title']; ?> (<?php echo $album['photos_count']?> <?php echo ($album['photos_count'] == 1 ? __(' image',GALLERY_DEVS_PLUGIN) : __(' images',GALLERY_DEVS_PLUGIN))?>)</option>
                        <?php endforeach;?>
                    </select>
                    <ul class="gfdevs-gallery-list-flickr">
                        <?php foreach( $albums as $album ): ?>
                        <li data-photoset-id="<?php echo $album['ID'];?>" class="<?php echo ($post_gallery_flickr == $album['ID'] ? '' : 'hidden') ?>">
                            <a href="<?php echo $album['url']?>" target="_blank">
                                <img src="<?php echo $album['thumbnail']?>" alt="<?php echo $album['title']?>"><br/>
                            </a>
                            <p class="title"><strong><?php echo $album['title']?> (<?php echo $album['photos_count']?> <?php echo ($album['photos_count'] == 1 ? __(' image',GALLERY_DEVS_PLUGIN) : __(' images',GALLERY_DEVS_PLUGIN))?>)</strong></p>
                            <p class="description"><?php echo __('Description', GALLERY_DEVS_PLUGIN);?>: <?php echo $album['description']?></p>
                            <p class="description"><?php echo __('URL', GALLERY_DEVS_PLUGIN);?>: <a href="<?php echo $album['url']?>" target="_blank"><?php echo $album['url']?></a></p>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            <?php else : ?>
                <p><?php echo __('Sorry, you have not synced with Flickr servers.', GALLERY_DEVS_PLUGIN);?></p>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <div id="gfdevs-gallery-tab-help" class="tabs-panel hidden-tab">
            <div id="gfdevs-quick-help-view">
                <p><strong><?php echo __('Code', GALLERY_DEVS_PLUGIN);?></strong></p>
                <p><?php echo __('Basic list of images. This example will loop over the selected images and display a list of thumbnails which each link to the full size image.', GALLERY_DEVS_PLUGIN);?></p>
                <?php echo $quick_help_template; ?>
                <p><strong><?php echo __('Shortcode', GALLERY_DEVS_PLUGIN);?></strong></p>
                <p><?php echo __('You can also use inline shortcodes straight in to content area.', GALLERY_DEVS_PLUGIN);?></p>
                <ul>
                    <li><code>[gfd_gallery]</code> - <?php echo __('show all images', GALLERY_DEVS_PLUGIN);?></li>
                    <li><code>[gfd_gallery class="my-custom-class"]</code> - <?php echo __('show all images with custom class name for the gallery container', GALLERY_DEVS_PLUGIN);?></li>
                    <li><code>[gfd_gallery class="my-custom-class" rel="gallery-group"]</code> - <?php echo __('group images with rel attribute', GALLERY_DEVS_PLUGIN);?></li>
                    <li><code>[gfd_gallery class="my-custom-class" rel="gallery-group" size="medium"]</code> - <?php echo __('set thumbnail size', GALLERY_DEVS_PLUGIN);?> </li>
                    <li><code>[gfd_gallery class="my-custom-class" rel="gallery-group" from="2"]</code> - <?php echo __('add some limits', GALLERY_DEVS_PLUGIN);?></li>
                    <li><code>[gfd_gallery class="my-custom-class" rel="gallery-group" from="2" to="5"]</code> - <?php echo __('add some limits', GALLERY_DEVS_PLUGIN);?> </li>
                </ul>
            </div>
        </div>

    </div>

    <?php
}