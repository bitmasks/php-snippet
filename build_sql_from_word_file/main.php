<?php
/**
 *
 * 从word文件中生成sql，以批量导入数据库
 *
 * Created by PhpStorm.
 * User: taorong
 * Date: 2017/6/21
 * Time: 22:59
 */




$dir = '/Users/taorong/Documents/work/word/help';
$table = ['content', 'title', 'create_time'];

$get_word_file_content = new  get_word_file_content();
/*echo $get_word_file_content->build_sql($data = [
    [
        'content...'  ,  'title...' , time()
    ]
], $table  , $table_name = 'table_name');*/

echo $sql = $get_word_file_content->main($dir, $table);


class   get_word_file_content
{


    function main($dir = '', $table = [])
    {


        $list = $this->get_file_list($dir);
        $data = $this->get_all_content($list);
        $sql = $this->build_sql($data, $table);
        return $sql;
    }


    //从一个路径读取words文件列表
    function get_file_list($dir = '')
    {
        $list = [];
        $list = scandir($dir);
        foreach ($list as &$v){
            if(empty($v) ||  in_array($v , [ '.' , '..', '.DS_Store' ])){
                unset($v);
            }
            //$v = $dir.$v;
        }
        return $list;
    }

    //从文件列表循环读出文件内容到变量中
    function get_all_content($list = [])
    {
        $data = [];
        foreach ($list as $v) {
            $data[] = $this->get_file_content($v);
        }
        return $data;
    }

    //单个文件内容读取到变量
    function get_file_content($file_path = '')
    {
        if(!file_exists($file_path)){
            return 0;
        }
        //设置流的编码格式，这是文件流(file)，如果是网络访问，file改成http
        $opts = array('file' => array('encoding' => 'gb2312'));
        $ctxt = stream_context_create($opts);
        header("Content-type:   application/msword");
        echo $content = file_get_contents(  $file_path,FILE_TEXT, $ctxt);
        return $content;
    }

    //创建sql
    function build_sql($data = [], $table = [], $table_name = 'table_name')
    {

        if(empty($data) ||  empty($table)){
            return '';
        }

        $sql = 'insert into `' . $table_name . '`  ( ' . implode(',', $table) . ' ) values ';

        foreach ($data as $k => $v) {

            if(empty($v) || !is_array($v) ){
                continue;
            }

            $glue = '';
            if ($k > 0) {
                $glue = ',';
            }

            $sql .= $glue . '(' . implode(',', $v) . ')';
        }

        return $sql;
    }


}