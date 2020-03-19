<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('contracts_notes_tab'); ?></h4>
<div class="col-md-12">

 <a href="#" class="btn btn-success mtop15 mbot10" onclick="slideToggle('.usernote'); return false;"><?php echo _l('new_note'); ?></a>
 <div class="clearfix"></div>
<div class="row">
    <hr class="hr-panel-heading" />
</div>
 <div class="clearfix"></div>
 <div class="usernote hide">
    <?php echo form_open(admin_url( 'misc/add_note/'.$client->userid.'/customer')); ?>
    <?php echo render_textarea( 'description', 'note_description', '',array( 'rows'=>5)); ?>
    <button class="btn btn-info pull-right mbot15">
        <?php echo _l( 'submit'); ?>
    </button>
    <?php echo form_close(); ?>
</div>
<div class="clearfix"></div>
<div class="mtop15">
    <table class="table dt-table scroll-responsive" data-order-col="2" data-order-type="desc">
        <thead>
            <tr>
                <th width="50%">
                    <?php echo _l( 'clients_notes_table_description_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'clients_notes_table_addedfrom_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'clients_notes_table_dateadded_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'options'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $noteIds=array();
            foreach($user_notes as $note){
                array_push($noteIds, $note['id']);
            ?>
            <tr>
              <td width="50%">
                <div data-note-description="<?php echo $note['id']; ?>">
                    <?php echo check_for_links($note['description']); ?>
                </div>
                <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide">
                    <div id= "edit<?php echo $note['id'] ?>" name="description" class="form-control" rows="4"><?php  echo clear_textarea_breaks($note['description']); ?></div>
                    <div class="text-right mtop15">
                      <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                      <button type="button" class="btn btn-info" onclick="get_edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                    </div>
                </div>
              </td>

              <td>
                <?php echo '<a href="'.admin_url( 'profile/'.$note[ 'addedfrom']). '">'.$note[ 'firstname'] . ' ' . $note[ 'lastname'] . '</a>' ?>
              </td>

              <td data-order="<?php echo $note['dateadded']; ?>">
                <?php if(!empty($note['date_contacted'])){ ?>
                    <span data-toggle="tooltip" data-title="<?php echo html_escape(_dt($note['date_contacted'])); ?>">
                        <i class="fa fa-phone-square text-success font-medium valign" aria-hidden="true"></i>
                    </span>
                <?php } ?>
                <?php echo _dt($note[ 'dateadded']); ?>
              </td>

              <td>
                <?php if($note['addedfrom'] == get_staff_user_id() || is_admin()){ ?>
                    <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                    <a href="<?php echo admin_url('misc/delete_note/'. $note['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>
<script src="https://cdn.ckeditor.com/4.8.0/full-all/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description', {
    skin: 'moono',
    enterMode: CKEDITOR.ENTER_BR,
    shiftEnterMode:CKEDITOR.ENTER_P,
    toolbar: [{ name: 'basicstyles', groups: [ 'basicstyles' ], items: [ 'Bold', 'Italic', 'Underline', 'TextColor'] }
                ],
    }
    );
    function createEditors(ids){
        for(var i=0;i<ids.length;i++){
            CKEDITOR.replace('edit'+ids[i], {
                skin: 'moono',
                enterMode: CKEDITOR.ENTER_BR,
                shiftEnterMode:CKEDITOR.ENTER_P,
                toolbar: [{ name: 'basicstyles', groups: [ 'basicstyles' ], items: [ 'Bold', 'Italic', 'Underline', 'TextColor'] }],
            });
        }
    }
    createEditors(<?php echo json_encode($noteIds); ?>);

    function get_edit_note(id){
        var editorId="edit"+id;
        var description = CKEDITOR.instances[editorId].getData();
        if (description !== '') {
        $.post(admin_url + 'misc/edit_note/' + id, {
            description: description
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success === true || response.success == 'true') {
                alert_float('success', response.message);
                $("body").find('[data-note-description="' + id + '"]').html(nl2br(description));
            }
        });
        toggle_edit_note(id);
        }
    }
</script>