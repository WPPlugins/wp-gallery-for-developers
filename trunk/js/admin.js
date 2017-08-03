/**
 * Admin.js
 */

jQuery(document).ready( function($) {

    // Var
    var file_frame;
    var image_template = $('#image-template').html();
    var gallery_image_list = $('#gfdevs-admin-gallery ul.ui-sortable');

    // Add images from WP Media Library
    $('#gfdevs-add-media').live('click', function(event){

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $( this ).data( 'uploader_title' ),
            button: {
                text: $( this ).data( 'uploader_button_text' ),
            },
            library: {
                type: 'image'
            },
            multiple: true  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            var selection = file_frame.state().get('selection');
            selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                var image_item = $(image_template).clone();
                image_item.html(image_item.html().replace(/\#\{url\}/g, attachment.sizes.thumbnail.url));
                image_item.html(image_item.html().replace(/\{id\}/g, attachment.id));
                image_item.html(image_item.html().replace(/\{alt\}/g, attachment.alt));
                $(gallery_image_list).append(image_item);

                // Show/Hide buttons
                gfdevs_additional_buttons();
            });
        });

        // Finally, open the modal
        file_frame.open();

    });

    // Remove images from gallery
    $('#gfdevs-remove-media').live('click', function(event){
        event.preventDefault();
        if (gallery_image_list.find('li').length == 0) {
            return false;
        }
        if (!confirm($(this).data('remove-message'))) {
            return false;
        }
        gallery_image_list.find('li').each(function() {
            $(this).remove();
        });
        // Show/Hide buttons
        gfdevs_additional_buttons();
    });

    // Remove one image from gallery
    $('.gfdevs-remove-media-item').live('click', function(event){
        event.preventDefault();
        var id = $(this).data('gallery-item-id');
        gallery_image_list.find('li').each(function() {
            if ($(this).data('gallery-item-id') == id) {
                $(this).remove();
                gfdevs_additional_buttons();
            }
        });
    });

    // Show Help
    $('#gfdevs-quick-help').live('click', function(event){
        $('#gfdevs-quick-help-view').toggle('fast');
    });

    // Onchange show Flickr gallery
    $('#gfdevs_gallery_flickr').live('change', function(event){
        var albumId = this.value;
        $('.gfdevs-gallery-list-flickr li').each( function() {
            $(this).addClass('hidden');
        });
        $('.gfdevs-gallery-list-flickr li').each( function() {
            if (albumId == $(this).data('photoset-id')) {
                $(this).removeClass('hidden');
            }
        });
    });

    // Set sync action
    $('#gfdevs-flickr-sync').live('click', function(event) {
        gfdevs_gallery_form_action('sync');
    });

    // Set reset action
    $('#gfdevs-flickr-reset').live('click', function(event) {
        gfdevs_gallery_form_action('reset');
    });

    // Run on document load
    gfdevs_additional_buttons();
    gfdevs_gallery_metabox_tabs();
    gfdevs_gallery_image_sort();

    // Set form action
    function gfdevs_gallery_form_action(action) {
        $('#gfdevs-flickr-action').val(action);
    }
    
    // Gallery image sorting
    function gfdevs_gallery_image_sort() {
        $(gallery_image_list).sortable();
    }

    // Show/Hide remove all, quick help buttons based on image count
    function gfdevs_additional_buttons() {
        if (gallery_image_list.find('li').length > 0) {
            $('#gfdevs-remove-media').css({'display': 'inline-block'});
            $('#gfdevs-quick-help').css({'display': 'inline-block'});
        } else {
            $('#gfdevs-remove-media').css({'display': 'none'});
            $('#gfdevs-quick-help').css({'display': 'none'});
        }
    }

    // Enable tabs in metabox
    function gfdevs_gallery_metabox_tabs() {

        var active_tab = $('#gfdevs-admin-gallery-tabs').data('active-tab');

        $('#gfdevs-admin-gallery-tabs .hidden-tab').removeClass('hidden-tab');
        $('#gfdevs-admin-gallery-tab-list li').each( function(index) {
            if (index == active_tab) {
                $(this).addClass('tabs');
            }
        });

        $('#gfdevs-admin-gallery-tabs').tabs({
            active: active_tab
        });

        $('#gfdevs-admin-gallery-tab-list li').live('click', function(event){
            event.preventDefault();
            $('#gfdevs-admin-gallery-tab-list li').each(function() {
                $(this).removeClass('tabs');
            });
            if ($(this).hasClass('ui-tabs-active')) $(this).addClass('tabs');
        });
    }

});