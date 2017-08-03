<form method="post" action="options.php">
<?php

    settings_fields('gfdevs_settings_post_types_page');
    do_settings_sections('gfdevs_settings_post_types_page');

    submit_button();
?>
</form>

<form method="post" action="options.php">
    <?php

    settings_fields('gfdevs_settings_other_sources_page');
    do_settings_sections('gfdevs_settings_other_sources_page');

    submit_button();
    ?>
</form>

