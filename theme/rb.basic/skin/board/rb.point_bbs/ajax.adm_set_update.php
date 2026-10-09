<?php
include_once('../../../../../common.php');

$bo_table = isset($_POST['bo_table']) ? $_POST['bo_table'] : '';
$bo_1 = isset($_POST['bo_1']) ? $_POST['bo_1'] : '0';
$bo_2 = isset($_POST['bo_2']) ? $_POST['bo_2'] : '0';

if ($is_admin) {
    
    if($bo_table) {
        $sql =  " UPDATE {$g5['board_table']} SET bo_1_subj = '수수료(%)', bo_1 = '$bo_1', bo_2_subj = '수수료(P)', bo_2 = '$bo_2' WHERE bo_table = '{$bo_table}' ";
        sql_query($sql);
        
        $data = array('status' => 'ok');
        echo json_encode($data);
        
    } else { 
        $data = array('status' => 'no');
        echo json_encode($data);
    }

}