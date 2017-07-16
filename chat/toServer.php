<?php
/**
 * 发送消息给客服
 * Created by PhpStorm.
 * User: taorong
 * Date: 2017/7/16
 * Time: 19:53
 */

require_once  'database.php';

$content =  htmlspecialchars($_REQUEST['msg']  ,ENT_QUOTES);

if(!empty($content)){
    $sql = 'INSERT INTO chat(`content`,`receiver`,`sender`,`is_new` , `add_time`) VALUE ( \''.$content.'\' ,\'admin\' ,\'user\' ,\''.date('Y-m-d H:i:s').'\')';
    DB::insert($sql);
    echo $content;

}