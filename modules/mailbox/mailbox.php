<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Mailbox 
Description: Mailbox is a webmail client for Perfex's dashboard.
Version: 1.0.0
Requires at least: 2.3.2
*/

define('MAILBOX_MODULE_NAME', 'mailbox');
define('MAILBOX_MODULE_UPLOAD_FOLDER', module_dir_path(MAILBOX_MODULE_NAME, 'uploads'));

hooks()->add_action('after_cron_run', 'scan_email_server');
hooks()->add_action('app_admin_head', 'mailbox_add_head_components');
hooks()->add_action('app_admin_footer', 'mailbox_load_js');
hooks()->add_action('admin_init', 'mailbox_add_settings_tab');
hooks()->add_action('admin_init', 'mailbox_module_init_menu_items');
hooks()->add_filter('migration_tables_to_replace_old_links', 'mailbox_migration_tables_to_replace_old_links');


/**
 * Injects chat CSS
 * @return null
 */
function mailbox_add_head_components(){
    if (get_option('mailbox_enabled') == '1') {
        $CI = &get_instance();
        echo '<link href="' . base_url('modules/mailbox/assets/css/mailbox_styles.css') .'?v=' . $CI->app_scripts->core_version(). '"  rel="stylesheet" type="text/css" />';
    }
}

/**
 * Injects chat Javascript
 * @return null
 */
function mailbox_load_js(){
    if (get_option('mailbox_enabled') == '1') {
        $CI = &get_instance();
        echo '<script src="'.module_dir_url('mailbox', 'assets/js/mailbox_js.js').'?v=' . $CI->app_scripts->core_version().'"></script>';
    }
}

/**
 * Init mailbox module menu items in setup in admin_init hook
 * @return null
 */
function mailbox_module_init_menu_items()
{
    $CI = &get_instance();
    if (get_option('mailbox_enabled') == '1') {
        $badge = "";
        $num_unread = total_rows(db_prefix() . 'mail_inbox', ['read' => '0','to_staff_id' => get_staff_user_id()]);
        if($num_unread > 0){
            $badge = ' <span class="badge menu-badge bg-warning">' . total_rows(db_prefix() . 'mail_inbox', ['read' => '0','to_staff_id' => get_staff_user_id()]).'</span>';
        }

        $CI->app_menu->add_sidebar_menu_item('mailbox', [
            'name'     =>_l('mailbox').$badge,
            'href'     => admin_url('mailbox'),
            'icon'     => 'fa fa-envelope-square',
            'position' => 6,
        ]);
    }
}

/**
 * Init mailbox module setting menu items in setup in admin_init hook
 * @return null
 */ 
function mailbox_add_settings_tab()
{
    $CI = & get_instance();
    $CI->app_tabs->add_settings_tab('mailbox-settings', [
       'name'     => ''._l('mailbox_setting').'',
       'view'     => 'mailbox/mailbox_settings',
       'position' => 36,
   ]);
}

/**
 * mailbox migration tables to replace old links description
 * @param  array $tables 
 * @return array         
 */
function mailbox_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
                'table' => db_prefix() . 'mail_inbox',
                'field' => 'description',
            ];

    return $tables;
}

/**
 * Scan mailbox from mail-server
 * @return [bool] [true/false]
 */
