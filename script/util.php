<?php

class Util{
    /**
     * 发送邮件
     * @param $to 收件人
     * @param $cc 抄送人
     * @param $title 标题
     * @param $content 内容
     * @param bool $from 发件人：（默认）后台系统
     */
    public static function sendMail($to, $cc, $title, $content, $from=false, $html=false){
        $subject = "=?UTF-8?B?".base64_encode($title)."?=";
        $message = $content;
        if(!$from){
            $from = "=?UTF-8?B?".base64_encode("数据后台")."?= <no-reply@jindianzi.today>";
        }
        $headers = array();
        if($html){
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        }
        $headers[] = "From: $from";
        $headers[] = "Cc: $cc";
        return mail($to,$subject,$message,implode("\r\n", $headers));
    }

    static public function generateMailTable($head = array(), $data = array(), $column=array(), $width=821)
    {
        $style = '<style> *{ padding:0; margin:0; } </style> ';
        $table = '<table border="1" style="border-spacing: 0;border-collapse: collapse;width:'.$width.'px;text-align:center">';
        $table_body = $table_head = '';
        if (is_array($head) && count($head) > 0) {
            $table_head .= '<tr><th>'.implode('</th><th>', $head).'</th></tr>';
        }
        foreach ($data as $item) {
            if(count($column) > 0 && count($column) >= count($head)){
                $row_data = array();
                foreach($column as $key){
                    $row_data[] = $item[$key];
                }
            }else{
                $row_data = $item;
            }
            $table_body .= '<tr><td>'.implode('</td><td>', $row_data).'</td></tr>'."\n";
        }
        $html = $style.$table.$table_head.$table_body.'</table>';
        return $html;
    }
}
