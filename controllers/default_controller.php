<?php
	
	class DefaultController{

		public function __construct(){}

		public function index(){
			if(isset($_SESSION["type"])){
				switch($_SESSION["type"]){
					case "0":
						require_once("views/manager/index.html");
						break;
					case "1":
						require_once("views/manager/index.html");
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