<?php
	/*
	課程相關資料模組
	學生:只能查詢
	老師:可以登記成績
	行政人員:可以新增課程
	*/
	require_once("controllers/controller.php");
	require_once("models/course_model.php");
	class CourseController implements Controller{
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

		public function index(){
			$this->CheckSession();
			switch ($_SESSION["type"]) {
				case '0':
					//學生
					require_once("views/course/student/index.html");
					break;
				case '1':
					//教師
					require_once("views/course/teacher/index.html");
					break;
				case '2':
					//行政人員
					require_once("views/course/employee/index.html");
					break;
			}
		}
		
		public function search(){
			$this->CheckSession();
			switch ($this->post["course_type"]) {
				case "Pro":
					$course_type = "1";
					break;
				case "Gen":
					$course_type = "2";
					break;
				case "Phy":
					$course_type = "3";
					break;
				case "Mili":
					$course_type = "4";
					break;
			}
			$course_model = new Course_Model();
			$response = $course_model->search($_SESSION["login"],$course_type);
			if(is_null($response)){
				$resMsg = array(
					"Type"=>"SearchNotFound",
					"Msg"=>"查無資料"
				);
			}else{
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>$response
				);
			}
			return json_encode($resMsg);
		}

		public function history_search(){
			$this->CheckSession();
			switch ($this->post["search_type"]) {
				case "Grade":
					// 查詢歷年成績
					$course_model = new Course_Model();
					$response = $course_model->Grade_Search($_SESSION["login"]);

					# code...
					break;
				case "Curr":
					// 查詢歷年課程
					$course_model = new Course_Model();
					$response = $course_model->History_Course($_SESSION["login"]);
					break;
			}
			if(is_null($response)){
				$resMsg = array(
					"Type"=>"SearchNotFound",
					"SearchType"=>($this->post["search_type"]=="Grade"?"Grade":"Curr"),
					"Msg"=>"查無資料"
				);
			}else{
				$resMsg = array(
					"Type"=>"Success",
					"SearchType"=>($this->post["search_type"]=="Grade"?"Grade":"Curr"),
					"Msg"=>$response
				);
			}
			return json_encode($resMsg);
		}
		// 查詢課程相關資料
		public function information(){
			$this->CheckSession();
			if(isset($this->post["id"])){
				$course_model = new Course_Model();
				// 先取得課程詳細資料
				$response = $course_model->information($this->post["id"]);
				// 再取得課程與老師的評論
				if(is_null($response)){
					$resMsg = array(
						"Type"=>"Error",
						"Msg"=>"查無該課程資料"
					);
				}else{
					$resMsg = array(
						"Type"=>"Success",
						"Msg"=>$response
					);
				}
				return json_encode($resMsg);
			}
		}
		// 顯示本學期所有課程
		public function now(){
			$this->CheckSession();
			$course_model = new Course_Model();
			$response = $course_model->now($_SESSION["login"]);
			if(is_null($response)){
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"無資料"
				);
			}else{
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>json_encode($response)
				);
			}
			return json_encode($resMsg);
		}

		public function show_evol(){
			$this->CheckSession();
			$course_model = new Course_Model();
			$response = $course_model->now($_SESSION["login"]);
			$count = count($response);
			foreach ($response as $key => $value) {
				if($response[$key][0] == $this->post["id"]){
					$resMsg = array(
						"Type"=>"Success_Show",
						"Msg"=>json_encode($response[$key])
					);
					return json_encode($resMsg);
				}
				$resMsg = array(
					"Type"=>"IDError",
					"Msg"=>"無此課程編號或是該學期無此課程"
				);
				return json_encode($resMsg);
			}
		}
	}
?>