<?php
namespace EOV\Model;

class EnqueueAssets{
    protected static $_instance = null;

    public function __construct(){
        add_action('wp_enqueue_scripts',[$this, 'publicAssets']);
        add_action('admin_enqueue_scripts',[$this, 'adminAssets']);
        add_action( "admin_header", [$this, 'eov_add_script_footer'] );
    }

    public static function instance(){
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function publicAssets(){
        wp_enqueue_script(
            'dropboxjs',
            'https://www.dropbox.com/static/api/2/dropins.js',
            array(),
            ''
        );

    
    }

    public function adminAssets($screen){
        $_screen = get_current_screen();
        global  $post ;
        
        if ( !empty($post) && $post->post_type == 'officeviewer' || 'officeviewer_page_eov-onedrive' == $screen ) {
            wp_enqueue_script(
                'dropboxjs',
                'https://www.dropbox.com/static/api/2/dropins.js',
                array(),
                ''
            );

            wp_enqueue_script(
                'eov-admin-js',
                EOV_PLUGIN_DIR . 'admin/js/script.js',
                array( 'jquery', 'dropboxjs', 'eov-microsoft-js' ),
                time()
            );
            // OneDrive Picker
            wp_enqueue_script( 'eov-microsoft-js', 'https://js.live.net/v7.2/OneDrive.js' );
            // Google Picker
            wp_enqueue_script('eov-google-js', EOV_PLUGIN_DIR . 'admin/js/google.js', array( 'eov-google-picker-js' ), null, true );
            
            wp_enqueue_script( 'eov-google-picker-js', 'https://apis.google.com/js/api.js?onload=onApiLoad', array(), null,  true );
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
            wp_enqueue_style( 'eov-admin-css', EOV_PLUGIN_DIR . 'admin/css/style.css' );
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
            wp_enqueue_style( 'eov-admin-css', EOV_PLUGIN_DIR . 'admin/css/style.css' );
        }
    }

    function eov_add_script_footer(){
        ?>
    <script type="text/javascript" src="https://js.live.net/v6.0/OneDrive.js" id="onedrive-js"
        client-id="91fb70ed-4347-4204-b61d-a8b3751005d3"></script>
    
    <script type="text/javascript">
    
    </script>
    <?php 
    }

}

EnqueueAssets::instance();