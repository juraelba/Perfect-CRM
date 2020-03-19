<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_add_edit_profile'); ?></h4>
<div class="row">
   <?php echo form_open($this->uri->uri_string(),array('class'=>'client-form','autocomplete'=>'off')); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      <div class="horizontal-scrollable-tabs">
         <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
         <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
         <div class="horizontal-tabs">
            <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
               <li role="presentation" class="<?php if(!$this->input->get('tab')){echo 'active';}; ?>">
                  <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                  <?php echo _l( 'customer_profile_details'); ?>
                  </a>
               </li>
               <?php
                  $customer_custom_fields = false;
                  if(total_rows(db_prefix().'customfields',array('fieldto'=>'customers','active'=>1)) > 0 ){
                       $customer_custom_fields = true;
                   ?>
               <li role="presentation" class="<?php if($this->input->get('tab') == 'custom_fields'){echo 'active';}; ?>" style="display:none">
                  <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
                  <?php echo hooks()->apply_filters('customer_profile_tab_custom_fields_text', _l( 'custom_fields')); ?>
                  </a>
               </li>
               <?php } ?>
               
               <li role="presentation">
                  <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                  <?php echo _l( 'billing_shipping'); ?>
                  </a>
               </li>
               <?php hooks()->do_action('after_customer_billing_and_shipping_tab', isset($client) ? $client : false); ?>
               <?php if(isset($client)){ ?>
               <li role="presentation">
                  <a href="#customer_admins" aria-controls="customer_admins" role="tab" data-toggle="tab">
                  <?php echo _l( 'customer_admins' ); ?>
                  </a>
               </li>
               <?php hooks()->do_action('after_customer_admins_tab',$client); ?>
               <?php } ?>
            </ul>
         </div>
      </div>
      <div class="tab-content mtop15">
         <?php hooks()->do_action('after_custom_profile_tab_content',isset($client) ? $client : false); ?>
         <?php if($customer_custom_fields) { ?>
         <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'custom_fields'){echo ' active';}; ?>" id="custom_fields">
            <?php $rel_id=( isset($client) ? $client->userid : false); ?>
            <?php //echo render_custom_fields( 'customers',$rel_id); ?>
         </div>
         <?php } ?>
         <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">
            <div class="row">
               <div class="col-md-12 mtop15 <?php if(isset($client) && (!is_empty_customer_company($client->userid) && total_rows(db_prefix().'contacts',array('userid'=>$client->userid,'is_primary'=>1)) > 0)) { echo ''; } else {echo ' hide';} ?>" id="client-show-primary-contact-wrapper">
                  <div class="checkbox checkbox-info mbot20 no-mtop col-md-10">
                     <input type="checkbox" name="show_primary_contact"<?php if(isset($client) && $client->show_primary_contact == 1){echo ' checked';}?> value="1" id="show_primary_contact">
                     <label for="show_primary_contact"><?php echo _l('show_primary_contact',_l('invoices').', '._l('estimates').', '._l('payments').', '._l('credit_notes')); ?></label>
                  </div>
                  <button type="button" class="btn btn-info load_data_table col-md-2" data-toggle="modal" data-target="#sendEmailModal" >
                     <i class="fa fa-paper-plane" aria-hidden="true"></i>
                     <?php echo _l('Send Email'); ?>
                  </button>
               </div>
               <div class="col-md-6">
                  <?php $value=( isset($client) ? $client->company : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                  <?php echo render_input( 'company', 'client_company',$value,'text',$attrs); ?>
                  <div id="company_exists_info" class="hide"></div>
                  <?php if(get_option('company_requires_vat_number_field') == 1){
                     $value=( isset($client) ? $client->vat : '');
                     echo render_input( 'vat', 'client_vat_number',$value);
                     } ?>
                  <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                  <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>
                  <?php if((isset($client) && empty($client->website)) || !isset($client)){
                     $value=( isset($client) ? $client->website : '');
                     echo render_input( 'website', 'client_website',$value);
                     } else { ?>
                  <div class="form-group">
                     <label for="website"><?php echo _l('client_website'); ?></label>
                     <div class="input-group">
                        <input type="text" name="website" id="website" value="<?php echo $client->website; ?>" class="form-control">
                        <div class="input-group-addon">
                           <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                        </div>
                     </div>
                  </div>
                  <?php }
                     $selected = array();
                     if(isset($customer_groups)){
                       foreach($customer_groups as $group){
                          array_push($selected,$group['groupid']);
                       }
                     }
                     if(is_admin() || get_option('staff_members_create_inline_customer_groups') == '1'){
                      echo render_select_with_input_group('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,'<a href="#" data-toggle="modal" data-target="#customer_group_modal"><i class="fa fa-plus"></i></a>',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                      } else {
                        echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                      }
                     ?>
                  <?php if(!isset($client)){ ?>
                  <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
                  <?php }
                     $s_attrs = array('data-none-selected-text'=>_l('system_default_string'));
                     $selected = '';
                     if(isset($client) && client_have_transactions($client->userid)){
                        $s_attrs['disabled'] = true;
                     }
                     foreach($currencies as $currency){
                        if(isset($client)){
                          if($currency['id'] == $client->default_currency){
                            $selected = $currency['id'];
                         }
                      }
                     }
                            // Do not remove the currency field from the customer profile!
                     echo render_select('default_currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$selected,$s_attrs); ?>
                  <?php if(get_option('disable_language') == 0){ ?>
                  <div class="form-group select-placeholder">
                     <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
                     </label>
                     <select name="default_language" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""><?php echo _l('system_default_string'); ?></option>
                        <?php foreach($this->app->get_available_languages() as $availableLanguage){
                           $selected = '';
                           if(isset($client)){
                              if($client->default_language == $availableLanguage){
                                 $selected = 'selected';
                              }
                           }
                           ?>
                        <option value="<?php echo $availableLanguage; ?>" <?php echo $selected; ?>><?php echo ucfirst($availableLanguage); ?></option>
                        <?php } ?>
                     </select>
                  </div>
                  <?php } ?>
               </div>
               <div class="col-md-6">
                  <?php $value=( isset($client) ? $client->address : ''); ?>
                  <?php echo render_textarea( 'address', 'client_address',$value); ?>
                  <?php $value=( isset($client) ? $client->city : ''); ?>
                  <?php echo render_input( 'city', 'client_city',$value); ?>
                  <?php $value=( isset($client) ? $client->state : ''); ?>
                  <?php echo render_input( 'state', 'client_state',$value); ?>
                  <?php $value=( isset($client) ? $client->zip : ''); ?>
                  <?php echo render_input( 'zip', 'client_postal_code',$value); ?>
                  <?php $countries= get_all_countries();
                     $customer_default_country = get_option('customer_default_country');
                     $selected =( isset($client) ? $client->country : $customer_default_country);
                     echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'clients_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                     ?>
               </div>
            </div>
               <?php foreach ($cfc as $cfc_ar) {?>
                  <?php if ($cfc_ar['name'] == "Accreditation Dates" || $cfc_ar['name'] == "Program Details"){?>
                     <?php echo render_custom_fields( 'customers',$rel_id, ['category_id' => $cfc_ar['id']], [], $cfc_ar['name']); ?>
                  <?php } ?>
               <?php } ?>
               <div class="panel-group" id="accordion">
                  <?php foreach ($cfc as $cfc_ar) {?>
                  <?php if ($cfc_ar['name'] != "Membership Works" & $cfc_ar['name'] != "Accreditation Dates" & $cfc_ar['name'] != "Program Details"){?>
                  <div class="panel panel-default">
                     <div class="panel-heading">
                        <h4 class="panel-title">
                           <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $cfc_ar['id']?>"> <span class="	glyphicon glyphicon-menu-down"></span>    <?php echo $cfc_ar['name']?></a>
                        </h4>
                     </div>
                     <div id="<?php echo $cfc_ar['id']?>" class="panel-collapse collapse">
                        <div class="panel-body">
                           <?php echo render_custom_fields( 'customers',$rel_id, ['category_id' => $cfc_ar['id']], [], $cfc_ar['name']); ?>
                        </div>
                     </div>
                  </div>
                  <?php } ?> 
                  <?php } ?> 
               </div>
         </div>
         <?php if(isset($client)){ ?>
         <div role="tabpanel" class="tab-pane" id="customer_admins">
            <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
            <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
            <?php } ?>
            <table class="table dt-table">
               <thead>
                  <tr>
                     <th><?php echo _l('staff_member'); ?></th>
                     <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                     <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <th><?php echo _l('options'); ?></th>
                     <?php } ?>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($customer_admins as $c_admin){ ?>
                  <tr>
                     <td><a href="<?php echo admin_url('profile/'.$c_admin['staff_id']); ?>">
                        <?php echo staff_profile_image($c_admin['staff_id'], array(
                           'staff-profile-image-small',
                           'mright5'
                           ));
                           echo get_staff_full_name($c_admin['staff_id']); ?></a>
                     </td>
                     <td data-order="<?php echo $c_admin['date_assigned']; ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                     <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <td>
                        <a href="<?php echo admin_url('clients/delete_customer_admin/'.$client->userid.'/'.$c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                     <?php } ?>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>
         <?php } ?>
         <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
         
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-md-6">
                        <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->billing_street : ''); ?>
                        <?php echo render_textarea( 'billing_street', 'billing_street',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                        <?php echo render_input( 'billing_city', 'billing_city',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                        <?php echo render_input( 'billing_state', 'billing_state',$value); ?>
                        <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                        <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
                        <?php $selected=( isset($client) ? $client->billing_country : '' ); ?>
                        <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                     </div>
                     <div class="col-md-6">
                        <h4 class="no-mtop">
                           <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                           <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
                        </h4>
                        <hr />
                        <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                        <?php echo render_textarea( 'shipping_street', 'shipping_street',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                        <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                        <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>
                        <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                        <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
                        <?php $selected=( isset($client) ? $client->shipping_country : '' ); ?>
                        <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>  
                     </div>
                     <?php if(isset($client) &&
                        (total_rows(db_prefix().'invoices',array('clientid'=>$client->userid)) > 0 || total_rows(db_prefix().'estimates',array('clientid'=>$client->userid)) > 0 || total_rows(db_prefix().'creditnotes',array('clientid'=>$client->userid)) > 0)){ ?>
                     <div class="col-md-12">
                        <div class="alert alert-warning">
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_all_other_transactions" id="update_all_other_transactions">
                              <label for="update_all_other_transactions">
                              <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
                              </label>
                           </div>
                           <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_credit_notes" id="update_credit_notes">
                              <label for="update_credit_notes">
                              <?php echo _l('customer_profile_update_credit_notes'); ?><br />
                              </label>
                           </div>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php echo form_close(); ?>
</div>
<?php if(isset($client)){ ?>
<?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('clients/assign_admins/'.$client->userid)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
               $selected = array();
               foreach($customer_admins as $c_admin){
                  array_push($selected,$c_admin['staff_id']);
               }
               echo render_select('customer_admins[]',$staff,array('staffid',array('firstname','lastname')),'',$selected,array('multiple'=>true),array(),'','',false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>
<?php } ?>
<?php $this->load->view('admin/clients/client_group'); ?>

<div class="modal fade" id="sendEmailModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('Send Email');?></h4>
            </div>
            <div class="modal-body">
               <?php
               $client_id = $this->uri->segment(4);

               $templates = $this->db->select('id, template_name')->from(db_prefix() . '_custom_email_template')->where('is_deleted', 0)->get()->result();
               $contacts = $this->db->select('id,firstname,lastname,email,is_primary')->from(db_prefix() . 'contacts')->where('userid',$client_id)->get()->result_array();

               $all_contacts = array();
               $primary_contact_id = array();
               foreach ($contacts as $contact){
                  if ($contact['is_primary'] == 1) {
                     $contact['is_primary'] = 'Primary';
                     $contact['full_name'] = ucwords($contact['firstname'].' '.$contact['lastname']).' ( Primary )';
                     $primary_contact_id[] = $contact['id'];
                     $all_contacts[] = $contact;
                  } else if ($contact['is_primary'] == 0) {
                     $contact['is_primary'] = 'Secondary';
                     $contact['full_name'] = ucwords($contact['firstname'].' '.$contact['lastname']).' ( Secondary )';
                     $all_contacts[] = $contact;
                  }
               }

               $url = admin_url("custom_email_templates/send_mail");
               echo form_open($url,array('enctype'=>'multipart/form-data'));
               ?>

               <div class="form-group pull-right">
                  <button type="button" class="btn btn-info load_data_table" data-toggle="modal" data-target="#viewSentEmail"  data-companyId="<?php echo isset($client_id) ? $client_id : '';?>">View sent email </button>
               </div>

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
                  echo render_select('template_id',$allTemplates,array('name','value'),_l('choose_template'),$value,array(),array(),'','template_class');

                  echo form_hidden('client_id', $client->userid);
                  ?>

               </div>
               <!--<input type="hidden" value="<?php /*echo isset($client_id) ? $client_id : '';*/?>" name="company">-->
               <div class="form-group">

                  <?php
                  $value = isset($primary_contact_id) ? $primary_contact_id : array();
                  echo render_select('contact[]',$all_contacts,array('id','full_name'),_l('select_customer'),$value,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                  ?>

               </div>

               <div class="row visibility_cls">
                  <div class="col-md-12">
                     <?php
                     echo render_input('subject', _l('subject'));
                     ?>
                     <?php
                     echo render_textarea('body', _l('body'),'', array(), array(), '', 'tinymce');
                     ?>
                  <div class="col-md-6">
                     <div class="form-group">
                           <label for="email_template_attachment" class="control-label"><?php echo _l('attachment');?></label>
                           <input type="file" id="email_template_attachment" name="email_template_attachment" class="form-control">
                     </div>
                     <input type="hidden" name="email_template_attachment_hidden" id="email_template_attachment_hidden">

                  </div>
                  <div class="col-md-2 append_icon" id="append_icon" style="padding-top: 26px;"></div>
               </div>
            </div>
               <button type="submit" class="btn btn-info mbot25 visibility_cls" onclick="message()">
                  <i class="fa fa-paper-plane" aria-hidden="true"></i>
                  <?php echo _l('Send Email'); ?>
               </button>

            <?php echo form_close();?>
            <div class="modal fade" id="viewSentEmail" role="dialog">
               <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                     <div class="modal-header" style="background-color: grey">
                        <button type="button" class="close" data-dismiss="modal2" onclick="closeModal2()">&times;</button>
                        <h4 class="modal-title"><?php echo _l('view_sent_email');?></h4>
                     </div>
                     <div class="modal-body">
                        <div class="row">
                           <div class="col-md-12" id="cet_datatable_div">
                           <?php render_datatable(array(
                              _l('ID'),
                              _l('template_name'),
                              _l('subject'),
                              _l('contact_name'),
                              _l('status'),
                              _l('location'),
                              _l('created_on'),
                              _l('Actions'),
                           ), 'view_sent_email'); ?>
                           </div>
                           <div class="col-md-12 sent_cet_data_display"></div>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal2" onclick="closeModal2()">Close</button>
                        <button type="button" class="btn btn-info sent_cet_data_display_btn" style="display: none;">Back</button>
                     </div>
                  </div>
               </div>
            </div>
  
         </div>
    </div>
</div>

<script>
   function closeModal2 () {
         $('#viewSentEmail').modal('hide');
   }

   function message () {
      alert_float('success', 'Email Sent!');
   }
</script>

