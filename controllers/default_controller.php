<?php
	
	require_once("controllers/controller.php");
	class DefaultController extends Controller{

		public function __construct(){}
		public function CheckSession(){}
		public function router($action){}
		public function index(){
			if(isset($_SESSION["type"])){
				switch($_SESSION["type"]){
					case "0":
						require_once("views/manager/student/index.html");
						break;
					case "1":
						require_once("views/manager/teacher/index.html");
						break;
					case "2":
						require_once("views/manager/employee/index.html");
						break;
				}
			}else{
				require_once("views/default/index.html");
			}
		}

		public function error(){
			require_once("views/default/error.html");
		}

	}
	
?>