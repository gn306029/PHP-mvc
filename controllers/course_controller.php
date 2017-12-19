<?php
	/*
	課程相關資料模組
	學生:只能查詢
	老師:可以登記成績
	行政人員:可以新增課程
	*/
	require_once("models/course_model.php");
	class CourseController{
		private $get;
		private $post;

		public function __construct($eventMessage){
			$this->get = $eventMessage->Get();
			$this->post = $eventMessage->Post();
		}

		public function index(){
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
			}
			$course_model = new Course_Model();
			$response = $course_model->search($_SESSION["login"],$course_type);
			if(is_null($response)){
				$resMsg = array(
					"Type"=>"SearchError",
					"Msg"=>"搜尋時出現錯誤"
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

?>