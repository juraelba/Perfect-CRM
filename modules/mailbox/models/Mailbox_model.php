<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Mailbox Model
 */
class Mailbox_model extends App_Model
{
    /**
    * Controler __construct function to initialize options
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new ticket to database
     * @param mixed $data  ticket $_POST data
     * @param mixed $admin If admin adding the ticket passed staff id
     */
    public function add($data, $staff_id, $ob_id = null)
    {
        $outbox_id = '';
        $outbox = array();
    	$outbox['sender_staff_id'] = $staff_id;
    	$outbox['to'] = $data['to'];
    	$outbox['cc'] = $data['cc'];
    	$outbox['sender_name'] = get_staff_full_name($staff_id);
    	$outbox['subject'] = _strip_tags($data['subject']);
    	$outbox['body'] = _strip_tags($data['body']);        
        $outbox['body'] = nl2br_save_html($outbox['body']);
    	$outbox['date_sent']      = date('Y-m-d H:i:s');
        if(isset($data['reply_from_id'])){
            $outbox['reply_from_id'] = $data['reply_from_id'];
        }
        if(isset($data['reply_type'])){
            $outbox['reply_type'] = $data['reply_type'];
        }
        if(isset($data['sendmail']) && $data['sendmail']=='draft'){
            $outbox['draft']      =  1;
            $this->db->insert(db_prefix() . 'mail_outbox', $outbox);
            return true;
        }
        if(isset($ob_id)){
            $outbox['draft'] = 0;
            $this->db->where('id', $ob_id);
            $this->db->update(db_prefix() . 'mail_outbox', $outbox);
            $outbox_id = $ob_id;
        } else {
            $this->db->insert(db_prefix() . 'mail_outbox', $outbox);
            $outbox_id = $this->db->insert_id();    
        }
    	
    	$inbox = array();
    	$inbox['from_staff_id'] = $staff_id;
    	$inbox['to'] = $data['to'];
    	$inbox['cc'] = $data['cc'];
    	$inbox['sender_name'] = get_staff_full_name($staff_id);
    	$inbox['subject'] = _strip_tags($data['subject']);
    	$inbox['body'] = _strip_tags($data['body']);        
        $inbox['body'] = nl2br_save_html($inbox['body']);
    	$inbox['date_received']      = date('Y-m-d H:i:s');
    	$inbox['folder'] = 'inbox';
        $inbox['from_email'] = get_staff_email_by_id($staff_id);

    	$array_send_to = array();
    	$array_to = explode(";", $data['to']);
    	if(isset($array_to) && count($array_to) > 0){
    		foreach ($array_to as $value) {
    			$array_send_to[$value] = $value;
    		}
    	}
    	$array_cc = explode(";", $data['cc']);
    	if(isset($array_cc) && count($array_cc) > 0){
    		foreach ($array_cc as $value) {
    			$array_send_to[$value] = $value;
    		}
    	}

        $array_inbox_id = array();        
    	foreach ($array_send_to as $value) {
    		$to = get_staff_id_by_email(trim($value));
    		if($to > 0){
    			$d_inbox = $inbox;
    			$d_inbox['to_staff_id'] = $to; 
    			$this->db->insert(db_prefix() . 'mail_inbox', $d_inbox);
        		$inbox_id = $this->db->insert_id();
        		$array_inbox_id[] = $inbox_id;
    		}            
    	}
        $attachments = array();
    	if($outbox_id > 0){
    		if(count($array_inbox_id) > 0){
                foreach ($array_inbox_id as $inbox_id) {
                    $attachments = handle_mail_attachments($inbox_id,'inbox','attachments','copy');
                    if ($attachments) {
                        $this->insert_mail_attachments_to_database($attachments, $inbox_id,'inbox');
                    }
                }
            }

            $attachments = handle_mail_attachments($outbox_id,'outbox');
    		if ($attachments) {
                $this->insert_mail_attachments_to_database($attachments, $outbox_id,'outbox');
            }
            
    	}

        //Send email
        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0){
            $ci = &get_instance();
            $ci->email->initialize();
            $ci->load->library('email');    
            $ci->email->clear(true);
            $ci->email->from($inbox['from_email'], $inbox['sender_name']);
            $ci->email->to(str_replace(";", ",", $data['to']));
            if(isset($data['cc']) && strlen($data['cc']) > 0){
                $ci->email->cc($data['cc']);
            }
            $ci->email->subject($inbox['subject']);
            $ci->email->message($data['body']);
            foreach ($attachments as $attachment) {
                $attachment_url = module_dir_url(MAILBOX_MODULE_NAME) .'uploads/outbox/'. $outbox_id . '/'.$attachment['file_name'];
                $ci->email->attach($attachment_url);   
            }
            $ci->email->send(true);
        }

        return true;
    }

    /**
     * Insert mail attachments to database
     * @param  array  $attachments array of attachment
     * @param  mixed  $mail_id     
     */
    public function insert_mail_attachments_to_database($attachments, $mail_id,$type = 'inbox')
    {
        foreach ($attachments as $attachment) {
            $attachment['mail_id']  = $mail_id;
            $attachment['type']  = $type;            
            $this->db->insert(db_prefix() . 'mail_attachment', $attachment);
            $this->db->where('id', $mail_id);
            $this->db->update(db_prefix() . 'mail_'.$type, [
                'has_attachment' => 1,
            ]);
        }
    }

    /**
     * Get detail email by id and type
     * @param  integer $id   
     * @param  string $type 
     * @return row       
     */
    public function get($id, $type="inbox"){
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'mail_'.$type)->row();
    }

    /**
     * Update email status
     * @param  integer $group   
     * @param  string $action  
     * @param  integer $value  
     * @param  integer $mail_id 
     * @param  string $type    
     * @return bool          
     */
    public function update_field($group,$action,$value,$mail_id,$type = 'inbox'){
        if($action == 'starred'){
            $action = 'stared';
        }
        $arr_id = explode(",", $mail_id);
        foreach ($arr_id as $id) {
            if(strlen(trim($id)) > 0){
                if(($group == 'trash' || $group =='sent') && $action =='trash'){
                    if($group == 'sent'){
                        $type = 'outbox';
                    }
                    $this->db->where('id', $id);
                    $this->db->delete(db_prefix() . 'mail_'.$type);

                    $this->db->where('mail_id', $id);
                    $file = $this->db->get(db_prefix() . 'mail_attachment')->result_array();
                    foreach ($file as $f) {
                        $path           = MAILBOX_MODULE_UPLOAD_FOLDER .'/'.$type.'/'. $id . '/'.$f['file_name'];
                        if(file_exists($path)){
                            unlink($path);
                        }
                    }
                    $this->db->where('mail_id', $id);
                    $this->db->where('type', $type);
                    $this->db->delete(db_prefix() . 'mail_attachment');

                } else {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'mail_'.$type, [
                        $action => $value,
                    ]);
                }
            }
        }
        return true;
        
    }

    /**
     * Get email attachments
     * @param  integer $mail_id 
     * @param  string $type    
     * @return array          
     */
    public function get_mail_attachment($mail_id,$type='inbox'){
        $this->db->where('mail_id', $mail_id);
        $this->db->where('type', $type);
        return $this->db->get(db_prefix() . 'mail_attachment')->result_array();
    }

    /**
     * Update email configuration
     * @param  array $data     
     * @param  integer $staff_id 
     * @return bool           
     */
    public function update_config($data, $staff_id){
        unset($data['email']);
        $this->db->where('staffid', $staff_id);
        $this->db->update(db_prefix() . 'staff', $data);
        return true;
    }    
}