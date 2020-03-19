<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Maibox Controller
 */
class Mailbox extends AdminController
{
    /**
     * Controler __construct function to initialize options
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mailbox_model');
    }

    /**
     * Go to Mailbox home page
     * @return view
     */
    public function index()
    {
        $data['title'] = _l('mailbox');
        $group         = !$this->input->get('group') ? 'inbox' : $this->input->get('group');
        $data['group'] = $group;
        if($group == 'config'){
            $this->load->model('staff_model');
            $member = $this->staff_model->get(get_staff_user_id());
            $data['member'] = $member;
        }
        $this->load->view('mailbox', $data);
    }

    /**
     * Go to Compose Form
     * @param  integer $outbox_id 
     * @return view
     */
    public function compose($outbox_id = null)
    {
        $data['title'] = _l('mailbox');
        $group         = 'compose';
        $data['group'] = $group;
        if ($this->input->post()) {
            $data            = $this->input->post();                        
            $id              = $this->mailbox_model->add($data, get_staff_user_id(),$outbox_id);
            if ($id) {
                if($this->input->post('sendmail')=='draft'){
                    set_alert('success', _l('mailbox_email_draft_successfully', $id));
                    redirect(admin_url('mailbox?group=draft'));    
                } else {
                    set_alert('success', _l('mailbox_email_sent_successfully', $id));
                    redirect(admin_url('mailbox?group=sent'));    
                }                
            }
        }

        if(isset($outbox_id)){
            $mail = $this->mailbox_model->get($outbox_id,'outbox');
            $data['mail'] = $mail;
        }
        $this->load->view('mailbox', $data);
    }

    /**
     * Get list email to dislay on datagrid
     * @param  string $group
     * @return 
     */
    public function table($group = 'inbox'){
        if ($this->input->is_ajax_request()) {
            if($group == 'sent' || $group == 'draft'){
                $this->app->get_table_data(module_views_path('mailbox', 'table_outbox'),[
                    'group' => $group,
                ]);
            } else {
                $this->app->get_table_data(module_views_path('mailbox', 'table'),[
                    'group' => $group,
                ]);
            }
        }
    }

    /**
     * Go to Inbox Page
     * @param  integer $id
     * @return view
     */
    public function inbox($id){
        $inbox = $this->mailbox_model->get($id,'inbox'); 
        $this->mailbox_model->update_field('detail','read',1,$id,'inbox');
        $data['title'] = $inbox->subject;
        $group         = 'detail';
        $data['group'] = $group;   
        $data['inbox'] = $inbox;
        $data['type'] = 'inbox';
        $data['attachments'] = $this->mailbox_model->get_mail_attachment($id,'inbox');
        $this->load->view('mailbox', $data);    
    }

    /**
     * Go to Outbox Page
     * @param  integer $id
     * @return view
     */
    public function outbox($id){
        $inbox = $this->mailbox_model->get($id,'outbox'); 
        $data['title'] = $inbox->subject;
        $group         = 'detail';
        $data['group'] = $group;   
        $data['inbox'] = $inbox;
        $data['type'] = 'outbox';
        $data['attachments'] = $this->mailbox_model->get_mail_attachment($id,'outbox');
        $this->load->view('mailbox', $data);    
    }

    /**
     * update email status
     * @return json
     */
    public function update_field(){
        if ($this->input->post()) {
            $group = $this->input->post('group');
            $action = $this->input->post('action');
            $value = $this->input->post('value');
            $id = $this->input->post('id');
            $type = $this->input->post('type');
            if($action != 'trash'){
                if($value == 1){
                    $value = 0;
                } else {
                    $value = 1;
                }
            }
            $res = $this->mailbox_model->update_field($group,$action,$value,$id,$type);
            $message = _l('mailbox_'.$action).' '._l('mailbox_success');
            if($res == false){
                $message = _l('mailbox_'.$action).' '._l('mailbox_fail');
            }
            echo json_encode([
                'success' => $res,
                'message' => $message,
            ]);

        }

    }

    /**
     * Action for reply, reply all and forward
     * @param  integer $id     
     * @param  string $method 
     * @param  string $type   
     * @return view        
     */
    public function reply($id , $method = 'reply',$type = 'inbox'){        
        $mail = $this->mailbox_model->get($id,$type);
        $data['title'] = _l('mailbox');
        $group         = 'compose';
        $data['group'] = $group;
        if ($this->input->post()) {
            $data            = $this->input->post();   
            $data['reply_from_id'] = $id;
            $data['reply_type'] = $type;
            $id              = $this->mailbox_model->add($data, get_staff_user_id());
            if ($id) {
                set_alert('success', _l('mailbox_email_sent_successfully', $id));
                redirect(admin_url('mailbox?group=sent'));
            }
        }
        
        $data['group'] = $group;
        $data['type'] = 'reply';
        $data['action_type'] = $type;
        $data['method'] = $method;
        $data['mail'] = $mail;
        $this->load->view('mailbox', $data); 
    }

    /**
     * Configure password to receice email from email server
     * @return redirect
     */
    public function config(){
        if ($this->input->post()) {
            $res  = $this->mailbox_model->update_config($this->input->post(),get_staff_user_id());
            if ($res) {
                set_alert('success', _l('mailbox_email_config_successfully'));
                redirect(admin_url('mailbox'));
            }
        }
    }
    
}