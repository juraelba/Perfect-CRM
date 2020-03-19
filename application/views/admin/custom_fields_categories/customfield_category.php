<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                            <?php if(isset($custom_field_category_category)){ ?>
                            <a href="<?php echo admin_url('custom_fields_categories/field'); ?>" class="btn btn-success pull-right"><?php echo _l('new_custom_field_category'); ?></a>
                            <div class="clearfix"></div>
                            <?php } ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <div class="customers_field_info mbot25 alert alert-info<?php if(isset($custom_field_category) && $custom_field_category->fieldto != 'customers' || !isset($custom_field_category)){echo ' hide';} ?>">
                            <?php echo _l('custom_field_info_format_embed_info',array(
                                _l('clients'),
                                '<a href="'.admin_url('settings?group=clients#settings[customer_info_format]').'" target="_blank">'.admin_url('settings?group=clients').'</a>'
                                )); ?>
                            </div>
                             <div class="items_field_info mbot25 alert alert-warning<?php if(isset($custom_field_category) && $custom_field_category->fieldto != 'items' || !isset($custom_field_category)){echo ' hide';} ?>">
                                Custom fields for items can't be included in calculation of totals.
                            </div>
                            <div class="proposal_field_info mbot25 alert alert-info<?php if(isset($custom_field_category) && $custom_field_category->fieldto != 'proposal' || !isset($custom_field_category)){echo ' hide';} ?>">
                                <?php echo _l('custom_field_info_format_embed_info',array(
                                    _l('proposals'),
                                    '<a href="'.admin_url('settings?group=sales&tab=proposals#settings[proposal_info_format]').'" target="_blank">'.admin_url('settings?group=sales&tab=proposals').'</a>'
                                    )); ?>
                                </div>

                                <?php echo form_open($this->uri->uri_string()); ?>
                                <?php
                                $disable = '';
                                if(isset($custom_field_category)){
                                  if(total_rows(db_prefix().'customfieldsvalues',array('fieldid'=>$custom_field_category->id,'fieldto'=>$custom_field_category->fieldto)) > 0){
                                    $disable = 'disabled';
                                }
                            }
                            ?>
                            <div class="clearfix"></div>
                            <?php $value = (isset($custom_field_category) ? $custom_field_category->name : ''); ?>
                            <?php echo render_input('name','custom_field_category_name',$value); ?>
                           <div class="select-placeholder form-group">
                                <label for="nav_type"><?php echo _l('custom_field_category_add_edit_nav_type'); ?></label>
                            <select name="nav_type" id="type" class="selectpicker"<?php if(isset($custom_field_category) && total_rows(db_prefix().'customfieldsvalues',array('fieldid'=>$custom_field_category->id,'fieldto'=>$custom_field_category->fieldto)) > 0){echo ' disabled';} ?> data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" required>
                                <option value=""></option>
                                <option value="1" <?php if(isset($custom_field_category) && $custom_field_category->type == 1){echo 'selected';} ?>>Profile</option>
                            </select>
                           </div>
                            <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
<script>
var pdf_fields = <?php echo json_encode($pdf_fields); ?>;
var client_portal_fields = <?php echo json_encode($client_portal_fields); ?>;
var client_editable_fields = <?php echo json_encode($client_editable_fields); ?>;
$(function () {
    appValidateForm($('form'), {
        fieldto: 'required',
        name: 'required',
        type: 'required',
        bs_column: 'required',
        options: {
            required: {
                depends: function (element) {
                    var type = $('#type').val();
                    return type == 'select' || type == 'checkbox' || type == 'multiselect';
                }
            }
        }
    });
    $('form').on('submit', function () {
        $('#fieldto,#type').prop('disabled', false);
        return true;
    });
    $('select[name="fieldto"]').on('change', function () {
        var field = $(this).val();
        if ($.inArray(field, pdf_fields) !== -1) {
            $('.show-on-pdf').removeClass('hide');
        } else {
            $('.show-on-pdf').addClass('hide');
        }

        if ($.inArray(field, client_portal_fields) !== -1) {
            $('.show-on-client-portal').removeClass('hide');
            $('.disalow_client_to_edit').removeClass('hide');

            if ($.inArray(field, client_editable_fields) !== -1) {
                $('.disalow_client_to_edit').removeClass('hide');
            } else {
                $('.disalow_client_to_edit').addClass('hide');
                $('.disalow_client_to_edit input').prop('checked', false);
            }
        } else {
            $('.show-on-client-portal').addClass('hide');
            $('.disalow_client_to_edit').addClass('hide');
        }
        if (field == 'tickets') {
            $('.show-on-ticket-form').removeClass('hide');
        } else {
            $('.show-on-ticket-form').addClass('hide');
            $('.show-on-ticket-form input').prop('checked', false);
        }

        if (field == 'customers') {
            $('.customers_field_info').removeClass('hide');
        } else {
            $('.customers_field_info').addClass('hide');
        }

        if (field == 'items') {
            $('.items_field_info').removeClass('hide');
        } else {
            $('.items_field_info').addClass('hide');
        }

        if (field == 'company') {
            $('.company_field_info').removeClass('hide');
        } else {
            $('.company_field_info').addClass('hide');
        }

        if (field == 'proposal') {
            $('.proposal_field_info').removeClass('hide');
        } else {
            $('.proposal_field_info').addClass('hide');
        }

        if (field == 'company') {
            $('#only_admin').prop('disabled', true).prop('checked', false);
            $('input[name="required"]').prop('disabled', true).prop('checked', false);
            $('#show_on_table').prop('disabled', true).prop('checked', false);
            $('#show_on_client_portal').prop('disabled', true).prop('checked', true);
        } else if(field =='items'){
            $('#type option[value="link"]').prop('disabled', true);
            $('#show_on_table').prop('disabled', true).prop('checked', true);
            $('#show_on_pdf').prop('disabled', true).prop('checked', true);
            $('#only_admin').prop('disabled', true).prop('checked', false);
        } else {
            $('#only_admin').prop('disabled', false).prop('checked',false);
            $('input[name="required"]').prop('disabled', false).prop('checked',false);
            $('#show_on_table').prop('disabled', false).prop('checked',false);
            $('#show_on_client_portal').prop('disabled', false).prop('checked',false);
            $('#show_on_pdf').prop('disabled', false).prop('checked',false);
            $('#type option[value="link"]').prop('disabled', false);
        }
        $('#type').selectpicker('refresh');
    });
    $('select[name="type"]').on('change', function () {
        var type = $(this).val();
        var options_wrapper = $('#options_wrapper');
        var display_inline = $('.display-inline-checkbox')
        if (type == 'select' || type == 'checkbox' || type == 'multiselect') {
            options_wrapper.removeClass('hide');
            if (type == 'checkbox') {
                display_inline.removeClass('hide');
            } else {
                display_inline.addClass('hide');
                display_inline.find('input').prop('checked', false);
            }
        } else {
            options_wrapper.addClass('hide');
            display_inline.addClass('hide');
            display_inline.find('input').prop('checked', false);
        }
    });

    $('body').on('change', 'input[name="only_admin"]', function () {
        $('#show_on_client_portal').prop('disabled', $(this).prop('checked')).prop('checked', false);
        $('#disalow_client_to_edit').prop('disabled', $(this).prop('checked')).prop('checked', false);
    });
});
</script>
</body>
</html>
