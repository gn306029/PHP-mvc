<?php
	
	class DefaultController{

		public function __construct(){}

		public function index(){
			require_once("views/default/index.html");
		}

		public function error(){
			require_once("views/default/error.html");
		}

	}
	
?>