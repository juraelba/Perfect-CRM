<?php defined('BASEPATH') or exit('No direct script access allowed'); 

$enabled = get_option('mailbox_enabled');
$imap_server = get_option('mailbox_imap_server');
$encryption = get_option('mailbox_encryption');
$folder_scan = get_option('mailbox_folder_scan');
$check_every = get_option('mailbox_check_every');
$unseen_email = get_option('mailbox_only_loop_on_unseen_emails');

?>

<div class="row">
    <div class="col-md-12">
        <div class="form-group"> 
            <label for="pusher_chat" class="control-label clearfix">
                <?php echo _l('mailbox_enable_option'); ?>
            </label> 
            <div class="radio radio-primary radio-inline">
                <input type="radio" id="y_opt_1_mailbox_enabled" name="settings[mailbox_enabled]" value="1"<?php if ($enabled == '1') {
                    echo ' checked';
                } ?>>
                <label for="y_opt_1_mailbox_enabled"><?php echo _l('settings_yes'); ?></label>
            </div> 
            <div class="radio radio-primary radio-inline">
                <input type="radio" id="y_opt_2_mailbox_enabled" name="settings[mailbox_enabled]" value="0" <?php if ($enabled == '0') {
                    echo ' checked';
                } ?>>
                <label for="y_opt_2_mailbox_enabled">
                    <?php echo _l('settings_no'); ?>
                </label>
            </div>
        </div> 
        <hr/>
        <?php echo render_input('settings[mailbox_imap_server]','leads_email_integration_imap',$imap_server); ?>
        <div class="form-group">
           <label for="encryption"><?php echo _l('leads_email_encryption'); ?></label><br />
           <div class="radio radio-primary radio-inline">
              <input type="radio" name="settings[mailbox_encryption]" value="tls" id="tls" <?php if($encryption == 'tls'){echo 'checked';} ?>>
              <label for="tls">TLS</label>
           </div>
           <div class="radio radio-primary radio-inline">
              <input type="radio" name="settings[mailbox_encryption]" value="ssl" id="ssl" <?php if($encryption == 'ssl'){echo 'checked';} ?>>
              <label for="ssl">SSL</label>
           </div>
           <div class="radio radio-primary radio-inline">
              <input type="radio" name="settings[mailbox_encryption]" value="" id="no_enc" <?php if($encryption == ''){echo 'checked';} ?>>
              <label for="no_enc"><?php echo _l('leads_email_integration_folder_no_encryption'); ?></label>
           </div>
        </div>
        <?php echo render_input('settings[mailbox_folder_scan]','leads_email_integration_folder',$folder_scan); ?>
        <?php echo render_input('settings[mailbox_check_every]','leads_email_integration_check_every',$check_every,'number',array('min'=>hooks()->apply_filters('leads_email_integration_check_every', 3),'data-ays-ignore'=>true)); ?>
        <div class="checkbox checkbox-primary">
           <input type="checkbox" name="mailbox_only_loop_on_unseen_emails" class="ays-ignore" id="mailbox_only_loop_on_unseen_emails" <?php if($unseen_email == 1){echo 'checked';} ?>>
           <label for="mailbox_only_loop_on_unseen_emails"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('leads_email_integration_only_check_unseen_emails_help'); ?>"></i>
           <?php echo _l('leads_email_integration_only_check_unseen_emails'); ?></label>
        </div>
    </div>
</div>

