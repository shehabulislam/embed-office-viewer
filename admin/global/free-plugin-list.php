<?php
require_once ABSPATH . "wp-admin/includes/plugin-install.php";
function eov_free_plugin_assets(){
    wp_enqueue_script('plugin-install');
    wp_enqueue_script('updates');
}
add_action('init', 'eov_free_plugin_assets');
//$table->display();
if (!class_exists('Eov_Free_plugins')) {
    class Eov_Free_plugins
    {

        public function __construct()
        {
            add_action('admin_menu', array($this, 'eov_free_plugins_menu'));
        }
        public function eov_free_plugins_menu()
        {
            add_submenu_page(
                'edit.php?post_type=officeviewer',
                'bPlugins',
                'Free Plugins From bPlugins LLC',
                'manage_options',
                'eov-plugins-from-bplugins',
                array($this, 'eov_free_plugin_list')
            );
        }

        public function eov_free_plugin_list()
        {
            $fields = array(
                'active_installs' => true, // rounded int
                'added' => false, // date
                'author' => true, // a href html
                'author_block_count' => false, // int
                'author_block_rating' => false, // int
                'author_profile' => false, // url
                'banners' => false, // array( [low], [high] )
                'compatibility' => true, // empty array?
                'contributors' => false, // array( array( [profile], [avatar], [display_name] )
                'description' => false, // string
                'donate_link' => false, // url
                'download_link' => true, // url
                'downloaded' => true, // int
                // 'group' => false,                 // n/a
                'homepage' => false, // url
                'icons' => true, // array( [1x] url, [2x] url )
                'last_updated' => true, // datetime
                'name' => true, // string
                'num_ratings' => true, // int
                'rating' => true, // int
                'ratings' => true, // array( [5..0] )
                'requires' => true, // version string
                'requires_php' => true, // version string
                // 'reviews' => false,               // n/a, part of 'sections'
                'screenshots' => false, // array( array( [src],  ) )
                'sections' => true, // array( [description], [installation], [changelog], [reviews], ...)
                'short_description' => true, // string
                'slug' => true, // string
                'support_threads' => false, // int
                'support_threads_resolved' => false, // int
                'tags' => false, // array( )
                'tested' => false, // version string
                'version' => true, // version string
                'versions' => true, // array( [version] url )
            );
            $plugins = plugins_api('query_plugins', array(
                'author' => 'abuhayat',
                'per_page' => 30,
                //'fields' => $fields,
            ));
            ?>

<div class="bplgins_free_plugins">
    <div class="wp-filter" style="text-align: center;">
        <h1 style="padding:10px;">Free PLugnis From bPLugins LLC</h1>
    </div>
    <form id="plugin-filter" method="post">
        <div class="wp-list-table widefat plugin-install">
            <h2 class='screen-reader-text'>Plugins list</h2>
            <div id="the-list">
                <?php foreach ($plugins->plugins as $plugin):

                $title = $plugin['name'];
                $version = $plugin['version'];
                $name = strip_tags($title . ' ' . $version);
                $details_link = self_admin_url("plugin-install.php?tab=plugin-information&amp;plugin=" . $plugin["slug"] .
                    "&amp;TB_iframe=true&amp;width=753&amp;height=852");
                $status = install_plugin_install_status($plugin);
                $requires_php = isset($plugin->requires_php) ? $plugin->requires_php : null;
                $requires_wp = isset($plugin->requires) ? $plugin->requires : null;

                $compatible_php = is_php_version_compatible($requires_php);
                $compatible_wp = is_wp_version_compatible($requires_wp);
                $action_links = array();
                if (current_user_can("install_plugins") || current_user_can("update_plugins")) {
                    $status = install_plugin_install_status($plugin);
                    switch ($status['status']) {
                        case 'install':
                            if ($status['url']) {
                                if ($compatible_php && $compatible_wp) {
                                    // $action_links[] = '<a onClick="disableClick()" data-slug="' . esc_attr($plugin['slug']) . '" id="plugin_install_from_iframe" class="button button-primary right" href="' . esc_attr($plugin['slug']) . '" target="_parent">' . __('Install Now') . '</a>';
                                    $action_links[] = '<a class="install-now button" data-slug="' . esc_attr($plugin['slug']) . '" href="' . esc_attr($status['url']) . '" aria-label="Install ' . esc_attr($name) . ' now" data-name="' . esc_attr($name) . '">Install Now</a>';
                                } else {
                                    $action_links[] = '<button type="button" class="button button-primary button-disabled right" disabled="disabled">Cannot Install</button>';
                                }
                            }
                            break;
                        case 'update_available':
                            if ($status['url']) {
                                if ($compatible_php) {
                                    //$action_links[] = '<a data-slug="' . esc_attr($plugin['slug']) . '" data-plugin="' . esc_attr($status['file']) . '" id="plugin_update_from_iframe" class="button button-primary right" href="' . $status['url'] . '" target="_parent">' . __('Install Update') . '</a>';
                                    $action_links[] = '<a class="update-now button aria-button-if-js" data-plugin="' . esc_attr($status['file']) . '" data-slug="' . esc_attr($plugin['slug']) . '" href="' . $status['url'] . '" aria-label="Update PDF Poster – PDF Embedder Plugin for WordPress 1.6.3 now" data-name="' . esc_attr($plugin['name']) . '" role="button">Update Now</a>';
                                } else {

                                    $action_links[] = '<button type="button" class="button button-primary button-disabled right" disabled="disabled">Cannot Update</button>';

                                }
                            }
                            break;
                        case 'newer_installed':
                            /* translators: %s: Plugin version. */
                            $action_links[] = '<a class="button button-primary right disabled">' . sprintf(__('Newer Version (%s) Installed'), $status['version']) . '</a>';
                            break;
                        case 'latest_installed':
                            $action_links[] = '<a class="button button-primary right disabled">' . __('Installed') . '</a>';
                            break;
                    }
                }
                $action_links[] = '<a href="' . esc_url($details_link) . '" class="thickbox" aria-label="' . esc_attr(sprintf("More information about %s", $plugin['name'])) . '" data-title="' . esc_attr($plugin['name']) . '">' . __('More Details') . '</a>';

                ?>

																					                <div class="plugin-card plugin-card-<?php echo $plugin['slug']; ?>">
																					                    <div class="plugin-card-top">
																					                        <div class="name column-name">
																					                            <h3>
																					                                <a href="<?php echo $details_link; ?>" class="thickbox open-plugin-details-modal">
																					                                    <?php echo $plugin['name']; ?> <img src="<?php echo $plugin['icons']['1x'] ?>"
																					                                        class="plugin-icon" alt="">
																					                                </a>
																					                            </h3>
																					                        </div>
																					                        <div class="action-links">
																					                            <ul class="plugin-action-buttons">
																					                                <li><?php if ($action_links) {
                    echo implode("</li><li>", $action_links);
                }
                ?></li>
																					                            </ul>
																					                        </div>
																					                        <div class="desc column-description">
																					                            <p><?php echo $plugin['short_description']; ?></p>
																					                            <p class="authors"> <cite>By <?php echo $plugin['author'] ?></cite>
																					                            </p>
																					                        </div>
																					                    </div>
																					                    <div class="plugin-card-bottom">
																					                        <div class="vers column-rating">
																					                            <?php wp_star_rating(array("rating" => $plugin["rating"], "type" => "percent", "number" => $plugin["num_ratings"]));?>
																					                            <span class="num-ratings">
																					                                (<?php echo number_format_i18n($plugin["num_ratings"]); ?>)
																					                            </span>
																					                        </div>
																					                        <div class="column-updated">
																					                            <strong><?php _e("Last Updated:");?></strong>
																					                            <span title="<?php echo esc_attr($plugin["last_updated"]); ?>">
																					                                <?php printf("%s ago", human_time_diff(strtotime($plugin["last_updated"])));?>
																					                            </span>
																					                        </div>
																					                        <div class="column-updated">
																					                            <?php echo sprintf(_n("%s download", "%s downloads", $plugin["downloaded"]), number_format_i18n($plugin["downloaded"])); ?>
																					                        </div>
																					                        <div class="column-downloaded">
																					                            <?php //echo $plugin['active_installs'] . '+ Active Installations'; ?> </div>
																					                        <div class="column-compatibility">
																					                            <?php
    if (!empty($plugin["tested"]) && version_compare(substr($GLOBALS["wp_version"], 0, strlen($plugin["tested"])), $plugin["tested"], ">")) {
                    echo '<span class="compatibility-untested">' . __("<strong>Untested</strong> with your version of WordPress") . '</span>';
                } elseif (!empty($plugin["requires"]) && version_compare(substr($GLOBALS["wp_version"], 0, strlen($plugin["requires"])), $plugin["requires"], "<")) {
                echo '<span class="compatibility-incompatible">' . __("Incompatible with your version of WordPress") . '</span>';
            } else {
                echo '<span class="compatibility-compatible">' . __("Compatible with your version of WordPress") . '</span>';
            }
            ?>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>

            </div>
        </div>
    </form>
</div>
<?php

        }
    }
}
new Eov_Free_plugins();