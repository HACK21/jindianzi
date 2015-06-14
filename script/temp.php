<?php
class db{
    private $_db = null;
    public function __construct(){
        $dsn = 'mysql:dbname=q2a;host=127.0.0.1';
        $user = 'root';
        $password = 'Qihoo360';
        try {
            $this->_db = new PDO($dsn, $user, $password);
            echo 'yes';
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
$sql = "SELECT 
			postid,t1.userid,handle as cwid,t3.points,categoryid,t1.created,title,content,upvotes,views 
		FROM 
			`qa_posts` as t1 left join `qa_users` as t2 on t1.userid=t2.userid left join `qa_userpoints` as t3 on t1.userid=t3.userid 
		where 
			t1.type='Q'
		";

$db = new DB();
$db->exec("set names utf8");
$res = $db->query($sql);
foreach($res as &$item){
    $sql = "select title,content from qa_userprofile where userid='{$item['userid']}'";
    $user_info = $db->query($sql);
    foreach ($user_info as $value) {
        $item[$value['title']] = $value['content'];
    }
    $item['date'] = date('Y-m-d', strtotime($item['date']));
    $item['category'] = $item['categoryid'] == 2 ? '学术活动' : '日常拜访';
    if($type == 'post_info'){
        echo "{$item['location']}\t{$item['cwid']}\t{$item['name']}\t{$item['date']}\t{$item['points']}\t{$item['category']}\t{$item['created']}\t{$item['title']}\t{$item['upvotes']}\t{$item['views']}\n";
    }else{
        echo $item['title']."\n";
        echo "发布人: {$item['name']}({$item['location']})\t票数：{$item['upvotes']}\n";
        echo "------------------------------------\n";
        echo $item['content'];
        echo "\n------------------------------------\n";
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
            }
            if(!$first){
//                echo "\t\t===============================\t\n";
            }
        }

        echo "\n\n\n";
    }
}

