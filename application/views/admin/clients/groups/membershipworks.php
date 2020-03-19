<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('MembershipWorks'); ?></h4>
<?php $rel_id=( isset($client) ? $client->userid : false); ?>
    <?php foreach ($cfc as $cfc_ar) {?>
        <?php if ($cfc_ar['name'] == "Membership Works"){?>
                    <?php echo render_custom_fields( 'customers',$rel_id, ['category_id' => $cfc_ar['id']], [], $cfc_ar['name']); ?>
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-4" style="margin-top: -50px;">
                            <a target="_blank" rel="noopener noreferrer" href="https://membershipworks.com/admin/#!biz/id/<?php echo get_custom_field_value($rel_id, get_field_id(), 'customers', $format = true)?>/Profile" class="btn btn-info pull-left display-block mright5">
                            <?php echo _l(' ACCESS MEMBERSHIPWORKS PROFILE '); ?></a>
                        </div>
                    </div>
            </div>
        <?php } ?>
    <?php } ?>
<?php } ?>
<div id="contact_data"></div>
<div id="consent_data"></div>
