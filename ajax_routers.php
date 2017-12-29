<?php
	session_start();
	function call($controller,$action){
		require_once("controllers/".$controller."_controller.php");
		require_once("includes/php/eventMessage.php");
		switch($controller){
			case "manager":
				$eventMessage = new eventMessage($_GET,$_POST);
				$controller = new ManagerController($eventMessage);
				break;
			case "course":
				$eventMessage = new eventMessage($_GET,$_POST);
				$controller = new CourseController($eventMessage);
				break;
		}
		//  執行對應動作
		echo $controller->router($action);
	}

	$controllers = array('manager' => ['search','modify','login','logout','forget','show_insert','insert'],
						 'course' => ['search','grade_search','history_course','information','now','show_evol_page','send_evol','get_course_list','show_course_student','save_score','show_course_outline','save_outline','show_insert','show_modify','show_modify_detail','insert','modify']);

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