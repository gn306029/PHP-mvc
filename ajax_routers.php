<?php
	session_start();
	require_once("includes/php/db.php");
	function call($controller,$action){
		require_once("controllers/".$controller."_controller.php");
		require_once("includes/php/eventMessage.php");
		switch($controller){
			case "login":
				// 顯示 Login 模組相關頁面
				$eventMessage = new eventMessage($_GET,$_POST);
				$controller = new LoingController($eventMessage);
				break;
			case "manager":
				$eventMessage = new eventMessage($_GET,$_POST);
				$controller = new ManagerController($eventMessage);
				break;
		}
		//  執行對應動作
		echo $controller->$action();
	}

	$controllers = array('default' => ['index','error'],
						 'login'  => ['index','login','logout','forget'],
						 'manager' => ['search']);

	$controller = "default";
	$action = "error";
	if(isset($_GET["controller"]) && isset($_GET["action"])){
    	$controller = $_GET["controller"];
    	$action = $_GET["action"];
    }
	// check that the requested controller and action are both allowed
	// if someone tries to access something else he will be redirected to the error action of the pages controller
	if (array_key_exists($controller, $controllers)) {
		if (in_array($action, $controllers[$controller])) {
			call($controller, $action);
		} else {
			call('default', 'error');
		}
	} else {
		call('default', 'error');
	}

?>