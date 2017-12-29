<?php
	// 校車模組
	require_once("controllers/controller.php");

	class BusController extends Controller{

		private $get;
		private $post;

		public function __construct($eventMessage){
			$this->get = $eventMessage->Get();
			$this->post = $eventMessage->Post();
		}

		public function CheckSession(){
			if(!isset($_SESSION["type"]) && !isset($_SESSION["login"])){
				require_once("views/default/index.html");
				return;
			}
		}

		public function router($action){}

		public function index(){
			$this->CheckSession();
			switch ($_SESSION["type"]) {
				case "0":
				case "1":
					require_once("views/bus/others/index.html");
					break;
				case "2":
					require_once("views/bus/employee/index.html");
					break;
			}
		}

	}

?>