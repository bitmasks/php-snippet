<?php
/**
 * 微信验证伪装已上传验证文件
 *
 ***  .htaccess 文件内容begin   ***
 
<IfModule mod_rewrite.c>
  Options +FollowSymlinks
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>

 *** .htaccess 文件内容end  ***/

$data =  explode('/',$_SERVER['QUERY_STRING']);
header('Content-Type: text/plain'); //纯文本格式
echo  str_replace( ['.txt','MP_verify_']  ,['',''] ,   $data[count($data)-1] );
?>
