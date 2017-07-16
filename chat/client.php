<?php
/**
 * 用户聊天界面
 * Created by PhpStorm.
 * User: taorong
 * Date: 2017/7/16
 * Time: 19:52
 */
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User</title>

    <style>
        iframe{
            width: 0;
            height: 0;
            display: none;
        }
        .block{
            width: 40%;height: 5em;
            margin: auto auto  2em 3em;
        }
        .block textarea{
            width: 100%;height: 100%;
        }
        .block button{
            width: 6em;
            height: 2em;
            margin-top: 1em;
            display: block;
        }
    </style>
</head>
<body>
<iframe src="toServer.php"></iframe>
<div class="block">
    <textarea ></textarea>
</div>

<div class="block">
    <textarea></textarea>
    <button>  发送 </button>
</div>


<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.0.js">
</script>
<script>
    
    $(function () {
        $('button').click(function () {
            var msg = $("textarea:eq(1)").val();
            if(msg =='' ){
                alert('不能为空');
                return;
            }
            $.post( 'toServer.php' ,{ 'msg':$("textarea:eq(1)").val() } ,function (res) {
                $("textarea:eq(0)").append('您说：'+res+'\r\n');
                $("textarea:eq(1)").val('');
            } );

        });

        var setting = {
          'url':'fromServer.php',
          'dataType':'json',
          success:function (res) {
              if(!res){
                  return ;
              }
              var obj = eval(res);
              $("textarea:eq(0)").append('客服说：'+obj.content+'\r\n');
           }
        }

        $.ajax(setting);

    })
    

    
    
</script>

</body>
</html>