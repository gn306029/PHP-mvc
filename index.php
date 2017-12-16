<?php
	
	session_start();
    if(isset($_GET["controller"]) && isset($_GET["action"])){
    	$controller = $_GET["controller"];
    	$action = $_GET["action"];
    }else if(isset($_POST["controller"]) && isset($_POST["action"])){
    	$controller = $_POST["controller"];
    	$action = $_POST["action"];
    }else{
    	$controller = "default";
    	$action = "index";
    }

    require_once("views/layout.php");

?>