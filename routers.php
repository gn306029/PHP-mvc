<?php
	/*
	Post 路由不經過這裡，改使用 Ajax_Routers
	避免將整個 HTML 頁面回傳
	*/
	function call($controller,$action){
		require_once("controllers/".$controller."_controller.php");
		require_once("includes/php/eventMessage.php");
		switch($controller){
			case "default":
				// 顯示預設畫面
				$controller = new DefaultController();
				break;
			case "course":
				$eventMessage = new eventMessage($_GET,$_POST);
				$controller = new CourseController($eventMessage);
				break;
			case "manager":
				$eventMessage = new eventMessage($_GET,$_POST);
				$controller = new ManagerController($eventMessage);
				break;
			case "bus":
				$eventMessage = new eventMessage($_GET,$_POST);
				$controller = new BusController($eventMessage);
				break;
		}
		//  顯示該模組的首頁
		$controller->$action();
	}

	$controllers = array('default' => ['index','error'],
						 'course' => ['index'],
						 'manager' => ['index','show_forget','insert'],
						 'bus' => ['index']);

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