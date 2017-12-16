<!DOCTYPE html>
<html>
<head>
	<title>校務系統</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./includes/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="includes/js/main.js"></script>
    <script type="text/javascript" src="includes/js/ajax_class"></script>
    <script type="text/javascript" src="includes/js/ajax_login.js"></script>
    <script type="text/javascript" src="includes/js/ajax_logout.js"></script>

</head>
<body>
	<nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/MVC/?controller=default&action=index">KUAS</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                	<?php
                		if(isset($_SESSION["login"])){
                			echo "<li id=\"li_logout\"><a href=\"#\">登出</a></li>";
                		}else{
                			echo "<li id=\"li_login\"><a href=\"#\">登入</a></li>";
                		}
                	?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="content">
        <!--nav bar 的欄位使用 JS 做處理-->
        <?php require_once("routers.php"); ?>
    </div>
    <div>
    </div>
    <nav class="navbar navbar-default navbar-fixed-bottom">
        <div class="container">
        </div>
    </nav>
	
</body>
</html>