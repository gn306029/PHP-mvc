<?php
	require_once("models/Information_Model.php");
	class LoingController{

		private $get;
		private $post;

		public function __construct($eventMessage){
			$this->get = $eventMessage->Get();
			$this->post = $eventMessage->Post();
		}

		public function index(){
			require_once("views/login/index.html");
		}

		public function login(){
			$infor_model = new Information_Model();
			if(strlen($this->post["user"]) == 10){
				$type = "student";
			}else if(strlen($this->post["user"]) == 5){
				$type = "teacher";
			}else{
				$resMsg = array(
					"Type"=>"FormError",
					"Msg"=>"帳號格式錯誤"
				);
				return json_encode($resMsg);
			}
			$response = $infor_model->login($this->post["user"],$this->post["pwd"],$type);
			if(is_null($response)){
				$resMsg = array(
					"Type"=>"InforError",
					"Msg"=>"帳號密碼錯誤"
				);
			}else{
				$this->set_session("login",$this->post["user"]);
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>$_SESSION["login"]
				);
			}
			/*
			設定權限 SESSION
			只有老師資料表會與工作資料表做JOIN
			故沒有這個欄位就表示登入者為學生
			*/
			if(isset($response[0]["Job_Category"])){
				if($response[0]["Job_Category"] == "1"){
					// 教師
					$this->set_session("type","1");
				}else if($response[0]["Job_Category"] == "2"){
					// 行政人員
					$this->set_session("type","2");
				}
			}else{
				// 學生
				$this->set_session("type","0");
			}
			return json_encode($resMsg);
		}

		public function logout(){
			session_destroy();
			$resMsg = array(
				"Type"=>"Success",
				"Msg"=>"GoodBye"
			);
			return json_encode($resMsg);
		}

		public function show_forget(){
			require_once("views/login/forget.html");
		}
		/*
		行政人員與老師為何在一張資料表之中
		*/
		public function forget(){
			$infor_model = new Information_Model();
			switch ($this->post["type"]) {
				case "0":
					$type = "student";
					break;
				case "1":
					$type = "teacher";
					break;
				default:
					$type = Null;
					break;
			}
			if(is_null($type)){
				$resMsg = array(
					"Type"=>"TypeError",
					"Msg"=>"無此身份"
				);
				return json_encode($resMsg);
			}else{
				$response = $infor_model->forget($this->post["user"],$this->post["id"],$this->post["pwd"],$type);
				if($response){
					$resMsg = array(
						"Type"=>"Success",
						"Msg"=>"更新成功，已可使用新密碼登入"
					);
				}else{
					$resMsg = array(
						"Type"=>"UpdateError",
						"Msg"=>"更新時發生錯誤，請在嘗試看看"
					);
				}
				return json_encode($resMsg);
			}
			
		}

		public function set_session($name,$value){
			$_SESSION[$name] = $value;
		}

		public function del_session($name){
			unset($_SESSION[$name]);
		}
	}

?>