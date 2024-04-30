<?php
namespace EOV\Services;
use EOV\Model\Style;

class Template extends Style{

    protected static $style = [];
    public static $uniqid = null;
    
    public static function html($data){
        self::createId();
        self::addStyle("#".self::$uniqid, ['position' => 'relative', 'width' => $data['width'], 'margin' => '0 auto']);
        self::addStyle("#".self::$uniqid ." .disablePopout", ['width' => '80px;height: 200px;position: absolute;opacity: 0;right: 0px;top: 0px;']);

        $isPPTX =  pathinfo($data['docFile'], PATHINFO_EXTENSION) === 'pptx';
        if($isPPTX){
            self::addStyle("#".self::$uniqid.' #block', ['position' => 'absolute', 'top' => '0', 'right' => '20px', 'width' => '100%', 'height' => 'calc(100% - 30px)']);
        }else {
            self::addStyle("#".self::$uniqid.' #block', ['position' => 'absolute', 'top' => '0', 'right' => '20px', 'width' => '100%', 'height' => 'calc(100% - 55px)']);
        }

        if($data['disableFullscreen']){
            self::addStyle("#".self::$uniqid.' #disableFullscreen', ['position' => 'absolute', 'bottom' => '0', 'right' => '0px', 'width' => '100px', 'height' => '50px']);
        }

        ob_start(); 
        ?>
        <!-- Pro template -->
        <style>
            <?php echo esc_html(self::renderStyle()); ?>
        </style>
        <?php
        self::pdfNotice($data['docFile'], $data['viewer']);
        $url = 'https://view.officeapps.live.com/op/embed.aspx?src=' . $data['docFile'];
        
        $frame_style = 'width:' . $data['width'] . 'height:' . $data['height'];
        $base_url = '//docs.google.com/gview?embedded=true&url=';
    
        ?>
    <div id="<?php echo esc_attr(self::$uniqid); ?>" class="eov_wrapper eov_doc">
        <?php
        if ( $data['source'] == 'library' ) {
            if ( $data['showName'] ) {
                echo  '<p>File Name : ' . esc_html(basename( $data['docFile']  )) . '</p>' ;
            }
            if ( $data['downloadBtn'] ) { ?>
            <p>
                <a style="margin-bottom: 10px;" download href="<?php echo  esc_attr($data['docFile'] ) ; ?>">
                    <button style="display:inline;margin-bottom:10px;">Download File </button>
                </a>
            </p>
        <?php } } 
        if ( $data['rightClick'] ) { ?>
        <div id="wrapper" style="position: relative;" class="eov_wrapper">
            <div id="block"></div>
            <div id="disableFullscreen"></div>
            <script>
                setTimeout(() => {
                document.getElementById(`s_pdf_frame`).contentWindow.document.oncontextmenu = function (e) {
                    // alert("Right Click Disabled");
                    e.preventDefault();
                };
                }, 1000);
                document.oncontextmenu = function (e) {
                // alert("Right Click Disabled");
                e.preventDefault();
                };
            </script>
            <?php } 
        if ( $data['source'] == 'library' ) {
            if ( $data['viewer'] == 'microsoft' ) { 
                self::microsoftViewer($data);
            } else if($data['viewer'] == 'gooogle'){ 
                self::googleViewer($data);
            }else if ($data['viewer'] == 'js'){
                self::jsViewer($data);
            }
        } elseif ( $data['source'] == 'google' ) { 
            self::googleFrame($data);
        } elseif ( $data['source'] == 'onedrive' ) { 
            self::oneDriveFrame($data);
        } elseif ( $data['source'] == 'dropbox' ) {
            self::dropboxFrame($data);    
        }
        if ( $data['rightClick']) { echo "</div>"; } 
        if ( $data['disablePopout'] ) {  ?>
            <div class="disablePopout"></div>
     <?php } ?>
    </div>
    <?php 

        $output = ob_get_contents();
        ob_get_clean();
        return $output;
    }

    public static function pdfNotice($file, $viewer){
        $doc_ext = pathinfo( $file, PATHINFO_EXTENSION );
        if ( ($doc_ext == 'pdf' || $doc_ext == 'html') && $viewer == 'microsoft' ) {
            echo  "<h2>OOpssss... Please Select 'View From' Google to show ".esc_html($doc_ext)." File</h2>";
            return false;
        }
    }

    public static function googleFrame($data){
        ?>
        <iframe id="s_pdf_frame" src="<?php echo  esc_url($data['googleDoc']); ?>" style="margin:0 auto; padding:10px;<?Php echo 'width:' . esc_attr($data['width']) . ';height:' . esc_attr($data['height']); ?>" frameborder="0"></iframe>
        <?php
    }

    public static function oneDriveFrame($data){
        ?>
        <iframe id="s_pdf_frame" src="<?php echo  esc_url($data['oneDriveDoc']) ;?>" width="<?php echo esc_attr($data['width']) ;?>" height="<?php echo  esc_attr($data['height']) ;?>" frameborder="0" scrolling="no"></iframe>
        <?php
    }

    public static function dropboxFrame($data){
        ?>
        <div style="display: inline-block">
            <a href="<?php echo esc_url($data['dropboxDoc']) ;?>" class="dropbox-embed" data-height="<?php echo esc_attr($data['height']) ;?>" data-width="<?php echo esc_attr($data['width']) ; ?>">
            </a>
        </div>
        <?php
    }

    /**
     * Google Doc Viewer
     */
    public static function googleViewer($data){
        ?>
        <iframe id="s_pdf_frame" src="//docs.google.com/gview?embedded=true&url=<?php echo esc_url($data['docFile']); ?>" style="margin:0 auto; padding:10px;<?php echo 'width:' . esc_attr($data['width']) . ';height:' . esc_attr($data['height']) ?>" frameborder="0"></iframe>
        <?php
    }

    /**
     * Microsoft Doc Viewer
     */
    public static function microsoftViewer($data){
        ?>
        <iframe id="s_pdf_frame" src="https://view.officeapps.live.com/op/embed.aspx?src=<?php echo  esc_url($data['docFile']) ;?>" width="<?php echo esc_attr($data['width']) ;?>" height="<?php echo  esc_attr($data['height']) ;?>" frameborder="0"></iframe>
        <?php
    }

    /**
     * js viewer
     */
    public static function jsViewer($data){
        ?>
        <iframe id="s_pdf_frame" src="<?php echo EOV_PLUGIN_DIR."premium-files/pdfjs/web/viewer.php?file=". esc_url($data['docFile']); ?>" width="<?php echo esc_attr($data['width']); ?>" height="<?php echo esc_attr($data['height']) ?>"></iframe>
        
        <?php
    }

    /**
     * create a unique id
     */
    public static function createId(){
        if(self::$uniqid === null){
            self::$uniqid = "eov".uniqid();
        }
    }
}