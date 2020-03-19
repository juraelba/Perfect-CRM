<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="clearfix mtop20"></div>
<div class="">
  <div class="email-media">
      <div class="media mt-0">
        <?php echo staff_profile_image($inbox->sender_staff_id, ['mr-2 rounded-circle',]);?>        
        
        <div class="media-body">
          <div class="float-right d-md-flex fs-15">
            <small class="mr-2"><?php echo _dt($inbox->date_sent)?></small>            
            <small class="mr-2 cursor"><a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/reply/outbox';?>"><i class="fa fa-reply text-dark" data-toggle="tooltip" title="" data-original-title="<?php echo _l('mailbox_reply')?>"></i></a></small>
          </div>
          <div class="media-title text-dark font-weight-semiblod"><?php echo $inbox->sender_name;?> <span class="text-muted">( <?php echo get_staff_email_by_id($inbox->sender_staff_id);?> )</span></div>
          <p class="mb-0 font-weight-semiblod">To: <?php echo $inbox->to;?></p>
          <p class="mb-0 font-weight-semiblod">Cc: <?php echo $inbox->cc;?></p>
        </div>
      </div>
    </div>
    <div class="eamil-body">
        <p>
          <?php echo $inbox->body?>
        </p>
        <hr>
        <?php if($inbox->has_attachment > 0){?>
        <div class="email-attch">          
          <p><?php echo _l('mailbox_file_attachment')?></p>
          <div class="emai-img">
            <div class="">
               <?php foreach($attachments as $attachment){ 
                $attachment_url = module_dir_url(MAILBOX_MODULE_NAME) .'uploads/'.$type.'/'. $inbox->id . '/'.$attachment['file_name'];
                ?>
                <div class="mbot15 row" data-attachment-id="<?php echo $attachment['id']; ?>">
                     <div class="col-md-8">
                        <div class="pull-left"><i class="<?php echo get_mime_class($attachment['file_type']); ?>"></i></div>
                        <a href="<?php echo $attachment_url; ?>" target="_blank"><?php echo $attachment['file_name']; ?></a>
                        <br />
                        <small class="text-muted"> <?php echo $attachment['file_type']; ?></small>
                     </div>
                   </div>

               <?php }?>
              
            </div>
          </div>
        </div>
        <?php }?>
      </div>

      <div class="pull-right">      
      <a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/reply/outbox';?>" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-warning">
        <i class="fa fa-reply"></i></i> <?php echo _l('mailbox_reply'); ?></a>
      <a href="<?php echo admin_url().'mailbox/reply/'.$inbox->id.'/forward/outbox';?>" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info">
          <i class="fa fa-share"></i>
          <?php echo _l('mailbox_forward'); ?>          
        </a>
    </div>
</div>
<script>
  var mailid = <?php echo $inbox->id;?>;
  var mailtype = '<?php echo $type;?>';
</script>