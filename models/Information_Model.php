<?php
	/*
	基本資料查詢
	1.帳號登入判斷
	2.學生、老師、行政人員基本資料查詢修改
	*/
	require_once("models/Model.php");
	class Information_Model extends Model{

		public function __construct(){
			parent::__construct();
		}

		public function login($user,$pwd,$type){
			switch ($type) {
				case "student":
					$sql = "SELECT Name From student Where SID = :SID AND Password = :Password";
					break;
				
				case "teacher":
					$sql = "SELECT Name,job_title.Job_Category From teacher Join job_title On teacher.Job_ID = job_title.Job_ID Where Teacher_ID = :SID AND Password = :Password";
					break;
			}
			$parm = array(
				":SID"=>$user,
				":Password"=>md5($pwd)
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				return $response;
			}else{
				return;
			}
		}
		/* 忘記密碼直接重設 ， 因為密碼使用 MD5 加密過了*/
		public function forget($user,$id,$pwd,$type){
			switch ($type) {
				case "student":
					$sql = "UPDATE student SET Password = :Password Where SID = :SID AND ID = :ID";
					break;
				case "teacher":
					$sql = "UPDATE teacher SET Password = :Password Where Teacher_ID = :SID AND ID = :ID";
					break;
			}
			$parm = array(
				":SID"=>$user,
				":ID"=>$id,
				":Password"=>md5($pwd)
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$count = $stmt->rowCount();
			if($count > 0){
				return true;
			}else{
				return false;
			}
		}

		public function information($user,$type){
			switch ($type) {
				case "0":
					$sql = "SELECT 
							SID,student.Name,ID,Birth,Gender,faculty.Name As Faculty,academic.Name As Academic,
							Grade,Address,Phone,Cellphone,Father,Father_Phone,Mather,Mather_Phone,
							Urgent_Man,Urgent_Phone,Highschool,Status 
							From student JOIN faculty On student.Faculty_ID = faculty.Faculty_ID
							JOIN academic On student.Academic_ID = academic.Academic_ID
							Where SID = :SID";
					break;
				case "1":
				case "2":
					$sql = "SELECT 
							Teacher_ID,teacher.Name,ID,Birth,Gender,faculty.Name As Faculty,
							Address,Phone,Cellphone,
							Urgent_Man,Urgent_Phone,Salary,Years,job_title.Title,job_title.Job_Category
							From teacher JOIN faculty On teacher.Faculty_ID = faculty.Faculty_ID
							JOIN job_title On teacher.Job_ID = job_title.Job_ID
							Where Teacher_ID = :SID";
					break;
			}
			$parm = array(
				"SID"=>$user
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			// 將職務類型編號轉為中文
			if($type == "1" || $type == "2"){
				if($response[0]["Job_Category"] == "1"){
					$response[0]["Job_Category"] = "教師";
				}else if($response[0]["Job_Category"] == "2"){
					$response[0]["Job_Category"] = "行政人員";
				}
			}
			// 將 Null 轉為空值
			foreach ($response[0] as $key => $index) {
				if(is_null($response[0][$key])){
					$response[0][$key] = "";
				}
			}
			// 將性別轉為中文
			if($response[0]["Gender"] == "0"){
				$response[0]["Gender"] = "女";
			}else{
				$response[0]["Gender"] = "男";
			}
			if(count($response)>0){
				return json_encode($response);
			}else{
				return;
			}
		}

		public function modify($user,$type,$post){
			switch ($type) {
				case "0":
					$sql = "UPDATE student
							SET 
							Address = :Address,
							Phone = :Phone,
							Cellphone = :Cellphone,
							Father = :Father,
							Father_Phone = :Father_Phone,
							Mather = :Mather,
							Mather_Phone = :Mather_Phone,
							Urgent_Man = :Urgent_Man,
							Urgent_Phone = :Urgent_Phone
							Where
							SID = :SID";
					$parm = array(
						":Address"=>$post["address"],
						":Phone"=>$post["phone"],
						":Cellphone"=>$post["cellphone"],
						":Father"=>$post["father"],
						":Father_Phone"=>$post["father_phone"],
						":Mather"=>$post["mather"],
						":Mather_Phone"=>$post["mather_phone"],
						":Urgent_Man"=>$post["urgent_man"],
						":Urgent_Phone"=>$post["urgent_phone"],
						":SID"=>$user
					);
					break;
				case "1":
				case "2":
					$sql = "UPDATE teacher
							SET 
							Address = :Address,
							Phone = :Phone,
							Cellphone = :Cellphone,
							Urgent_Man = :Urgent_Man,
							Urgent_Phone = :Urgent_Phone
							Where
							Teacher_ID = :SID";
					$parm = array(
						":Address"=>$post["address"],
						":Phone"=>$post["phone"],
						":Cellphone"=>$post["cellphone"],
						":Urgent_Man"=>$post["urgent_man"],
						":Urgent_Phone"=>$post["urgent_phone"],
						":SID"=>$user
					);
					break;
			}
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$count = $stmt->rowCount();
			if($count > 0){
				return true;
			}else{
				return false;
			}
		}

	}

?>