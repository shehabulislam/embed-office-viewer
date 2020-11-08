<?php
/*-------------------------------------------------------------------------------*/
// Developer page
/*-------------------------------------------------------------------------------*/
add_action('admin_menu', 'eov_premium_plugins');

function eov_premium_plugins()
{
    add_submenu_page('edit.php?post_type=officeviewer', 'Premium Plugins', 'Premium Plugins', 'manage_options', 'eov-premium-plugins', 'eov_premium_plugins_callback');
}

function eov_premium_plugins_callback()
{
    ?>
    <?php $plugins = wp_remote_get('http://localhost/freemius/premium-plugin/');?>

<?php echo $plugins['body']; ?>

<?php
}