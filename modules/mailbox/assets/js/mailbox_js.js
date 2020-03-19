/**
 * Update email status 
 */
function update_field(group, action, value, mail_id){
    var data = {};
    data.group = group;
    data.action = action;
    data.value = value;
    data.id = mail_id;
    data.type = 'inbox';     
    if(group == 'detail'){
        data.type = mailtype; 
    }
    $.post(admin_url + 'mailbox/update_field', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);            
            if(group == 'detail'){
                window.location.reload();
            } else {
                reload_mailbox_tables();
            }
            
        } else {
            alert_float('warning', response.message);
        }
    });
}

/**
 * Reload mailbox datagrid
 * @return 
 */
function reload_mailbox_tables() {
    var av_tasks_tables = ['.table-mailbox'];
    $.each(av_tasks_tables, function(i, selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().ajax.reload(null, false);
        }
    });
}

/**
 * Update multi-email 
 */
function update_mass(group, action, value){
    if(group == 'detail'){
        update_field(group, action, value, mailid);
    } else {
        if (confirm_delete()) {
            var table_mailbox = $('.table-mailbox');
            var rows = table_mailbox.find('tbody tr');
            var lstid = '';
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') === true) {
                    lstid = lstid + checkbox.val() + ',';
                }
            });
            update_field(group, action, value, lstid);
        }
    }
}