<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_open_multipart($this->uri->uri_string().'/config',array('id'=>'mailbox_config_form')); ?>
<div class="row">
    <div class="col-lg-12">
        <br>
        <?php echo _l('mailbox_user_pass_instructions'); ?>
        <br><br>
    </div>
    <div class="col-md-6">
        <?php $value = (isset($member) ? $member->email : ''); ?>
        <?php echo render_input('email','staff_add_edit_email',$value,'email',array('autocomplete'=>'off','readonly'=>'readonly')); ?>
    </div>
    <div class="col-md-6">
        <label for="mail_password" class="control-label"><?php echo _l('mailbox_email_password'); ?></label>
        <div class="input-group">
        	<?php $value = (isset($member) ? $member->mail_password : ''); ?>
	        <input type="password" class="form-control password" name="mail_password" value="<?php echo $value;?>" autocomplete="new-password">
	        <span class="input-group-addon">
	        <a href="#mail_password" class="show_password" onclick="showPassword('mail_password'); return false;"><i class="fa fa-eye"></i></a>
	        </span>	        
	    </div>
    </div>
</div>
<div class="row">
	<div class="col-md-12 center-block">
		<button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info">          
          <?php echo _l('save'); ?>          
        </button>
	</div>
</div>
<?php echo form_close(); ?>