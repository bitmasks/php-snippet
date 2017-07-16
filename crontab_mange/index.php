<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Linux Crontab Web （网页版计划任务）</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>

<body>
<div class="container">
    <div class="masthead">
        <ul class="nav nav-pills pull-right">
            <li class="nav-home active"><a href="#" onclick="changePage('home');">首页</a></li>
            <li class="nav-new"><a href="#new" onclick="changePage('new');">新建计划任务</a></li>
            <li class="nav-active"><a href="#active" onclick="changePage('active');">任务列表</a></li>
        </ul>
        <h1><a onclick="changePage('home');">Linux Crontab Web</a>
            <small>（网页版计划任务）</small>
        </h1>
    </div>
    <hr>
    <div id="home">
        <div class="jumbotron">
            <h1>随时随地管理您的计划任务！</h1>

            <br>

            <p class="lead">简单设置，网页管理您的计划任务。</p>

            <p>这是很简单的， 您不再需要了解复杂的计划任务语法，让您很轻松地在几秒钟之内设置您的计划任务！</p>
        </div>
    </div>

    <div id="new">
        <h3>新建计划任务</h3>

        <form>
            <label>分钟</label>
            <input class="add-minute input-xlarge" type="text" placeholder="0-59 or *">

            <label>小时</label>
            <input class="add-hour input-xlarge" type="text" placeholder="0-23 or *">

            <label>星期</label>
            <input class="add-dayweek input-xlarge" type="text" placeholder="0-6 or *">

            <label>日期</label>
            <input class="add-daymonth input-xlarge" type="text" placeholder="0-31 or *">

            <label>月份</label>
            <input class="add-month input-xlarge" type="text" placeholder="0-12 or *">

            <label>需要执行的命令</label>
            <textarea class="add-command input-block-level" rows="3"
                      placeholder="输入一个当前用户权限在系统上有效的CLI命令，或引用外部可执行脚本。"></textarea>

            <p>
                <a id="save-btn" class="btn btn-large btn-success">保存新的计划任务</a>
            </p>

            <hr>
            <div class="alert alert-info">
                <strong>帮助：</strong>
                <br>
                在一个区域里填写多个数值的方法：
                <ul>
                    <li>逗号 (',') 分开的值，例如：“1,3,4,7,8”</li>
                    <li>连词符 ('-') 制定值的范围，例如：“1-6”，意思等同于“1,2,3,4,5,6”</li>
                    <li>星号 ('*') 代表任何可能的值。例如，在“小时域” 里的星号等于是“每一个小时”。</li>
                    <li>命令举例：curl http://127.0.0.1/crontab.php</li>
                </ul>
            </div>


        </form>
    </div>

    <div id="active">
        <h3>计划任务列表</h3>

        <p><a class="btn btn-danger delete-all-btn">删除全部任务</a></p>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>分钟</th>
                <th>小时</th>
                <th>星期</th>
                <th>日期</th>
                <th>月份</th>
                <th>命令</th>
                <th>文件</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody class="active-job-list">
            </tbody>
        </table>
    </div>

    <hr>

</div>

<script src="js/jquery-1.9.0.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/main.js"></script>
</body>
</html>
