<?php

/**
 * Get post types for options page
 * Post types to which you can apply gallery custom field
 * @return array
 */
function gfdevs_get_post_types() {

    $exclude_post_types = array('attachment');
    $post_types = get_post_types(array('public' => true), 'objects');

    foreach ($post_types as $post_type_key => $post_type ) {
        foreach ($exclude_post_types as $exclude_post_type) {
            if ($post_type->name == $exclude_post_type) {
                unset($post_types[$post_type_key]);
            }
        }
    }

    return $post_types;

}

/**
 * Get photo count from synced Flickr album
 * @param $photosets
 * @param $album_id
 * @return bool
 */
function gfdevs_gallery_flickr_photo_count($photosets, $album_id) {
    if (!$photosets) {
        return false;
    }

    if (!$album_id) {
        return false;
    }

    $albums = $photosets['photosets'];

    foreach( $albums as $album ) {
        if ($album_id == $album['ID']) {
            return $album['photos_count'];
        }
    }

    return false;
}