<?php

function gfdevs_admin_settings_page() {?>

    <?php
    // Active Tab
    $active_tab = isset($_GET[ 'tab' ]) ? $_GET['tab'] : 'general';
    // Gallery Service Providers
    $providers = get_option('gfdevs_options_providers');
    ?>

    <div class="wrap">
        <h1><?php echo get_admin_page_title(); ?></h1>
        <p class="description"><?php echo __('Settings for the plugin WP Gallery for Developers', GALLERY_DEVS_PLUGIN); ?>.</p>

        <h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo GALLERY_DEVS_PLUGIN; ?>-settings&tab=general" class="nav-tab<?php echo $active_tab == 'general' ? ' nav-tab-active' : ''; ?>"><?php echo __('General', GALLERY_DEVS_PLUGIN);?></a>
            <?php if (isset($providers['flickr']) && $providers['flickr']== '1'):?>
                <a href="?page=<?php echo GALLERY_DEVS_PLUGIN; ?>-settings&tab=flickr" class="nav-tab<?php echo $active_tab == 'flickr' ? ' nav-tab-active' : ''; ?>"><?php echo __('Flickr', GALLERY_DEVS_PLUGIN);?></a>
            <?php endif; ?>
            <a href="?page=<?php echo GALLERY_DEVS_PLUGIN; ?>-settings&tab=about" class="nav-tab<?php echo $active_tab == 'about' ? ' nav-tab-active' : ''; ?>"><?php echo __('About', GALLERY_DEVS_PLUGIN);?></a>
        </h2>

        <?php switch ($active_tab) :
            case 'general' :
                require_once "gallery-for-devs-admin-settings-general.php";
                break;

            case 'about' :
                require_once "gallery-for-devs-admin-settings-about.php";
                break;

            case 'flickr' :
                if (isset($providers['flickr']) && $providers['flickr']== '1'):
                    require_once "gallery-for-devs-admin-settings-flickr.php";
                endif;
                break;

        endswitch; ?>

    </div>

<?php } ?>