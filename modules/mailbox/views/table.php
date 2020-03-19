<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'sender_name',    
    'subject',
    'date_received',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'mail_inbox';

$join = [];
$where = [];
if($group == 'inbox'){
    array_push($where, ' AND trash = 0');
} else if($group == 'starred'){
    array_push($where, ' AND trash = 0 AND stared = 1');
} else if($group == 'important'){
    array_push($where, ' AND trash = 0 AND important = 1');
} else if($group == 'trash'){
    array_push($where, ' AND trash = 1');
}
array_push($where, ' AND to_staff_id = '.get_staff_user_id());
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','has_attachment','stared','important','body',db_prefix() . 'mail_inbox.read']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $read = "bold";
    if($aRow['read'] == 1){
        $read = "";
    }
    $starred = "fa-star-o";    
    $msg_starred = _l('mailbox_add_star');
    $important = "fa-bookmark-o";
    $msg_important = _l('mailbox_mark_as_important');
    if($aRow['stared']==1){
        $starred = "fa-star orange";
        $msg_starred = _l('mailbox_remove_star');
    }
    if($aRow['important']==1){
        $important = "fa-bookmark red";
        $msg_important = _l('mailbox_mark_as_not_important');
        
    }
    $has_attachment = "";
    if($aRow['has_attachment']>0){
        $has_attachment = '<i class="fa fa-paperclip pull-right" data-toggle="tooltip" title="'._l('mailbox_file_attachment').'" data-original-title="fa-paperclip"></i>';
    }

    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>
                <a class="btn btnIcon" data-toggle="tooltip" title="" data-original-title="'. $msg_starred.'" onclick="update_field(\''.$group.'\',\'starred\','.$aRow['stared'].','.$aRow['id'].');"><i class="fa '.$starred.'"></i></a>
                <a class="btn btnIcon" data-toggle="tooltip" title="" data-original-title="'. $msg_important.'" onclick="update_field(\''.$group.'\',\'important\','.$aRow['important'].','.$aRow['id'].');"><i class="fa '.$important.'"></i></a>                
                <a class="btn btnIcon" data-toggle="tooltip" title="" data-original-title="'. _l('mailbox_delete').'" onclick="update_field(\''.$group.'\',\'trash\',1,'.$aRow['id'].');"><i class="fa fa-trash-o"></i></a>';
    

    $content = '<a href="'.admin_url().'mailbox/inbox/'.$aRow['id'].'">';
    $row[] = $content.'<span class="'.$read.'">'.$aRow['sender_name'].'</span></a>';
    $row[] = $content.'<span class="'.$read.'">'.$aRow['subject'].' - </span><span class="text-muted">'.clear_textarea_breaks(text_limiter($aRow['body'],8,'...')).'</span>'.$has_attachment.'</a>';    
    $row[] = $content.'<span class="'.$read.'">'._dt($aRow['date_received']).'</span></a>';

    $output['aaData'][] = $row;
}