function scan_email_server(){
    $enabled = get_option('mailbox_enabled');
    $imap_server = get_option('mailbox_imap_server');
    $encryption = get_option('mailbox_encryption');
    $folder_scan = get_option('mailbox_folder_scan');
    $check_every = get_option('mailbox_check_every');
    $unseen_email = get_option('mailbox_only_loop_on_unseen_emails');
    if($enabled == 1 && strlen($imap_server) > 0){
        $CI = &get_instance();
        $CI->db->select()
            ->from(db_prefix() . 'staff')
            ->where(db_prefix() . 'staff.mail_password !=', '');
        $staffs = $CI->db->get()->result_array();    
        require_once(APPPATH . 'third_party/php-imap/Imap.php');
        include_once(APPPATH . 'third_party/simple_html_dom.php');
        foreach ($staffs as $staff) {
            $last_run = $staff['last_email_check'];
            $staff_email = $staff['email'];
            $staff_id = $staff['staffid'];
            $email_pass = $staff['mail_password'];
            if (empty($last_run) || (time() > $last_run + ($check_every * 60))) {
                require_once(APPPATH . 'third_party/php-imap/Imap.php');
                $CI->db->where('staffid', $staff_id);
                $CI->db->update(db_prefix() . 'staff', [
                    'last_email_check' => time(),
                ]);                
                // open connection
                $imap = new Imap($imap_server, $staff_email, $email_pass, $encryption);
                if ($imap->isConnected() === false) {
                    log_activity('Failed to connect to IMAP from email: '.$staff_email, null);
                    continue;
                }
                if($folder_scan == ''){
                    $folder_scan = 'Inbox';
                }
                $imap->selectFolder($folder_scan);
                if ($unseen_email == 1) {
                    $emails = $imap->getUnreadMessages();
                } else {
                    $emails = $imap->getMessages();
                }  

                foreach ($emails as $email) {
                    $plainTextBody = $imap->getPlainTextBody($email['uid']);
                    $plainTextBody = trim($plainTextBody);
                    if (!empty($plainTextBody)) {
                        $email['body'] = $plainTextBody;
                    }
                    /*if(strpos($email['body'],'sFmB2605')){
                        continue;
                    }*/
                    $email['body'] = handle_google_drive_links_in_text($email['body']);
                    $email['body']       = prepare_imap_email_body_html($email['body']);
                    $data['attachments'] = [];                    
                    $data = [];;
                    $data['attachments'] = [];
                    if (isset($email['attachments'])) {
                        foreach ($email['attachments'] as $key => $at) {
                            $_at_name = $email['attachments'][$key]['name'];
                            // Rename the name to filename the model expects filename not name
                            unset($email['attachments'][$key]['name']);
                            $email['attachments'][$key]['filename'] = $_at_name;
                            $_attachment                            = $imap->getAttachment($email['uid'], $key);
                            $email['attachments'][$key]['data']     = $_attachment['content'];
                        }
                        // Add the attchments to data
                        $data['attachments'] = $email['attachments'];
                    } else {
                        // No attachments
                        $data['attachments'] = [];
                    }                    

                    // Check for To
                    $data['to'] = [];
                    if (isset($email['to'])) {
                        foreach ($email['to'] as $to) {
                            $data['to'][] = trim(preg_replace('/(.*)<(.*)>/', '\\2', $to));
                        }
                    }

                    // Check for CC
                    $data['cc'] = [];
                    if (isset($email['cc'])) {
                        foreach ($email['cc'] as $cc) {
                            $data['cc'][] = trim(preg_replace('/(.*)<(.*)>/', '\\2', $cc));
                        }
                    }

                    if (hooks()->apply_filters('imap_fetch_from_email_by_reply_to_header', 'true') == 'true') {
                        $replyTo = $imap->getReplyToAddresses($email['uid']);

                        if (count($replyTo) === 1) {
                            $email['from'] = $replyTo[0];
                        }
                    }
                    $from_email = preg_replace('/(.*)<(.*)>/', '\\2', $email['from']);
                    $data['fromname'] = preg_replace('/(.*)<(.*)>/', '\\1', $email['from']);
                    $data['fromname'] = trim(str_replace('"', '', $data['fromname']));
                    
                    $inbox = array();                    
                    $inbox['from_email'] = $email['from'];                    
                    $from_staff_id = get_staff_id_by_email(trim($from_email));
                    if($from_staff_id){
                        $inbox['from_staff_id'] = $from_staff_id;    
                    }                    
                    $inbox['to'] = implode(',', $data['to']);
                    $inbox['cc'] = implode(',', $data['cc']);
                    $inbox['sender_name'] = $data['fromname'];
                    $inbox['subject'] = $email['subject'];
                    $inbox['body'] = $email['body']; 
                    $inbox['to_staff_id'] = $staff_id;                            
                    $inbox['date_received']      =  date('Y-m-d H:i:s');
                    $inbox['folder'] = 'inbox';
                   
                    $CI->db->insert(db_prefix() . 'mail_inbox', $inbox);                    
                    $inbox_id = $CI->db->insert_id();
                    $path = MAILBOX_MODULE_UPLOAD_FOLDER .'/inbox/'. $inbox_id . '/';
                    foreach ($data['attachments'] as $attachment) {
                        $filename      = $attachment['filename'];
                        $filenameparts = explode('.', $filename);
                        $extension     = end($filenameparts);
                        $extension     = strtolower($extension);
                        $filename = implode(array_slice($filenameparts, 0, 0 - 1));
                        $filename = trim(preg_replace('/[^a-zA-Z0-9-_ ]/', '', $filename));
                        if (!$filename) {
                            $filename = 'attachment';
                        }
                        if (!file_exists($path)) {
                            mkdir($path, 0755);
                            $fp = fopen($path . 'index.html', 'w');
                            fclose($fp);
                        }
                        $filename = unique_filename($path, $filename . '.' . $extension);
                        $fp       = fopen($path . $filename, 'w');
                        fwrite($fp, $attachment['data']);
                        fclose($fp);
                        $matt = array();
                        $matt['mail_id']  = $inbox_id;
                        $matt['type']  = 'inbox'; 
                        $matt['file_name']  = $filename; 
                        $matt['file_type']  = get_mime_by_extension($filename); 
                        $CI->db->insert(db_prefix() . 'mail_attachment', $matt);
                        
                    }
                    if(count($data['attachments']) > 0){
                        $CI->db->where('id', $inbox_id);
                        $CI->db->update(db_prefix() . 'mail_inbox', [
                            'has_attachment' => 1,
                        ]);
                    }

                    if($inbox_id){
                        $imap->setUnseenMessage($email['uid']);    
                    }                   

                }
            }
        }
    } 

    return false;
    
}

/**
* Load the module helper
*/
$CI = & get_instance();
$CI->load->helper(MAILBOX_MODULE_NAME . '/mailbox');

/**
 * Register the activation mailbox
 */
register_activation_hook(MAILBOX_MODULE_NAME, 'mailbox_activation_hook');

/**
 * The activation function
 */
function mailbox_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register mailbox language files
 */
register_language_files(MAILBOX_MODULE_NAME, [MAILBOX_MODULE_NAME]);