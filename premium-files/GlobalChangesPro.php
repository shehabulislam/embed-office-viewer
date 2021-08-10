<?php
namespace EOV\Model;

class GlobalChangesPro{
    protected static $_instance = null;

    public function __construct(){
        
        add_filter( 'script_loader_src', [$this, 'add_id_to_script'], 10, 2 );
        add_filter( 'clean_url', [$this, 'unclean_url'], 10, 3 );
    }

    public static function instance(){
        if(self::$_instance === null){
            self::$_instaance = new self();
        }
        return self::$_instance();
    }

    function add_id_to_script( $src, $handle ){
        if ( $handle != 'dropboxjs' ) {
            return $src;
        }
        return $src . '" id="dropboxjs" data-app-key="MY_APP_KEY"';
    }
    function unclean_url( $good_protocol_url, $original_url, $_context ){
        $dropbox_key = '';
        $dropbox_appkey = get_option( 'eov_onedrive' );
        if ( is_array( $dropbox_appkey ) && array_key_exists( 'eov_dropbox_appkey', $dropbox_appkey ) ) {
            $dropbox_key = ( $dropbox_appkey['eov_dropbox_appkey'] ? $dropbox_appkey['eov_dropbox_appkey'] : '' );
        }
        
        if ( false !== strpos( $original_url, 'data-app-key' ) ) {
            remove_filter( 'clean_url', [$this, 'unclean_url'], 10, 3 );
            $url_parts = parse_url( $good_protocol_url );
            return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' id='dropboxjs' data-app-key='" . $dropbox_key . "";
        }
        
        return $good_protocol_url;
    }

}

GlobalChangesPro::instance();