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

		public function login($get,$post){
			if(strlen($post["user"]) == 10){
				$type = "student";
			}else if(strlen($post["user"]) == 5){
				$type = "teacher";
			}else{
				$resMsg = array(
					"Type"=>"FormError",
					"Msg"=>"帳號格式錯誤"
				);
				return json_encode($resMsg);
			}
			switch ($type) {
				case "student":
					$sql = "SELECT Name From student Where SID = :SID AND Password = :Password";
					break;
				
				case "teacher":
					$sql = "SELECT Name,job_title.Job_Category From teacher Join job_title On teacher.Job_ID = job_title.Job_ID Where Teacher_ID = :SID AND Password = :Password";
					break;
			}
			$parm = array(
				":SID"=>$post["user"],
				":Password"=>md5($post["pwd"])
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				$this->set_session("login",$post["user"]);
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>$_SESSION["login"]
				);
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
			}else{
				$resMsg = array(
					"Type"=>"InforError",
					"Msg"=>"帳號密碼錯誤"
				);
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
		/* 忘記密碼直接重設 ， 因為密碼使用 MD5 加密過了*/
		public function forget($get,$post){
			switch ($post["type"]) {
				case "0":
					$sql = "UPDATE student SET Password = :Password Where SID = :SID AND ID = :ID";
					break;
				case "1":
					$sql = "UPDATE teacher SET Password = :Password Where Teacher_ID = :SID AND ID = :ID";
					break;
			}
			$parm = array(
				":SID"=>$post["user"],
				":ID"=>$post["id"],
				":Password"=>md5($post["pwd"])
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$count = $stmt->rowCount();
			if($count > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>"更新成功，已可使用新密碼登入"
				);
			}else{
				$resMsg = array(
					"Type"=>"UpdateError",
					"Msg"=>"無更新資料"
				);
			}
			return json_encode($resMsg);
		}

		public function search($get,$post){
			switch ($_SESSION["type"]) {
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
				"SID"=>$_SESSION["login"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			// 將職務類型編號轉為中文
			if($_SESSION["type"] == "1" || $_SESSION["type"] == "2"){
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
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"搜尋時出現錯"
				);
			}
			return json_encode($resMsg);
		}

		public function modify($get,$post){
			switch ($_SESSION["type"]) {
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
						":SID"=>$_SESSION["login"]
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
						":SID"=>$_SESSION["login"]
					);
					break;
			}
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$count = $stmt->rowCount();
			if($count > 0){
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

		// 新增資料時須根據新增的類別不同撈出不同的預設資料
		public function show_insert($get,$post){
			switch ($post["type"]) {
				case "student":
					$sql = "SELECT 
							academic.Academic_ID,academic.Name As academic
							FROM academic
							ORDER BY academic.Academic_ID";
					$stmt = $this->conn->prepare($sql);
					$stmt->execute();
					$res1 = $stmt->fetchAll();
					$resMsg = array(
						"Type"=>"Success",
						"Insert_Type"=>"student",
						"Data1"=>$res1
					);
					break;
				case "teacher":
				case "employee":
					$sql = "SELECT 
							job_title.Job_ID,job_title.Title As job_title
							FROM job_title
							ORDER BY job_title.Job_ID";
					$stmt = $this->conn->prepare($sql);
					$stmt->execute();
					$res1 = $stmt->fetchAll();
					$resMsg = array(
						"Type"=>"Success",
						"Insert_Type"=>"teacher",
						"Data1"=>$res1
					);
					break;
			}
			$sql = "SELECT
					faculty.Faculty_ID,faculty.Name As faculty
					From faculty
					ORDER BY faculty.Faculty_ID";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			$res2 = $stmt->fetchAll();
			$resMsg["Faculty"] = $res2;
			return json_encode($resMsg);
		}

		public function insert($get,$post){
			switch ($post["insert_type"]) {
				case "student":
					$sql = "INSERT INTO `student`
							(`SID`, `Name`, `ID`, `Birth`, `Gender`, `Faculty_ID`, `Academic_ID`, `Grade`, `Address`, `Phone`, `Cellphone`, 
							 `Father`, `Father_Phone`, `Mather`, `Mather_Phone`, `Urgent_Man`, `Urgent_Phone`, `Highschool`, `Status`, `Password`) 
							VALUES 
							(:SID,:Name,:ID,:Birth,:Gender,:Faculty_ID,:Academic_ID,:Grade,:Address,:Phone,:Cellphone,
							 :Father,:Father_Phone,:Mather,:Mather_Phone,:Urgent_Man,:Urgent_Phone,:Highschool,:Status,:Password)";
					$parm = array(
						":SID"=>$post["SID"],
						":Name"=>$post["Name"],
						":ID"=>$post["ID"],
						":Gender"=>$post["Gender"],
						":Birth"=>$post["Birth"],
						":Address"=>$post["Address"],
						":Phone"=>$post["Phone"],
						":Cellphone"=>$post["Cellphone"],
						":Urgent_Man"=>$post["Urgent_Man"],
						":Urgent_Phone"=>$post["Urgent_Phone"],
						":Grade"=>$post["Grade"],
						":Father"=>$post["Father"],
						":Father_Phone"=>$post["Father_Phone"],
						":Mather"=>$post["Mather"],
						":Mather_Phone"=>$post["Mather_Phone"],
						":Highschool"=>$post["Highschool"],
						":Status"=>$post["status"],
						":Faculty_ID"=>$post["faculty"],
						":Academic_ID"=>$post["data1"],
						":Password"=>$post["Password"]
					);
					break;
				case "teacher":
					$sql = "INSERT INTO `teacher`
							(`Teacher_ID`, `Name`, `ID`, `Gender`, `Birth`, `Faculty_ID`, `Address`, `Phone`, `Cellphone`, 
							 `Urgent_Man`, `Urgent_Phone`, `Salary`, `Years`, `Job_ID`, `Password`) 
							VALUES 
							(:SID,:Name,:ID,:Gender,:Birth,:Faculty_ID,:Address,:Phone,:Cellphone,
							 :Urgent_Man,:Urgent_Phone,:Salary,0,:Job_ID,md5(:Password))";
					$parm = array(
						":Teacher_ID"=>$post["Teacher_ID"],
						":Name"=>$post["Name"],
						":ID"=>$post["ID"],
						":Gender"=>$post["Gender"],
						":Birth"=>$post["Birth"],
						":Address"=>$post["Address"],
						":Phone"=>$post["Phone"],
						":Cellphone"=>$post["Cellphone"],
						":Urgent_Man"=>$post["Urgent_Man"],
						":Urgent_Phone"=>$post["Urgent_Phone"],
						":Salary"=>$post["Salary"],
						":Faculty_ID"=>$post["faculty"],
						":Job_ID"=>$post["data1"],
						":Password"=>$post["Password"]
					);
					break;
			}
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->rowCount();
			if($response > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>"新增成功"
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"新增失敗"
				);
			}
			return json_encode($resMsg);
		}

		public function set_session($name,$value){
			$_SESSION[$name] = $value;
		}

		public function del_session($name){
			unset($_SESSION[$name]);
		}
	}

?>