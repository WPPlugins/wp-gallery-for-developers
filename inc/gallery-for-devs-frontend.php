<?php

/**
 * Gallery items with meta data (attachment array)
 * @return array|bool
 */
function gfd_gallery() {

    // Getting images from local media library, if available
    $gallery_items = gfdevs_gallery_local();

    if ($gallery_items != false) {
        return $gallery_items;
    }

    // Getting images from Flickr service, if available
    $gallery_items = gfdevs_gallery_flickr();

    if ($gallery_items != false) {
        return $gallery_items;
    }

    return false;
}

/**
 * Create array of all images from local Media Library
 * @return array|bool
 */
function gfdevs_gallery_local() {
    global $post;

    $gallery = get_post_meta($post->ID, '_gfdevs_gallery', true);

    if ($gallery == false) {
        return false;
    }

    $upload_dir = wp_upload_dir();
    $upload_dir_base_url = $upload_dir['baseurl'];

    $gallery_items = array();

    foreach($gallery as $attachment_id) {

        $attachment = get_post($attachment_id);
        $attachment_meta = wp_get_attachment_metadata($attachment_id);

        $image = array();
        $image['ID'] = $attachment_id;
        $image['alt'] = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        $image['title'] = $attachment->post_title;
        $image['caption'] = $attachment->post_excerpt;
        $image['description'] = $attachment->post_content;
        $image['url'] = $upload_dir_base_url . '/' . $attachment_meta['file'];
        $image['width'] = $attachment_meta['width'];
        $image['height'] = $attachment_meta['height'];
        $image['mime_type'] = $attachment->post_mime_type;
        $image['type'] ='image';

        $path_info = pathinfo($image['url']);
        $image_file_name = $path_info['basename'];

        $image['sizes'] = array();
        if (count($attachment_meta['sizes'])>0) {
            foreach($attachment_meta['sizes'] as $attachment_key => $attachment_size) {
                $image['sizes'][$attachment_key] = str_replace($image_file_name, $attachment_size['file'], $image['url']);
                $image['sizes'][$attachment_key.'-width'] = $attachment_size['width'];
                $image['sizes'][$attachment_key.'-height'] = $attachment_size['height'];
            }
        }

        $gallery_items[] = $image;
    }

    return $gallery_items;
}

/**
 * Create array of all images from Flickr service
 * @return array|bool
 */
function gfdevs_gallery_flickr() {
    global $post;

    $album_id = get_post_meta($post->ID, '_gfdevs_gallery_flickr', true);

    if ($album_id == false) {
        return false;
    }

    $providers = get_option('gfdevs_options_providers');
    $flickr = (!isset($providers['flickr']) ? false : true);

    if (!$flickr) {
        return false;
    }

    $albums = get_option('gfdevs_flickr_photoset_' . $album_id);

    if ($albums) {
        return $albums;
    }

    return false;
}