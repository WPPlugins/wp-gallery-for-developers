<?php

/**
 * Shortcode: Show gallery on page
 */

function gfd_gallery_shortcode( $atts ) {

    $html_string = '';

    $attributes = shortcode_atts( array(
        'class' => 'gfd-gallery',
        'rel' => 'gfd-gallery',
        'from' => 0,
        'to' => 0,
        'size' => 'thumbnail',
    ), $atts);

    $images = function_exists('gfd_gallery') ? gfd_gallery() : false ;

    if (!is_array($images)) {
        return false;
    }

    if (count($images) == 0) {
        return false;
    }

    $from = ($attributes['from'] <= 0) ? 0 : $attributes['from'] - 1;
    $to = ($attributes['to'] <= 0) ? 1000000 : $attributes['to'] - 1;

    // Filter images
    if ($from >=0 && $to > 0) {
        $idx = 0;
        foreach( $images as $key => $image ) {
            if ($idx < $from || $idx > $to) {
                unset($images[$key]);
            }
            $idx++;
        }
    }

    $html_string .= '<div class="' . $attributes['class'] . '">';
    $html_string .= '<ul>';

    foreach( $images as $image ):

        $html_string .= '<li>';
        $html_string .= '<a href="' . $image['url'] . '" rel="' . $attributes['rel'] . '" class="' . $attributes['class'] . '-link">';
        $html_string .= '<img src="' . $image['sizes'][$attributes['size']] . '" alt="' . $image['alt'] . '" />';
        $html_string .= '</a>';
        $html_string .= '</li>';

    endforeach;

    $html_string .= '</ul>';
    $html_string .= '</div>';


    return $html_string;
}

add_shortcode( 'gfd_gallery', 'gfd_gallery_shortcode' );