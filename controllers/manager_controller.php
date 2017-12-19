<?php
	/*
	資料管理模組
	進來就會顯示個人相關資料
	*/
	require_once("models/Information_Model.php");
	class ManagerController{

		private $get;
		private $post;

		public function __construct($eventMessage){
			$this->get = $eventMessage->Get();
			$this->post = $eventMessage->Post();
		}

		public function search(){
			if(!isset($_SESSION["type"]) && !isset($_SESSION["login"])){
				require_once("views/default/index.html");
				return;
			}
			$infor_model = new Information_Model();
			$response = $infor_model->information($_SESSION["login"],$_SESSION["type"]);
			if(is_null($response)){
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"搜尋時出現錯"
				);
			}else{
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>$response
				);
			}
			return json_encode($resMsg);
		}

		public function modify(){
			if(!isset($_SESSION["type"]) && !isset($_SESSION["login"])){
				require_once("views/default/index.html");
				return;
			}
			$infor_model = new Information_Model();
			$response = $infor_model->modify($_SESSION["login"],$_SESSION["type"],$this->post);
			if($response){
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>"更新成功"
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"沒有更新任何資料"
				);
			}
			return json_encode($resMsg);
		}
	}
?>