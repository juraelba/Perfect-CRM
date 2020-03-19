<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-email-group-heading"><?php echo _l('Send Email'); ?></h4>

<?php 
  $templates = $this->db->select('id, template_name')->from(db_prefix() . '_custom_email_template')->where('is_deleted', 0)->get()->result();

  $url = admin_url("custom_email_template/send_mail");
  echo form_open($url);
?>

<div class="form-group">

  <?php
    $allTemplates = array();
    foreach($templates as $template){

      $allTemplates[] = array(
        'name' => $template->id,
        'value' => $template->template_name
      );
    }
    $value = isset($status) ? $status : '';
    echo render_select('template_id',$allTemplates,array('name','value'),'Choose Template',$value);

    echo form_hidden('client_id', $client->userid);
    ?>

</div>

<button type="submit" class="btn btn-info mbot25">
  <i class="fa fa-paper-plane" aria-hidden="true"></i> 
  <?php echo _l('Send Email'); ?>
</button>

<?php echo form_close();?>