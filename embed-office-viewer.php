<?php

/*
 * Plugin Name: Office Viewer
 * Plugin URI:  http://bplugins.com
 * Description: You can Embed Microsoft Word, Excel And Powerpodint File in wordpress Using Office Viewer Plugin.
 * Version: 2.0.2
 * Author: bPlugins LLC
 * Author URI: http://bPlugins.com
 * License: GPLv3
 * Text Domain:  bplugins
 * Domain Path:  /languages
 */

if ( !function_exists( 'eov_fs' ) ) {
    // Create a helper function for easy SDK access.
    function eov_fs()
    {
        global  $eov_fs ;
        
        if ( !isset( $eov_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $eov_fs = fs_dynamic_init( array(
                'id'             => '7003',
                'slug'           => 'embed-office-viewer',
                'type'           => 'plugin',
                'public_key'     => 'pk_0657e65491580bc23260341c9d8e0',
                'is_premium'     => true,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
                'menu'           => array(
                'slug'       => 'edit.php?post_type=officeviewer',
                'first-path' => 'edit.php?post_type=officeviewer&page=eov-support',
            ),
                'is_live'        => true,
            ) );
        }
        
        return $eov_fs;
    }
    
    // Init Freemius.
    eov_fs();
    // Signal that SDK was initiated.
    do_action( 'eov_fs_loaded' );
}

// Import Old Data From CMB2
// function eov_active_plugin()
// {
//     eov_import_meta();
// }

// register_activation_hook( __FILE__, 'eov_active_plugin' );
add_action(
    'init',
    'eov_upgrade_function',
    10,
    2
);
function eov_upgrade_function()
{
    if ( eov_fs()->is_free_plan() ) {
        eov_import_meta();
    }
}

function eov_add_simple_css()
{
    ?>
<style>
/*Readonly Fields*/

.hayat-readyonly {
    filter:invert(1);
}

.hayat-readyonly:hover:after {
    content: "This option is available in the Pro Version only.";
    position: absolute;
    top: 0;
    width: 95%;
    height: 100%;
    vertical-align: middle;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #2196F3;
}
</style>

<?php 
}

// load textdomain
function ovp_load_textdomain()
{
    load_plugin_textdomain( 'bplugins', false, dirname( __FILE__ ) . "/languages" );
}

add_action( "plugins_loaded", 'ovp_load_textdomain' );
/*Some Set-up*/
define( 'OVP_PLUGIN_DIR', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
//Remove post update massage and link
function ovp_updated_messages( $messages )
{
    $messages['officeviewer'][1] = __( 'Updated ' );
    return $messages;
}

add_filter( 'post_updated_messages', 'ovp_updated_messages' );
/*-------------------------------------------------------------------------------*/
/*   Register Custom Post Types
/*-------------------------------------------------------------------------------*/
add_action( 'init', 'ovp_create_post_type' );
function ovp_create_post_type()
{
    register_post_type( 'officeviewer', array(
        'labels'              => array(
        'name'          => __( 'Office Viewer' ),
        'singular_name' => __( 'Office Documents' ),
        'add_new'       => __( 'Add New' ),
        'add_new_item'  => __( 'Add New' ),
        'edit_item'     => __( 'Edit' ),
        'new_item'      => __( 'New ' ),
        'view_item'     => __( 'View' ),
        'search_items'  => __( 'Search' ),
        'not_found'     => __( 'Sorry, we couldn\'t find the Doc file you are looking for.' ),
    ),
        'public'              => false,
        'show_ui'             => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => true,
        'menu_position'       => 14,
        'show_in_rest'        => true,
        'menu_icon'           => OVP_PLUGIN_DIR . '/img/icon.png',
        'has_archive'         => false,
        'hierarchical'        => false,
        'capability_type'     => 'post',
        'rewrite'             => array(
        'slug' => 'officeviewer',
    ),
        'supports'            => array( 'title' ),
    ) );
}

/*-------------------------------------------------------------------------------*/
/*  Metabox

/*-------------------------------------------------------------------------------*/
// include_once('cmb2/init.php');
// include_once('cmb2/example-functions.php');
// //include_once('admin/codestar-framework/codestar-framework.php');
// include_once('gutenblock/index.php');
/*-------------------------------------------------------------------------------*/
/*   Hide & Disabled View, Quick Edit and Preview Button
/*-------------------------------------------------------------------------------*/
function ovp_remove_row_actions( $idtions )
{
    global  $post ;
    
    if ( $post->post_type == 'officeviewer' ) {
        unset( $idtions['view'] );
        unset( $idtions['inline hide-if-no-js'] );
    }
    
    return $idtions;
}

if ( is_admin() ) {
    add_filter(
        'post_row_actions',
        'ovp_remove_row_actions',
        10,
        2
    );
}
/*-------------------------------------------------------------------------------*/
/* HIDE everything in PUBLISH metabox except Move to Trash & PUBLISH button
/*-------------------------------------------------------------------------------*/
function ovp_hide_publishing_actions()
{
    $my_post_type = 'officeviewer';
    global  $post ;
    if ( $post->post_type == $my_post_type ) {
        echo  '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ' ;
    }
}

add_action( 'admin_head-post.php', 'ovp_hide_publishing_actions' );
add_action( 'admin_head-post-new.php', 'ovp_hide_publishing_actions' );
/*-------------------------------------------------------------------------------*/
/* Change publish button to save.
/*-------------------------------------------------------------------------------*/
add_filter(
    'gettext',
    'ovp_change_publish_button',
    10,
    2
);
function ovp_change_publish_button( $translation, $text )
{
    if ( 'officeviewer' == get_post_type() ) {
        if ( $text == 'Publish' ) {
            return 'Save';
        }
    }
    return $translation;
}

// ONLY MOVIE CUSTOM TYPE POSTS
add_filter( 'manage_officeviewer_posts_columns', 'ST4_columns_head_only_officeviewer', 10 );
add_action(
    'manage_officeviewer_posts_custom_column',
    'ST4_columns_content_only_officeviewer',
    10,
    2
);
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
function ST4_columns_head_only_officeviewer( $defaults )
{
    $defaults['directors_name'] = 'ShortCode';
    return $defaults;
}

function ST4_columns_content_only_officeviewer( $column_name, $post_ID )
{
    if ( $column_name == 'directors_name' ) {
        echo  '<input onClick="this.select();" value="[office_doc id=' . $post_ID . ']" >';
    }
}

//Lets register our shortcode
function ovp_add_shortcode( $atts )
{
    extract( shortcode_atts( array(
        'id'     => null,
        'url'    => null,
        'width'  => 640,
        'height' => 900,
    ), $atts ) );
    $document_source = get_post_meta( $id, 'eov_document_source', true );
    $show_name = get_post_meta( $id, 'eov_show_name', true );
    $download_btn = get_post_meta( $id, 'eov_download_button', true );
    $right_click = get_post_meta( $id, 'eov_right_click', true );
    $disable_popout = get_post_meta( $id, 'eov_disbale_popout', true );
    $doc_file = get_post_meta( $id, 'eov_document', true );
    $google_document = get_post_meta( $id, 'eov_google_document', true );
    $dropbox_doc_file = get_post_meta( $id, 'eov_dropbox_document', true );
    $eov_onedrive_document = get_post_meta( $id, 'eov_onedrive_document', true );
    $width = get_post_meta( $id, 'eov_document_width', true );
    $height = get_post_meta( $id, 'eov_document_height', true );
    //$dropbox_document = get_post_meta($id, 'is_dropbox_document', true);
    // $is_onedrive_document = get_post_meta($id, 'is_onedrive_document', true);
    $doc_ext = pathinfo( $doc_file, PATHINFO_EXTENSION );
    $view_type = get_post_meta( $id, 'eov_view_type', true );
    if ( $view_type == '' ) {
        $view_type = 'google';
    }
    ?>

<?php 
    
    if ( ($doc_ext == 'pdf' || $doc_ext == 'html') && $view_type == 'microsoft' ) {
        echo  "<h2>OOpssss... Please Select 'View From' Google to show {$doc_ext} File</h2>" ;
    } else {
        ?>

<?php 
        ob_start();
        $url = 'https://view.officeapps.live.com/op/embed.aspx?src=' . $doc_file;
        $width = $width['width'];
        if ( $width == 0 ) {
            $width = 640;
        }
        $height = $height['height'];
        if ( $height == 0 ) {
            $height = 900;
        }
        $frame_style = 'width:' . $width . 'px; ' . 'height:' . $height . 'px;';
        $base_url = '//docs.google.com/gview?embedded=true&url=';
        ?>

<?php 
        
        if ( $document_source == 'library' ) {
            if ( $show_name == '1' ) {
                echo  '<p>File Name : ' . basename( $doc_file ) . '</p>' ;
            }
            
            if ( $download_btn == '1' ) {
                ?>

<p><a style="margin-bottom: 10px;" download href="<?php 
                echo  $doc_file ;
                ?>"><button
            style="display:inline;margin-bottom:10px;">Download
            File</button></a></p>

<?php 
            }
        
        }
        
        ?>
<div style="position:relative">
    <?php 
        if ( $right_click == '1' ) {
            ?>
    <div id="wrapper" style="position: relative;">
        <div id="block" style="position: absolute; top: 0; left: 0; width: 100%; height: 600%"></div>
        <?php 
        }
        ?>

        <?php 
        
        if ( $document_source == 'library' ) {
            
            if ( $view_type == 'microsoft' ) {
                ?>
        <iframe src="<?php 
                echo  $url ;
                ?>" width="<?php 
                echo  $width ;
                ?>" height="<?php 
                echo  $height ;
                ?>"
            frameborder="0"></iframe>
        <?php 
            } else {
                clearstatcache();
                echo  '<iframe id="s_pdf_frame" src="' . $base_url . $doc_file . '" style="float:left; padding:10px;' . $frame_style . '" frameborder="0"></iframe>' ;
            }
        
        } elseif ( $document_source == 'google' ) {
            clearstatcache();
            echo  '<iframe id="s_pdf_frame" src="' . $google_document . '" style="float:left; padding:10px;' . $frame_style . '" frameborder="0"></iframe>' ;
        } elseif ( $document_source == 'onedrive' ) {
            ?>

        <iframe src="<?php 
            echo  $eov_onedrive_document ;
            ?>" width="<?php 
            echo  $width ;
            ?>"
            height="<?php 
            echo  $height ;
            ?>" frameborder="0" scrolling="no"></iframe>

        <?php 
        } elseif ( $document_source == 'dropbox' ) {
            ?>
        <div style="display: inline-block">
            <a href="<?php 
            echo  $dropbox_doc_file ;
            ?>" class="dropbox-embed" data-height="<?php 
            echo  $height ;
            ?>px"
                data-width="<?php 
            echo  $width ;
            ?>px"></a>
        </div>
        <?php 
        }
        
        ?>

        <?php 
        if ( $right_click == '1' ) {
            ?>
    </div><?php 
        }
        ?>
    <?php 
        if ( $disable_popout == '1' ) {
            ?>
    <div style="width: 80px;height: 80px;position: absolute;opacity: 0;right: 18px;top: 0px;"></div>
    <?php 
        }
        ?>
</div>
<?php 
    }
    
    ?>

<?php 
    $output = ob_get_clean();
    return $output;
    //print $output; // debug
    ?>

<?php 
}

add_shortcode( 'office_doc', 'ovp_add_shortcode' );
// Add shortcode area
add_action( 'edit_form_after_title', 'ovp_shortcode_area' );
function ovp_shortcode_area()
{
    global  $post ;
    
    if ( $post->post_type == 'officeviewer' ) {
        ?>
<div>
    <label style="cursor: pointer;font-size: 13px; font-style: italic;" for="pdfp_shortcode">Copy this shortcode and
        paste it into your post, page, or text widget content:</label>
    <span style="display: block; margin: 5px 0; background:#1e8cbe; ">
        <input type="text" id="pdfp_shortcode"
            style="font-size: 12px; border: none; box-shadow: none;padding: 4px 8px; width:100%; background:transparent; color:white;"
            onfocus="this.select();" readonly="readonly" value="[office_doc id=<?php 
        echo  $post->ID ;
        ?>]" />
    </span>
</div>
<?php 
    }

}

// Adds a box to the main column on the Post and Page edit screens.
function ovp_metabox()
{
    add_meta_box(
        'donation',
        __( 'Support Office Viewer', 'ovp' ),
        'ovp_review_req',
        'officeviewer',
        'side'
    );
}

add_action( 'add_meta_boxes', 'ovp_metabox' );
function ovp_review_req()
{
    echo  'If you like <strong>Embed Office Viewer </strong> Plugin, please leave us a <a href="https://wordpress.org/support/plugin/embed-office-viewer/reviews/?filter=5#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733; rating.</a> Your Review is very important to us as it helps us to grow more.

<p>Need some improvement ? <a href="mailto:abuhayat.du@gmail.com">Please let me know </a> how can i improve the Plugin.</p>' ;
}

// Footer Review Request
add_filter( 'admin_footer_text', 'ovp_admin_footer' );
function ovp_admin_footer( $text )
{
    
    if ( 'officeviewer' == get_post_type() ) {
        $url = 'https://wordpress.org/support/plugin/embed-office-viewer/reviews/?filter=5#new-post';
        $text = sprintf( __( 'If you like <strong>Embed Office Viewer</strong> please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'h5ap-domain' ), $url );
    }
    
    return $text;
}

/*
 * Code By Raju
 */
function eov_admin_assets( $screen )
{
    $_screen = get_current_screen();
    global  $post ;
    
    if ( !empty($post) && $post->post_type == 'officeviewer' || 'officeviewer_page_eov-onedrive' == $screen ) {
        wp_enqueue_script(
            'eov-admin-js',
            plugin_dir_url( __FILE__ ) . 'admin/js/script.js',
            array( 'jquery', 'dropboxjs', 'eov-microsoft-js' ),
            ''
        );
        // OneDrive Picker
        wp_enqueue_script( 'eov-microsoft-js', 'https://js.live.net/v7.2/OneDrive.js' );
        // Google Picker
        wp_enqueue_script(
            'eov-google-js',
            plugin_dir_url( __FILE__ ) . 'admin/js/google.js',
            array( 'eov-google-picker-js' ),
            null,
            true
        );
        wp_enqueue_script(
            'eov-google-picker-js',
            'https://apis.google.com/js/api.js?onload=onApiLoad',
            array(),
            null,
            true
        );
        $api = array();
        $google_api = get_option( 'eov_onedrive' );
        
        if ( is_array( $google_api ) && (array_key_exists( 'eov_google_apikey', $google_api ) && array_key_exists( 'eov_google_client_id', $google_api ) && array_key_exists( 'eov_google_project_number', $google_api )) ) {
            $api = [
                'apikey'         => $google_api['eov_google_apikey'],
                'client_id'      => $google_api['eov_google_client_id'],
                'project_number' => $google_api['eov_google_project_number'],
                'plugin'         => 'pro',
            ];
        } else {
            $api = array(
                'plugin' => 'free',
            );
        }
        
        wp_localize_script( 'eov-google-js', 'api', $api );
        wp_enqueue_style( 'eov-admin-css', plugin_dir_url( __FILE__ ) . 'admin/css/style.css' );
        $option = get_option( 'eov_onedrive' );
        $eov = array();
        
        if ( is_array( $option ) && (array_key_exists( 'eov_google_apikey', $option ) || array_key_exists( 'eov_google_client_id', $option ) || array_key_exists( 'eov_google_project_number', $option ) || array_key_exists( 'eov_onedrive_client_id', $option ) || array_key_exists( 'eov_dropbox_appkey', $option )) ) {
            $eov = array(
                'client_id'      => ( $option['eov_onedrive_client_id'] ? $option['eov_onedrive_client_id'] : '' ),
                'dropbox_appkey' => ( $option['eov_dropbox_appkey'] ? $option['eov_dropbox_appkey'] : '' ),
                'g_apikey'       => ( $google_api['eov_google_apikey'] ? $google_api['eov_google_apikey'] : '' ),
                'g_client_id'    => ( $google_api['eov_google_client_id'] ? $google_api['eov_google_client_id'] : '' ),
                'project_number' => ( $google_api['eov_google_project_number'] ? $google_api['eov_google_project_number'] : '' ),
                'plugin'         => 'pro',
            );
        } else {
            $eov = array(
                'plugin' => 'free',
            );
        }
        
        wp_localize_script( 'eov-admin-js', 'eov', $eov );
    }
    
    if ( $screen == 'officeviewer_page_eov-support' || $screen == 'officeviewer_page_eov-plugins-from-bplugins' ) {
        wp_enqueue_style( 'eov-admin-css', plugin_dir_url( __FILE__ ) . 'admin/css/style.css' );
    }
}

add_action( 'admin_enqueue_scripts', 'eov_admin_assets' );
function eov_add_script_footer()
{
    ?>
<script type="text/javascript" src="https://js.live.net/v6.0/OneDrive.js" id="onedrive-js"
    client-id="91fb70ed-4347-4204-b61d-a8b3751005d3"></script>

<script type="text/javascript">

</script>
<?php 
}

add_action( "admin_header", 'eov_add_script_footer' );
add_filter(
    'script_loader_src',
    'add_id_to_script',
    10,
    2
);
function add_id_to_script( $src, $handle )
{
    if ( $handle != 'dropboxjs' ) {
        return $src;
    }
    return $src . '" id="dropboxjs" data-app-key="MY_APP_KEY"';
}

add_filter(
    'clean_url',
    'unclean_url',
    10,
    3
);
function unclean_url( $good_protocol_url, $original_url, $_context )
{
    $dropbox_key = '';
    $dropbox_appkey = get_option( 'eov_onedrive' );
    if ( is_array( $dropbox_appkey ) && array_key_exists( 'eov_dropbox_appkey', $dropbox_appkey ) ) {
        $dropbox_key = ( $dropbox_appkey['eov_dropbox_appkey'] ? $dropbox_appkey['eov_dropbox_appkey'] : '' );
    }
    
    if ( false !== strpos( $original_url, 'data-app-key' ) ) {
        remove_filter(
            'clean_url',
            'unclean_url',
            10,
            3
        );
        $url_parts = parse_url( $good_protocol_url );
        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' id='dropboxjs' data-app-key='" . $dropbox_key . "";
    }
    
    return $good_protocol_url;
}

function eov_dropbox_script()
{
    wp_enqueue_script(
        'dropboxjs',
        'https://www.dropbox.com/static/api/2/dropins.js',
        array(),
        ''
    );
}

add_action( 'wp_enqueue_scripts', 'eov_dropbox_script' );
add_action( 'admin_enqueue_scripts', 'eov_dropbox_script' );
/*-------------------------------------------------------------------------------*/
/*   FRAMEWORK + OTHER INC
/*-------------------------------------------------------------------------------*/
//require_once 'inc/cpt.php';
require_once 'admin/codestar-framework/codestar-framework.php';
// Free only code block
if ( eov_fs()->is_free_plan() ) {
    require_once 'admin/codestar-framework/metabox-free.php';
    //require_once 'inc/shortcode-free.php';
    require_once 'admin/import-meta.php';
    require_once 'admin/global/free-plugin-list.php';
    require_once 'admin/global/help-usages.php';
    require_once 'admin/global/premium-plugins.php';
    add_action( 'admin_head', 'eov_add_simple_css' );
    //require_once 'premium-files/metabox-pro.php';
}

if ( eov_fs()->can_use_premium_code__premium_only() ) {
    require_once 'premium-files/metabox-pro.php';
    require_once 'premium-files/shortcode-pro.php';
    require_once 'premium-files/help-usages.php';
    require_once 'admin/import-meta.php';
    //require_once 'premium-files/widgets.php';
    //require_once 'premium-files/blocks/index.php';
}