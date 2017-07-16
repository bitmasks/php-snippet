<?php
error_reporting(E_ALL);
/**
 * 数据库操作
 * Created by PhpStorm.
 * User: taorong
 * Date: 2017/7/16
 * Time: 19:55
 */
class  DB
{


    public static $mysql_server_name = 'localhost';
    public static $mysql_username = 'root';
    public static $mysql_password = '18133193e0';
    public static $mysql_database = 'chat';
    public static $port = '3306';

    static function conn()
    {
        $conn = mysqli_connect(
            self::$mysql_server_name,
            self::$mysql_username,
            self::$mysql_password,
            self::$mysql_database,
            self::$port
        );
        if (!$conn) {
            die("error connecting");
        }
        return $conn;
    }

    static function select($sql)
    {
        $conn = self::conn();
        $result = mysqli_query($conn, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($rows, $row);
        };
        return $rows;
    }

    static function insert($sql){
        $conn = self::conn();
        return  mysqli_query($conn, $sql);
    }

}

/*$sql = 'SELECT * FROM `chat` WHERE `is_new` = \'1\' LIMIT 0,1000; ';
$data =  DB::select($sql);
print_r($data);*/


