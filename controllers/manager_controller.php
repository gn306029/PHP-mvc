<?php
	/*
	資料管理模組
	進來就會顯示個人相關資料
	*/
	require_once("models/Information_Model.php");
	require_once("controllers/controller.php");
	class ManagerController extends Controller{

		private $get;
		private $post;

		public function __construct($eventMessage){
			$this->get = $eventMessage->Get();
			$this->post = $eventMessage->Post();
		}

		public function index(){
			require_once("views/login/index.html");
		}

		public function CheckSession(){
			if(!isset($_SESSION["type"]) && !isset($_SESSION["login"])){
				require_once("views/default/index.html");
				return;
			}
		}

		public function router($action){
			if($action != "login" && $action != "logout" && $action != "forget"){
				$this->CheckSession();
			}
			$infor_model = new Information_Model();
			$response = $infor_model->$action($this->get,$this->post);
			return $response;
		}

		public function show_forget(){
			require_once("views/login/forget.html");
		}
	}
?>