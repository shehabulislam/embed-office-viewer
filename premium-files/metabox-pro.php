<?php // Silence is golden.
define('SITE_URL', get_site_url());

// Control core classes for avoid errors

if (class_exists('CSF')) {

    /*
     * Create Setting Page to take Ondrive api details
     */
    //
    // Set a unique slug-like ID
    $prefix = 'eov_onedrive';
    $site_url = get_site_url();
    // Create options
    CSF::createOptions($prefix, array(
        'framework_title' => 'Cloud API Settings',
        'theme' => 'light',
        'menu_title' => 'Cloud API Settings',
        'menu_parent' => 'edit.php?post_type=officeviewer',
        'menu_slug' => 'eov-onedrive',
        'menu_type' => 'submenu',
        'menu_capability' => 'manage_options',
    ));
    CSF::createSection($prefix, array(
        'id' => 'eov_onedrive_tab',
        'title' => 'Onedrive API Settings',
        'fields' => array(
            array(
                'id' => 'eov_onedrive_client_id',
                'title' => 'OneDrive Application (client) ID',
                'type' => 'text',
                'attributes' => array('id' => 'eov_onedrive_optoin_client_id'),
                'desc' => '<a href="https://aka.ms/AppRegistrations" target="_blank">Click Here</a> to create Onedrive Application (client) ID',
            ),
            array(
                'id' => 'eov_dropbox_appkey',
                'title' => 'Dropbox App Key',
                'type' => 'text',
                'attributes' => array('id' => 'eov_dropbox_appkey'),
                'desc' => 'Please <a href="https://www.dropbox.com/developers/apps/create" target="_blank">Click Here</a> to create Dropbox API key',
            ),
            array(
                'type' => 'content',
                'content' => 'Google API Setup',
                'class' => 'csf-field-subheading',
            ),
            array(
                'id' => 'eov_google_apikey',
                'type' => 'text',
                'title' => 'Google API key',
                'before' => '<p><a href="https://console.cloud.google.com/" target="_blank">Click Here</a> To Get Google Credentials</p>',
            ),
            array(
                'id' => 'eov_google_client_id',
                'type' => 'text',
                'title' => 'Google Client ID',
            ),
            array(
                'id' => 'eov_google_project_number',
                'type' => 'text',
                'title' => 'Google Project Number',
            ),

        ),
    ));

    // Set a unique slug_like ID

    $prefix = '_eovm_';

    // Create a metabox

    CSF::createMetabox($prefix, array(
        'title' => 'Viewer Setup',
        // 'class' => 'spt-main-class',
        'post_type' => 'officeviewer',
        'data_type' => 'unserialize',
        'class' => 'spt-main-class',
        'priority' => 'high',
    ));

    $onedrive_icon = plugin_dir_url(__FILE__) . 'admin/skydrive.png';

    // Create a section
    CSF::createSection($prefix, array(
        'title' => '',
        'fields' => array(
            // A textarea field
            // array(
            //     'id' => 'eov_choose_file',
            //     'title' => 'Chooose File From Cloud',
            //     'type' => 'content',
            //     'content' => '<button id="choose-files" class="button-primary csf--button">Choose File</button><br><div id="kresults"><pre></pre></div>',
            // ),
            // array(
            //     'id' => 'is_dropbox_document',
            //     'title' => 'Document From Dropbox?',
            //     'type' => 'switcher',
            //     'dependency' => array(array('is_onedrive_document', '=='), '0',array('is_google_document', '==', '0')),
            // ),
            // array(
            //     'id' => 'eov_document_source',
            //     'title' => 'Document Source',
            //     'type' => 'radio',
            //     'options' => array(
            //         'library' => 'Library',
            //         'google' => 'Google Drive',
            //         'onedrive' => 'OneDrive',
            //         'dropbox' => 'DropBox',
            //     ),
            //     'default' => 'library',
            // ),
            array(
                'id' => 'eov_document_source',
                'title' => 'Document Source',
                'type' => 'button_set',
                'options' => array(
                    'library' => 'Library',
                    'google' => 'Google Drive',
                    'onedrive' => 'OneDrive',
                    'dropbox' => 'Dropbox',
                ),
                'multiselect' => false,
                'default' => 'library',
                'attributes' => array('id' => 'document_source_btn'),
                'class' => 'document_source_btn',
            ),
            array(
                'id' => 'eov_dropbox_document',
                'type' => 'text',
                'title' => 'Dropbox Document URL',
                'placeholder' => 'https://',
                'dependency' => array('eov_document_source', '==', 'dropbox'),
                'attributes' => array('style' => 'min-height:29px !important;height:29px;', 'id' => 'dropbox_cloud_file_url'),
                'validate' => 'csf_validate_url',
            ),
            // array(
            //     'id' => 'is_onedrive_document',
            //     'title' => 'Document From OneDrive?',
            //     'type' => 'switcher',
            //     'dependency' => array(array('is_dropbox_document', '==', '0'), array('is_google_document', '==', '0')),
            //     //'class' => 'eov_ondrive_doc',
            // ),
            array(
                'id' => 'eov_onedrive_document',
                'type' => 'text',
                'title' => 'OneDrive Document URL',
                'dependency' => array('eov_document_source', '==', 'onedrive'),
                'class' => 'eov_ondrive_doc',
                'attributes' => array(
                    'style' => 'min-height:50px !important',
                ),
                'validate' => 'csf_validate_url',
                //'after' => '<button  id="eov_ondeive_picker"><img src="../'.$onedrive_icon.'"/>Choose From OneDrive</button>',
                //'after' => '<button  id="eov_ondeive_picker">Choose From OneDrive</button>'
                'attributes' => array('style' => 'min-height:29px !important;height:29px;', 'id' => 'eov_ondeive_file_url'),
            ),
            // array(
            //     'id' => 'is_google_document',
            //     'type' => 'switcher',
            //     'title' => 'Document From Google Drive?',
            //     'dependency'  => array(array('is_dropbox_document', '==', '0') , array('is_onedrive_document', '==', '0'))
            // ),
            array(
                'id' => 'eov_google_document',
                'title' => 'Google Drive Document URL',
                'type' => 'text',
                'validate' => 'csf_validate_url',
                'attributes' => array(
                    'style' => 'min-height:29px !important;height:29px',
                    'id' => 'eov_google_document_url',
                ),
                'dependency' => array('eov_document_source', '==', 'google'),
            ),
            // array(
            //     //'id' => 'eov_dropbox_document',
            //     'type' => 'content',
            //     'title' => ' ',
            //     'dependency' => array('is_dropbox_document', '==', '1'),
            //     'content' => '<div id="dropbox_btn"></div>'
            // ),

            // array(
            //     'id' => 'dropbox_api_key',
            //     'title' => 'Dropbox API Key',
            //     'type' => 'text',
            //     'dependency' => array('is_dropbox_document', '==', '1'),
            // ),
            array(
                'id' => 'eov_view_type',
                'title' => 'Viewer',
                'type' => 'radio',
                'options' => array(
                    'gooogle' => 'Google Doc Viewer',
                    'microsoft' => 'Microsoft Online Viewer',
                    'js' => 'JS Viewer',
                ),
                'default' => 'microsoft',
                //'class' => 'hayat-readyonly',
                'dependency' => array('eov_document_source', '==', 'library'),
            ),
            array(
                'id' => 'eov_document',
                'type' => 'upload',
                'title' => 'Document',
                'subtitle' => '',
                'desc' => 'also support .pdf and .html in "View From" google',
                'help' => 'help',
                'before' => '<p class="dfsp">Choose a document from Library or <b>Paste an external file link.</b></p>',
                'after' => 'Microsoft Word, Excel And Powerpodint Doc Only, Supported File Extension: .doc, .docx, .xls, .xlsx, .ppt, .pptx ',
                'button_title' => 'Choose File',
                'placeholder' => 'http://',
                'dependency' => array('eov_document_source', '==', 'library'),
            ),
            array(
                'id' => 'eov_document_width',
                'type' => 'dimensions',
                'title' => 'Width',
                'height' => false,
                'default' => array(
                    'width' => '640',
                    'unit' => 'px',
                ),
                'class' => 'document-width',
                'desc' => '<p>Leave blank if you want to use viewer default width (640px)</p>',
                'units' => array('px'),
            ),
            array(
                'id' => 'eov_document_height',
                'type' => 'dimensions',
                'title' => 'Height',
                'width' => false,
                'class' => 'document-height',
                'default' => array(
                    'height' => '900',
                    'unit' => 'px',
                ),
                'desc' => '<p>Leave blank if you want to use viewer default height (900px)</p>',
                'units' => array('px'),
            ),
            array(
                'id' => 'eov_disbale_popout',
                'type' => 'switcher',
                'title' => 'Disable Pop-out',
                'dependency' => [['eov_view_type', '==', 'gooogle'], ['eov_document_source', '==', 'library']],
            ),
            array(
                'id' => 'eov_show_name',
                'type' => 'switcher',
                'title' => 'Show File Name in Top',
                'dependency' => ['eov_document_source', '==', 'library'],
            ),
            array(
                'id' => 'eov_download_button',
                'type' => 'switcher',
                'title' => 'Show Downlaod Button On Top',
                'dependency' => ['eov_document_source', '==', 'library'],
            ),
            array(
                'id' => 'eov_right_click',
                'type' => 'switcher',
                'title' => 'Disable Right Click',
            ),
            array(
                'id' => 'eov_disable_fullscreen',
                'type' => 'switcher',
                'title' => 'Disable Fullscreen',
                'default' => 0
            ),

        ),
    ));
    // Create a section
    /*  CSF::createSection( $prefix, array(
'title'  => 'Viewer Settings (Optional)',
'fields' => array(
// A textarea field

)
) );
 */

}

// function eov_exclude_fields_before_save( $data ) {

//   $exclude = array(
//     'eov_view_type'
//   );

//   foreach ( $exclude as $id ) {
//     unset( $data[$id] );
//   }

//   return $data;

// }

// add_filter( 'csf_sc__save', 'eov_exclude_fields_before_save', 10, 1 );
