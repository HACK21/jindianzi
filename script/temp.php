<?php
$today = date('m');
$next = date('m', strtotime('+1 week'));
if($today == $next){
    echo 'Stop Running '.date('Y-m-d')."\n";
    exit('Stop Running '.date('Y-m-d'));
}



require_once(dirname(__FILE__).'/util.php');
class db{
    private $_db = null;
    public function __construct(){
        $dsn = 'mysql:dbname=q2a;host=127.0.0.1';
        $user = 'root';
        $password = '()!@#!**';
        try {
            $this->_db = new PDO($dsn, $user, $password);
            echo "yes\n";
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function exec($sql){
        $this->_db->exec($sql);
    }

    public function query($sql){
        $res = $this->_db->query($sql, PDO::FETCH_ASSOC);
        $data = array();
        foreach ($res as $value) {
            $data[] = $value;
        }
        return $data;
    }
}

$type = !isset($argv[1]) ? 'post_info' : 'content';
$start_date = isset($argv[2]) ? $argv[2].' 00:00:00' : date('Y-m-d 00:00:00', strtotime('-1 month'));
$end_date = isset($argv[3]) ? $argv[3].' 23:59:59' : date('Y-m-d 00:00:00');
var_dump($start_date);
var_dump($end_date);
$sql = "SELECT
			postid,t1.userid,handle as cwid,t3.points,categoryid,t1.created,title,content,upvotes,views 
		FROM 
			`qa_posts` as t1 left join `qa_users` as t2 on t1.userid=t2.userid left join `qa_userpoints` as t3 on t1.userid=t3.userid 
		where 
			t1.type='Q' and t1.created >= '$start_date' and t1.created <= '$end_date'
		";

$db = new DB();
$db->exec("set names utf8");
$res = $db->query($sql);
$mail_content = '';
foreach($res as &$item){
    $sql = "select title,content from qa_userprofile where userid='{$item['userid']}'";
    $user_info = $db->query($sql);
    foreach ($user_info as $value) {
        $item[$value['title']] = $value['content'];
    }
    $item['date'] = date('Y-m-d', strtotime($item['created']));
    $item['category'] = $item['categoryid'] == 2 ? '学术活动' : '日常拜访';
    if($type == 'post_info'){
        echo "{$item['location']}\t{$item['cwid']}\t{$item['name']}\t{$item['date']}\t{$item['points']}\t{$item['category']}\t{$item['created']}\t{$item['title']}\t{$item['upvotes']}\t{$item['views']}\n";
    }else{
        echo "标题：".$item['title']."\n";
        echo "发布人: {$item['name']}({$item['location']})\t票数：{$item['upvotes']}\n";
        echo "------------------------------------\n";
        echo $item['content'];
        echo "\n------------------------------------\n";
        echo "链接地址：http://jindianzi.today/index.php?qa={$item['postid']}\n";
        
        $mail_content .= '<p>'."标题：".$item['title']."</p>";
        $mail_content .= '<p>'."发布人: {$item['name']}({$item['location']})\t票数：{$item['upvotes']}"."</p>";
        $mail_content .= '<p>'."------------------------------------"."</p>";
        $mail_content .= '<p>'.$item['content']."</p>";
        $mail_content .= '<p>'."------------------------------------"."</p>";
        $mail_content .= '<p>'."链接地址：http://jindianzi.today/index.php?qa={$item['postid']}"."</p>";
        $sql = "select
                    t1.userid, t1.postid, t1.content
                from
                    `qa_posts` as t1 left join `qa_users` as t2 on t1.userid=t2.userid
                where
                    t1.parentid='{$item['postid']}'
                order by t1.created desc
        ";
        $replies = $db->query($sql);
        $i=1;
        foreach ($replies as $reply) {
//            echo "\t".$i++."楼：".str_replace(" ", '', str_replace("\n", '', $reply['content']))."\n";
            echo "\t".$i++."楼：".str_replace("\n", "\n\t   ", $reply['content'])."\n";
            $mail_content .= "<p>"."\t".$i++."楼：".str_replace("\n", "\n\t   ", $reply['content'])."</p>";
            $sql = "select
                    t1.userid, t1.postid, t1.content
                from
                    `qa_posts` as t1 left join `qa_users` as t2 on t1.userid=t2.userid
                where
                    t1.parentid='{$reply['postid']}'
                order by t1.created desc
            ";
            $comments = $db->query($sql);
            $first=true;
            foreach ($comments as $comment) {
                if($first){
//                    echo "\t\t===============================\t\n";
                    $first = false;
                }
                echo "\t\t>> 回复：{$comment['content']}\n";
                $mail_content .= "<p>"."\t\t>> 回复：{$comment['content']}</br>"."</p>";
            }
            if(!$first){
//                echo "\t\t===============================\t\n";
            }
        }
    }
    $mail_content .= "</br></br>";
}

if($mail_content){
    echo "月报发射.......!";
//    Util::sendMail('', 'aptx4869a@qq.com', "月报 $start_date -- $end_date", $mail_content, false, true);
    Util::sendMail('rencangjing2015@126.com', 'aptx4869a@qq.com', "[金点子] 详情月报 $start_date -- $end_date", $mail_content, false, true);
}
