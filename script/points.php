<?php
require_once(dirname(__FILE__).'/util.php');
require_once(dirname(__FILE__).'/db.php');

$sql = "select t1.points,t2.handle,t3.content from qa_userpoints as t1 left join qa_users as t2 on t1.userid=t2.userid left join qa_userprofile as t3 on t1.userid=t3.userid where t3.title='name' order by points desc";

$db = new DB();
$db->exec("set names utf8");
$res = $db->query($sql);

$html = Util::generateMailTable(array('points','nickname','name'), $res, array('points', 'handle', 'content'));
Util::sendMail('rencangjing2015@126.com', 'aptx4869a@qq.com', 'Daily Report - '.date('Y-m-d H:i:s'), $html, false, true);
//Util::sendMail('aptx4869a@qq.com', '', 'Daily Report - '.date('Y-m-d H:i:s'), $html, false, true);

