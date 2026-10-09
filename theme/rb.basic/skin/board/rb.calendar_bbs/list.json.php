<?php
if (!isset($_GET['start']) || !isset($_GET['end'])) die();

$path = '../common.php';
for($i=0; $i<5; $i++)
{
    if(is_file($path))
    {
        include $path;
        break;
    }
    $path = '../'.$path;
}

$start = substr($_GET['start'], 0, 10);
$end = substr($_GET['end'], 0, 10);

$list = array();

if(isset($sca) && $sca) {
    $rst = sql_query("SELECT * FROM {$write_table} WHERE wr_is_comment = '0' AND wr_1 != '' and ca_name = '{$sca}' ");
} else {
    $rst = sql_query("SELECT * FROM {$write_table} WHERE wr_is_comment = '0' AND wr_1 != '' ");
}

while($row = sql_fetch_array($rst))
{
    if(isset($row['wr_2']) && $row['wr_2']) {
        $end_dates1 = strtotime($row['wr_2']."+1 days");
        $end_dates = date("Y-m-d", $end_dates1);
    } else { 
        $end_dates = $row['wr_1'];
    }

    if(isset($row['wr_3']) && $row['wr_3']) {
        $cl_color = $row['wr_3'];
    } else {
        $cl_color = '#aaa';
    }
    
    if(isset($row['wr_4']) && $row['wr_4']) {
        $tl_color = $row['wr_4'];
    } else {
        $tl_color = '#fff';
    }
    
    $pretty_url = get_pretty_url($bo_table, $row['wr_id'], 'sca=' . $sca);

    $editable = ($is_admin || $member['mb_id'] == $row['mb_id']) ? true : false;

    $list[] = array(
        'id' => $row['wr_id'],
        'textColor' => $tl_color,
        'color' => $cl_color,
        'title' => $row['wr_subject'],
        'start' => $row['wr_1'],
        'end' => $end_dates,
        'url' => htmlspecialchars_decode($pretty_url),
        'editable' => $editable
    );
}

echo json_encode($list);
?>