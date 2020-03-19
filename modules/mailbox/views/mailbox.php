<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="panel_s mbot5">
                    <div class="">
                        <a href="<?php echo admin_url().'mailbox/compose'?>" class="btn btn-info display-block hidden-xs">
                            <i class="fa fa-edit"></i>
                            <?php echo _l('mailbox_compose');?>
                        </a>
                
                    </div>
                </div>               

                <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked customer-tabs" role="tablist">
                    <li class="<?php if($group == 'inbox'){echo 'active ';} ?>mail_tab_<?php echo $group; ?>">
                        <a data-group="inbox" href="<?php echo admin_url('mailbox?group=inbox'); ?>">
                            <i class="fa fa-inbox menu-icon" aria-hidden="true"></i>
                            <?php echo _l('mailbox_inbox'); ?>
                            <?php
                                $num_unread = total_rows(db_prefix() . 'mail_inbox', ['read' => '0','to_staff_id' => get_staff_user_id()]);
                                if($num_unread > 0){
                            ?>
                            <span class="badge menu-badge bg-warning"><?php echo $num_unread; ?></span>
                            <?php }  ?>
                        </a>
                    </li>
                    <li class="<?php if($group == 'starred'){echo 'active ';} ?>mail_tab_<?php echo $group; ?>">
                        <a data-group="starred" href="<?php echo admin_url('mailbox?group=starred'); ?>">
                            <i class="fa fa-star menu-icon orange" aria-hidden="true"></i>
                            <?php echo _l('mailbox_starred'); ?>
                        </a>
                    </li>
                    <li class="<?php if($group == 'sent'){echo 'active ';} ?>mail_tab_<?php echo $group; ?>">
                        <a data-group="sent" href="<?php echo admin_url('mailbox?group=sent'); ?>">
                            <i class="fa fa-envelope-o menu-icon" aria-hidden="true"></i>
                            <?php echo _l('mailbox_sent'); ?>
                        </a>
                    </li>
                    <li class="<?php if($group == 'important'){echo 'active ';} ?>mail_tab_<?php echo $group; ?>">
                        <a data-group="important" href="<?php echo admin_url('mailbox?group=important'); ?>">
                            <i class="fa fa-bookmark menu-icon red" aria-hidden="true"></i>
                            <?php echo _l('mailbox_important'); ?>
                        </a>
                    </li>
                    <li class="<?php if($group == 'draft'){echo 'active ';} ?>mail_tab_<?php echo $group; ?>">
                        <a data-group="draft" href="<?php echo admin_url('mailbox?group=draft'); ?>">
                            <i class="fa fa-file-o menu-icon" aria-hidden="true"></i>
                            <?php echo _l('mailbox_draft'); ?>
                        </a>
                    </li>
                    <li class="<?php if($group == 'trash'){echo 'active ';} ?>mail_tab_<?php echo $group; ?>">
                        <a data-group="trash" href="<?php echo admin_url('mailbox?group=trash'); ?>">
                            <i class="fa fa-trash-o menu-icon" aria-hidden="true"></i>
                            <?php echo _l('mailbox_trash'); ?>
                        </a>
                    </li>
                    <li class="<?php if($group == 'config'){echo 'active ';} ?>mail_tab_<?php echo $group; ?>">
                        <a data-group="trash" href="<?php echo admin_url('mailbox?group=config'); ?>">
                            <i class="fa fa-cogs menu-icon" aria-hidden="true"></i>
                            <?php echo _l('mailbox_config'); ?>
                        </a>
                    </li>
                  
                </ul>
            </div>
            <div class="col-md-9">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tab-content">
                            <h4 class="customer-profile-group-heading">                                
                                <?php if($group== "detail"){
                                    echo $title;
                                } else {
                                    echo _l('mailbox_'.$group);    
                                }
                                ?>                                    
                            </h4>
                            <?php if($group != 'compose' && $group != 'config'){?>
                            <div class="horizontal-scrollable-tabs preview-tabs-top">
                                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                        <?php if($group=='inbox' || $group =='starred' || $group=='important' || ($group == 'detail' && isset($type) && $type!='outbox')){?>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_add_star");?>">
                                            <a href="Javascript:void(0)" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab" onclick="update_mass('<?php echo $group;?>','starred',0)">
                                                <i class="fa fa-star orange" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_remove_star");?>">
                                            <a href="Javascript:void(0)" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab" onclick="update_mass('<?php echo $group;?>','starred',1)">
                                                <i class="fa fa-star-o" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_mark_as_important");?>">
                                            <a href="Javascript:void(0)" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab" onclick="update_mass('<?php echo $group;?>','important',0)">
                                                <i class="fa fa-bookmark red" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_mark_as_not_important");?>">
                                            <a href="Javascript:void(0)" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab" onclick="update_mass('<?php echo $group;?>','important',1)">
                                                <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                                            </a>
                                        </li>                                        
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_mark_as_unread");?>">
                                            <a href="Javascript:void(0)" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab" onclick="update_mass('<?php echo $group;?>','read',1)">
                                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_mark_as_read");?>">
                                            <a href="Javascript:void(0)" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab" onclick="update_mass('<?php echo $group;?>','read',0)">
                                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_delete");?>">
                                            <a href="Javascript:void(0)" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab" onclick="update_mass('<?php echo $group;?>','trash',1)">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <?php if($group == "detail"){?>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_reply");?>">
                                            <a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/reply/'.$type;?>">
                                                <i class="fa fa-mail-reply" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_reply_all");?>">
                                            <a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/replyall/'.$type;?>">
                                                <i class="fa fa-mail-reply-all" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li role="presentation" data-toggle="tooltip" title="" class="tab-separator" data-original-title="<?php echo _l("mailbox_forward");?>">
                                            <a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/forward/'.$type;?>">
                                                <i class="fa fa-mail-forward" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <?php }?>
                                    </ul>                    
                                </div>                                
                            </div>    
                            <?php }?>                                                    
                            <div class="tab-content">
                                <?php if($group == 'compose' && !isset($type)){
                                    $this->load->view('mailbox/mailbox_compose'); 
                                } else if($group == 'compose' && $type=='reply'){
                                    $this->load->view('mailbox/mailbox_reply'); 
                                } else if($group == 'detail' && $type=='inbox'){
                                    $this->load->view('mailbox/mailbox_detail'); 
                                } else if($group == 'detail' && $type=='outbox'){
                                    $this->load->view('mailbox/mailbox_detail_outbox'); 
                                } else if($group == 'config'){
                                    $this->load->view('mailbox/mailbox_config'); 
                                } else {?>
                                    <?php
                                     $table_data = array();
                                     $obj = array(
                                         'name'=>_l('mailbox_from'),
                                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-mailbox-from')
                                        );
                                     if($group == 'sent'){
                                        $obj = array(
                                         'name'=>_l('mailbox_to'),
                                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-mailbox-to')
                                        );
                                     }
                                     $_table_data = array(
                                      '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="mailbox"><label></label></div>',                                                                             
                                         $obj
                                        ,
                                         array(
                                         'name'=>_l('mailbox_subject'),
                                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-mailbox-subject')
                                        ),
                                         array(
                                         'name'=>_l('mailbox_date'),
                                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-mailbox-date')
                                        ),                                        
                                      );
                                     foreach($_table_data as $_t){
                                        array_push($table_data,$_t);
                                     }                                     

                                     $table_data = hooks()->apply_filters('mailbox_table_columns', $table_data);

                                     render_datatable($table_data,'mailbox',[],[
                                           'data-last-order-identifier' => 'mailbox',
                                           'data-default-order'         => get_table_last_order('mailbox'),
                                     ]);
                                     ?>
                                <?php } ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
	"use strict";

    $(function(){
        init_btn_with_tooltips();   
        init_tabs_scrollable();   
        var webmailTableNotSortable = [0];
        initDataTable('.table-mailbox', admin_url + 'mailbox/table/<?php echo $group;?>', 'undefined', webmailTableNotSortable, 'undefined', [2, 'desc']);
        appValidateForm($('#mailbox_config_form'), {
           email: 'required',
           mail_password: 'required',           
        });
    });
</script>
</body>
</html>