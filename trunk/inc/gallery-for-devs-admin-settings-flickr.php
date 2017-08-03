
<p><?php echo __('Don\'t have Flickr API key?', GALLERY_DEVS_PLUGIN); ?> <a href="https://www.flickr.com/services/apps/create/" target="_blank" title="<?php echo __('Request your API Key from Flickr service', GALLERY_DEVS_PLUGIN); ?>"><?php echo __('Request API Key from Flickr service', GALLERY_DEVS_PLUGIN); ?>.</a></p>

<form method="post" action="options.php">
<?php

    settings_fields('gfdevs_settings_flickr_page');
    do_settings_sections('gfdevs_settings_flickr_page');

    submit_button();
?>
</form>

<?php
$flickr = new FlickrAPI();

if (!$flickr->isValid()) {
    return false;
}

$sync = $flickr->syncAlbums();
$reset = $flickr->resetAlbums();
?>

<h2><?php echo __('Flickr Albums', GALLERY_DEVS_PLUGIN); ?></h2>

<?php if ($sync) :?>
    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
        <p><strong><?php echo __('Sync ok', GALLERY_DEVS_PLUGIN); ?>.</strong></p>
    </div>
<?php endif; ?>


<?php if ($reset) :?>
    <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
        <p><strong><?php echo __('Cache removed from database', GALLERY_DEVS_PLUGIN); ?>.</strong></p>
    </div>
<?php endif; ?>

<?php
$photosets = get_option('gfdevs_flickr_photosets');
if ($photosets):
?>
    <p><?php echo __('Statistics for your synced albums with Flickr servers.', GALLERY_DEVS_PLUGIN); ?></p>
    <ul>
        <li><?php echo __('Albums', GALLERY_DEVS_PLUGIN); ?>: <?php echo $photosets['count']; ?></li>
        <li><?php echo __('Photos', GALLERY_DEVS_PLUGIN); ?>: <?php echo $photosets['photos_count']; ?></li>
        <li><?php echo __('Last sync time', GALLERY_DEVS_PLUGIN); ?>: <?php echo date( get_option('date_format') . ' '. get_option('time_format'), $photosets['sync_time']); ?></li>
    </ul>
<?php else: ?>
    <p><?php echo __('You have not synced your Flickr account yet or you don\'t have any public album on Flickr servers.', GALLERY_DEVS_PLUGIN); ?></p>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="provider" value="flickr">
    <input type="hidden" name="action" id="gfdevs-flickr-action" value="off">
    <button type="submit" class="button button-primary" id="gfdevs-flickr-sync"><?php echo __('Sync', GALLERY_DEVS_PLUGIN); ?></button>
    <?php if ($photosets):?>
        <button type="submit" class="button button-secondary" id="gfdevs-flickr-reset"><?php echo __('Reset cache', GALLERY_DEVS_PLUGIN); ?></button>
    <?php endif; ?>
</form>

