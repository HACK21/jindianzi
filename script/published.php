<?php
$today = date('m');
$next = date('m', strtotime('+1 week'));
if($today == $next){
    echo 'Stop Running '.date('Y-m-d')."\n";
    exit('Stop Running '.date('Y-m-d'));
}


require_once(dirname(__FILE__).'/util.php');
require_once(dirname(__FILE__).'/db.php');

$start_date = isset($argv[1]) ? $argv[1].' 00:00:00' : date('Y-m-d 00:00:00', strtotime('-1 week'));
$end_date = isset($argv[2]) ? $argv[2].' 23:59:59' : date('Y-m-d 00:00:00');

var_dump($start_date);
var_dump($end_date);

$sql = "select count(1) published,t2.handle nickname,t3.content name,t4.content location from qa_posts as t1
        left join qa_users as t2 on t1.userid=t2.userid
        left join qa_userprofile as t3 on t1.userid=t3.userid
         left join qa_userprofile as t4 on t1.userid=t4.userid
        where t3.title='name' and t4.title='location' and t1.type='Q' and t1.created >= '$start_date' and t1.created<='$end_date'
        group by t2.handle,t3.content,location
        order by location, t2.handle, t3.content";

$db = new DB();
$db->exec("set names utf8");
$res = $db->query($sql);

$html = Util::generateMailTable(array('地区','昵称','名称', '发表'), $res, array('location', 'nickname', 'name', 'published'));
Util::sendMail('rencangjing2015@126.com', 'aptx4869a@qq.com', "[金点子] 周报 $start_date - $end_date", $html, false, true);
//Util::sendMail('aptx4869a@qq.com', '', 'Daily Report - '.date('Y-m-d H:i:s'), $html, false, true);
