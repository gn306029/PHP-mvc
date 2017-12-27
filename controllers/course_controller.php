<?php
	/*
	課程相關資料模組
	學生:只能查詢
	老師:可以登記成績
	行政人員:可以新增課程
	*/
	require_once("controllers/controller.php");
	require_once("models/course_model.php");
	class CourseController extends Controller{
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

		public function router($action){
			$this->CheckSession();
			$course_model = new Course_Model();
			$response = $course_model->$action($this->get,$this->post);
			return $response;
		}
	}
?>