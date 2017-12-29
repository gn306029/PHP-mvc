<?php
	require_once("models/Model.php");
	class Course_Model extends Model{
		public function __construct(){
			parent::__construct();
		}
		// 用於查詢各類型之課程，目前為學生用
		public function search($get,$post){
			switch ($post["course_type"]) {
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
			$sql = "SELECT
					course.Course_ID,course.Name As Cs_Name,CONCAT(School_Year,Semester) As Year_Sem,
					teacher.Name As T_Name,classroom.Name As R_Name,
					category.Name As C_Name,remarks.Name As RE_Name,
					Outline,Course_Credit,Day,Time
					From course 
					JOIN teacher
					On course.Teacher_ID = teacher.Teacher_ID
					Join classroom
					On course.Classroom_ID = classroom.Classroom_ID
					Join category
					On course.Category_ID = category.Category_ID
					Join remarks
					On course.Remarks_ID = remarks.Remarks_ID
					Where category.Category_ID = :Category_ID
                    GROUP BY course.Course_ID,course.Name,CONCAT(School_Year,Semester),teacher.Name,
                    classroom.Name,category.Name,remarks.Name,Outline,Course_Credit,Day,Time";
			$parm = array(
				":Category_ID"=>$course_type,
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			// 將 Null 轉為空值
			for($i = 0;$i<$stmt->rowCount();$i++){
				foreach ($response[$i] as $key => $value) {
					if(is_null($response[$i][$key])){
						$response[$i][$key] = "";
					}
				}
			}
			if(count($response)>0){
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"SearchNotFound",
					"Msg"=>"查無資料"
				);
			}
			return json_encode($resMsg);
		}

		public function grade_search($get,$post){
			$sql = "SELECT
					sort_rank.SID,sort_rank.Name,sort_rank.grade,sort_rank.rank,sort_rank.year
					From
					(
						SELECT
					    grade.SID As SID,
					    grade.Name As Name,
					    grade.score As grade,
                        grade.School_Year As year,
					    @pre := @curr,
					    @curr := grade.score ,
					    @rank := IF(@prev = @curr,@rank,@rank + 1) As rank
					    From
					    (SELECT
							student.Name,
							student.SID,
							AVG(shc.Score) As score,
                         	course.School_Year,
							@pre := null,
							@curr := null,
							@rank := 0
							From 
							student
							JOIN student_history_course As shc
					        On student.SID = shc.SID
					        JOIN faculty
					        On student.Faculty_ID = faculty.Faculty_ID
					        JOIN academic
					        On student.Academic_ID = academic.Academic_ID
                         	JOIN course
                         	On shc.Course_ID = course.Course_ID
							Where
					        student.Faculty_ID = (
					            SELECT
					            Faculty_ID
					            From
					            student
					            WHERE
					            SID = :SID
					        )
					        AND
					        CONCAT(academic.Name,student.Grade) = (
					            SELECT
					            CONCAT(academic.Name,student.Grade)
					            From
					            student
					            JOIN academic
					            On student.Academic_ID = academic.Academic_ID
					            WHERE
					            SID = :SID
					        )
					        GROUP BY shc.SID,course.School_Year
					     	ORDER BY score DESC
					    )grade
					)sort_rank
					WHERE 
					sort_rank.SID = :SID
					AND
					sort_rank.grade IS NOT null
                    ORDER BY sort_rank.year DESC";
			$parm = array(
				":SID"=>$_SESSION["login"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response)>0){
				$resMsg = array(
					"Type"=>"Success",
					"SearchType"=>($post["search_type"]=="Grade"?"Grade":"Curr"),
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"SearchNotFound",
					"SearchType"=>($post["search_type"]=="Grade"?"Grade":"Curr"),
					"Msg"=>"查無資料"
				);
			}
			return json_encode($resMsg);
		}
		//取得歷史修課紀錄
		public function history_course($get,$post){

			$sql = "SELECT
					course.Course_ID,course.Name,CONCAT(course.School_Year,course.Semester) As 'year',
					teacher.Name As 'teacher',classroom.Name As 'room',
					category.Name As 'category',remarks.Name As 'remarks',
					course.Outline,course.Course_Credit,CONCAT(course.Day,course.Time) As 'time',
					student_history_course.Score
					FROM
					student_history_course
					JOIN course
					On student_history_course.Course_ID = course.Course_ID
					JOIN teacher
					On course.Teacher_ID = teacher.Teacher_ID
					JOIN classroom
					On course.Classroom_ID = classroom.Classroom_ID
					JOIN category
					On course.Category_ID = category.Category_ID
					JOIN remarks
					ON course.Remarks_ID = remarks.Remarks_ID
					WHERE
					student_history_course.SID = :SID
					ORDER BY course.School_Year DESC,course.Semester DESC";
			$parm = array(
				":SID"=>$_SESSION["login"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			foreach ($response as $key_name1 => $value) {
				foreach ($value as $key_name2 => $eval) {
					if(is_null($eval)){
						$response[$key_name1][$key_name2] = "";
					}
				}
			}
			if(count($response)>0){
				$resMsg = array(
					"Type"=>"Success",
					"SearchType"=>($post["search_type"]=="Grade"?"Grade":"Curr"),
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"SearchNotFound",
					"SearchType"=>($post["search_type"]=="Grade"?"Grade":"Curr"),
					"Msg"=>"查無資料"
				);
			}
			return json_encode($resMsg);
		}
		//取得課程資訊
		public function information($get,$post){
			if(!isset($post["id"])){
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"無課程編號"
				);
				return json_encode($resMsg);
			}
			$sql = "SELECT
					course.Name As Name,CONCAT(course.School_Year,course.Semester) As year,teacher.Name As teacher,classroom.Name As classroom,
					category.Name As category,course.Outline,course.Course_Credit,CONCAT(course.Day,course.TIme) As Opentime,remarks.Name As remarks
					FROM course
					JOIN teacher
					ON teacher.Teacher_ID = course.Teacher_ID
					JOIN classroom
					ON course.Classroom_ID = classroom.Classroom_ID
					JOIN category
					ON course.Category_ID = category.Category_ID
					JOIN remarks
					ON course.Remarks_ID = remarks.Remarks_ID
					WHERE
					course.Course_ID = :course_id";
			$parm = array(
				":course_id"=>$post["id"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			$resdata = array();
			if(count($response)>0){
				$resdata["information"] = $response;
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"查無該課程資料"
				);
				return json_encode($resMsg);
			}
			$sql = "SELECT
					course_evaluation.Course_Evaluation As CE,course_evaluation.Teacher_Evaluation As TE
					From
					course_evaluation
					WHERE
					course_evaluation.Course_ID = :course_id";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$evaluation = $stmt->fetchAll();
			// 去除 Null
			foreach ($evaluation as $key_name1 => $value) {
				foreach ($value as $key_name2 => $eval) {
					if(is_null($eval)){
						$evaluation[$key_name1][$key_name2] = "";
					}
				}
			}
			if(count($evaluation) > 0){
				$resdata["evaluation"] = $evaluation;
			}else{
				$resdata["evaluation"] = "暫無評論";
			}
			$resMsg = array(
				"Type"=>"Success",
				"Msg"=>$resdata
			);
			return json_encode($resMsg);
		}
		// 取當當前學期修課清單
		public function now($get,$post){
			$content = file_get_contents("./config.json");
			$content = json_decode($content,true);
			$sql = "SELECT
					course.Course_ID As Course_ID,
					course.Name As Course
					FROM course
					JOIN student_history_course As shc
					ON course.Course_ID = shc.Course_ID
					WHERE CONCAT(course.School_Year,course.Semester) = :year
					AND shc.SID = :SID
					ORDER BY course.Course_ID DESC";
			$parm = array(
				":SID"=>$_SESSION["login"],
				":year"=>$content["nowyear"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>json_encode($response)
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"無資料"
				);
			}
			return json_encode($resMsg);
		}

		public function show_evol_page($get,$post){
			$content = file_get_contents("./config.json");
			$content = json_decode($content,true);
			$sql = "SELECT
					course.Course_ID As Course_ID,
					course.Name As course
					FROM course
					JOIN student_history_course As shc
					ON course.Course_ID = shc.Course_ID
					WHERE CONCAT(course.School_Year,course.Semester) = :year
					AND course.Course_ID = :course_id
					AND shc.SID = :sid";
			$parm = array(
				":sid"=>$_SESSION["login"],
				":year"=>$content["nowyear"],
				":course_id"=>$post["course_id"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				$resMsg = array(
					"Type"=>"Success_Show",
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"IDError",
					"Msg"=>"該年度無此課程"
				);
			}
			return json_encode($resMsg);
		}

		public function send_evol($get,$post){
			if(!isset($post["course"])){
				$resMsg = array(
					"Type"=>"DataError",
					"Msg"=>"課程資料為必要"
				);
				return json_encode($resMsg);
			}
			$sql = "INSERT INTO `course_evaluation`(`Course_ID`, `SID`, `Course_Evaluation`, `Teacher_Evaluation`) VALUES (:course_id,:sid,:course_evol,:teacher_evol)";
			$parm = array(
				":course_id"=>$post["course_id"],
				":sid"=>$_SESSION["login"],
				":course_evol"=>$post["course"],
				":teacher_evol"=>$post["teacher"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->rowCount();
			if($response > 0){
				$resMsg = array(
					"Type"=>"Success_Send",
					"Msg"=>"填寫成功"
				);
			}else{
				$resMsg = array(
					"Type"=>"InsertError",
					"Msg"=>"新增失敗"
				);
			}
			return json_encode($resMsg);
		}

		public function get_graduation_threshold($sid){
			$sql = "SELECT 
					faculty.Name , Pro_Compulsory , General , 
					Physical , School , Sweep , College_Compulsory , Faculty_Option
					From graduation_threshold
					JOIN faculty
					ON faculty.Faculty_ID = graduation_threshold.Faculty_ID
					JOIN student
					ON graduation_threshold.Faculty_ID = student.Faculty_ID
					WHERE
					student.SID = :sid
					ORDER BY faculty.Name DESC";
			$parm = array(
				":sid"=>$sid
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				return json_encode($response);
			}else{
				return;
			}
		}

		public function get_now_threshold($sid){
			$sql = "SELECT
					A.Name,A.SUM_CREDIT
					FROM
					(SELECT
					remarks.Name,SUM(student_history_course.Get_Course_Credit) As SUM_CREDIT,CONCAT(course.School_Year,course.Semester) As 'year'
					FROM
					student_history_course
					JOIN course
					On student_history_course.Course_ID = course.Course_ID
					JOIN remarks
					ON course.Remarks_ID = remarks.Remarks_ID
					WHERE
					student_history_course.SID = :sid
					GROUP BY year,remarks.Name DESC,course.Semester DESC)A
					ORDER BY A.Name DESC";
			$parm = array(
				":sid"=>$sid
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				return json_encode($response);
			}else{
				return;
			}
		}
		// 搜尋該名任課老師所有教過的課程
		public function get_course_list($get,$post){
			$sql = "SELECT 
					course.Course_ID,course.Name,CONCAT(course.School_Year,course.Semester) As year,
					remarks.Name As remarks,category.Name As category 
					From course 
					JOIN teacher 
					ON course.Teacher_ID = teacher.Teacher_ID 
					JOIN remarks 
					ON course.Remarks_ID = remarks.Remarks_ID 
					JOIN category 
					ON course.Category_ID = category.Category_ID 
					WHERE teacher.Teacher_ID = :teacher_id";
			switch ($post["i"]) {
				case "0":
					$button = "Student";
					break;
				case "1":
					$button = "Outline";
					break;
				case "1-1":
					$button = "Score";
					$content = file_get_contents("./config.json");
					$content = json_decode($content,true);
					$sql .= "\nAND CONCAT(course.School_Year,course.Semester) = '".$content["nowyear"]."'";
					break;
			}
			$parm = array(
				":teacher_id"=>$_SESSION["login"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Button"=>$button,
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"搜尋時出錯"
				);
			}
			return json_encode($resMsg);
		}

		public function show_course_student($get,$post){
			if(isset($post["access"])){
				if($post["access"] == "OK"){
					$access = true;
				}else{
					$access = false;
				}
			}else{
				$access = false;
			}
			$sql = "SELECT
					student.SID,student.Name,faculty.Name As faculty,student.Grade,student_history_course.Score
					FROM student_history_course
					JOIN student
					ON student.SID = student_history_course.SID
					JOIN faculty
					ON student.Faculty_ID = faculty.Faculty_ID
					WHERE
					student_history_course.Course_ID = :course_id
					ORDER BY student.SID";
			$parm = array(
				":course_id"=>$post["course_id"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			for($i = 0;$i<$stmt->rowCount();$i++){
				foreach ($response[$i] as $key => $value) {
					if(is_null($response[$i][$key])){
						$response[$i][$key] = "";
					}
				}
			}
			if(count($response) > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Course_ID"=>$post["course_id"],
					"Access"=>($access)?true:false,
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"搜尋時出現錯誤"
				);
			}
			return json_encode($resMsg);
		}

		public function save_score($get,$post){
			$error_count = 0;
			$invaild_update = 0;
			$success_update = 0;
			foreach ($post["SID"] as $key => $value) {
				$sql = "UPDATE student_history_course
						SET Score = :score
						WHERE SID = :sid
						AND Course_ID = :course_id";
				$parm = array(
					":score"=>$post["score"][$key],
					":sid"=>$post["SID"][$key],
					":course_id"=>$post["course_id"]
				);
				try {
					$stmt = $this->conn->prepare($sql);
					$stmt->execute($parm);
					$count = $stmt->rowCount();
					if($count <= 0){
						$invaild_update += 1;
					}else{
						$success_update += 1;
					}
				} catch (Exception $e) {
					$error_count += 1;
				}
			}
			if($error_count > 0 || $invaild_update > 0){
				$resMsg = array(
					"Type"=>"Error",
					"Error_Count"=>$error_count,
					"Invaild_Update_Count"=>$invaild_update,
					"Success_Update_Count"=>$success_update
				);
			}else{
				$resMsg = array(
					"Type"=>"Success",
					"Error_Count"=>$error_count,
					"Invaild_Update_Count"=>$invaild_update,
					"Success_Update_Count"=>$success_update
				);
			}
			return json_encode($resMsg);
		}

		public function show_course_outline($get,$post){
			$sql = "SELECT
					course.Course_ID,course.Name,
					CONCAT(course.School_Year,course.Semester) As year,
					course.Outline
					FROM course
					WHERE course.Course_ID = :course_id";
			$parm = array(
				":course_id"=>$post["course_id"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>json_encode($response)
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"搜尋時出錯"
				);
			}
			return json_encode($resMsg);
		}

		public function save_outline($get,$post){
			$sql = "UPDATE course
					SET Outline = :outline
					WHERE Course_ID = :course_id";
			$parm = array(
				":course_id"=>$post["course_id"],
				":outline"=>$post["outline"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->rowCount();
			if($response > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Msg"=>"更新成功"
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"無資料受到更新"
				);
			}
			return json_encode($resMsg);
		}

		public function show_insert($get,$post){
			$content = file_get_contents("./config.json");
			$content = json_decode($content,true);
			$teacher = $this->get_all_teacher();
			$classroom = $this->get_all_classroom();
			$category = $this->get_all_category();
			$remarks = $this->get_all_remarks();
			$resMsg = array(
				"Type"=>"Success",
				"Insert_Type"=>$get["action"],
				"teacher"=>$teacher,
				"classroom"=>$classroom,
				"category"=>$category,
				"remarks"=>$remarks,
				"year"=>$content["nextyear"]
			);
			return json_encode($resMsg);
		}

		public function get_all_teacher(){
			$sql = "SELECT Teacher_ID,Name From teacher ORDER BY Teacher_ID";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			$teacher = $stmt->fetchAll();
			return $teacher;
		}

		public function get_all_category(){
			$sql = "SELECT Category_ID,Name From category ORDER BY Category_ID";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			$category = $stmt->fetchAll();
			return $category;
		}

		public function get_all_classroom(){
			$sql = "SELECT Classroom_ID,Name From classroom ORDER BY Classroom_ID";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			$classroom = $stmt->fetchAll();
			return $classroom;
		}

		public function get_all_remarks(){
			$sql = "SELECT Remarks_ID,Name From remarks ORDER BY Remarks_ID";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			$remarks = $stmt->fetchAll();
			return $remarks;
		}

		public function show_modify($get,$post){
			$sql = "SELECT
					course.Course_ID,course.Name As Cs_Name,CONCAT(School_Year,Semester) As Year_Sem,
					teacher.Name As T_Name,classroom.Name As R_Name,
					category.Name As C_Name,remarks.Name As RE_Name,
					Outline,Course_Credit,Day,Time
					From course 
					JOIN teacher
					On course.Teacher_ID = teacher.Teacher_ID
					Join classroom
					On course.Classroom_ID = classroom.Classroom_ID
					Join category
					On course.Category_ID = category.Category_ID
					Join remarks
					On course.Remarks_ID = remarks.Remarks_ID
					ORDER BY course.Course_ID";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute();
			$response = $stmt->fetchAll();
			$resMsg = array(
				"Type"=>"Success",
				"Insert_Type"=>$get["action"],
				"Msg"=>$response
			);
			return json_encode($resMsg);
		}

		public function show_modify_detail($get,$post){
			$teacher = $this->get_all_teacher();
			$classroom = $this->get_all_classroom();
			$category = $this->get_all_category();
			$remarks = $this->get_all_remarks();
			$sql = "SELECT
					course.Course_ID,course.Name As Cs_Name,CONCAT(School_Year,Semester) As Year_Sem,
					teacher.Teacher_ID As Teacher_ID,classroom.Classroom_ID As Classroom_ID,
					category.Category_ID As Category_ID,remarks.Remarks_ID As Remarks_ID,
					Outline,Course_Credit,Day,Time
					From course 
					JOIN teacher
					On course.Teacher_ID = teacher.Teacher_ID
					Join classroom
					On course.Classroom_ID = classroom.Classroom_ID
					Join category
					On course.Category_ID = category.Category_ID
					Join remarks
					On course.Remarks_ID = remarks.Remarks_ID
					Where course.Course_ID = :Course_ID";
			$parm = array(
				":Course_ID"=>$post["course_id"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Insert_Type"=>$get["action"],
					"teacher"=>$teacher,
					"classroom"=>$classroom,
					"category"=>$category,
					"remarks"=>$remarks,
					"Msg"=>$response
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"無此ID"
				);
			}
			return json_encode($resMsg);
		}

		public function insert($get,$post){
			$content = file_get_contents("./config.json");
			$content = json_decode($content,true);
			$sql = "INSERT INTO `course`
					(`Course_ID`, `Name`, `School_Year`, `Semester`, `Teacher_ID`, `Classroom_ID`, 
					 `Category_ID`, `Outline`, `Course_Credit`, `Day`, `Time`, `Remarks_ID`) 
					VALUES 
					(:Course_ID,:Name,:School_Year,:Semester,:Teacher_ID,:Classroom_ID,
					 :Category_ID,:Outline,:Course_Credit,:Day,:Time,:Remarks_ID)";
			$parm = array(
				":Course_ID"=>$post["Course_ID"],
				":Name"=>$post["Name"],
				":School_Year"=>substr($content["nextyear"],0,3),
				":Semester"=>substr($content["nextyear"],3),
				":Teacher_ID"=>$post["Teacher_ID"],
				":Classroom_ID"=>$post["Classroom_ID"],
				":Category_ID"=>$post["Category_ID"],
				":Outline"=>$post["Outline"],
				":Course_Credit"=>$post["Course_Credit"],
				":Day"=>$post["Day"],
				":Time"=>$post["Time"],
				":Remarks_ID"=>$post["Remarks_ID"]
			);

			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->rowCount();
			if($response > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Insert_Type"=>"insert",
					"Msg"=>"新增成功"
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"新增時出錯"
				);
			}
			return json_encode($resMsg);
		}

		public function modify($get,$post){
			$sql = "UPDATE `course` 
					SET 
					`Name`=:Name,`Teacher_ID`=:Teacher_ID,`Classroom_ID`=:Classroom_ID,
					`Category_ID`=:Category_ID,`Outline`=:Outline,`Course_Credit`=:Course_Credit,`Day`=:Day,`Time`=:Time,`Remarks_ID`=:Remarks_ID 
					WHERE course.Course_ID = :Course_ID";
			$parm = array(
				":Name"=>$post["Name"],
				":Teacher_ID"=>$post["Teacher_ID"],
				":Classroom_ID"=>$post["Classroom_ID"],
				":Category_ID"=>$post["Category_ID"],
				":Outline"=>$post["Outline"],
				":Course_Credit"=>$post["Course_Credit"],
				":Day"=>$post["Day"],
				":Time"=>$post["Time"],
				":Remarks_ID"=>$post["Remarks_ID"],
				":Course_ID"=>$post["Course_ID"]
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->rowCount();
			if($response > 0){
				$resMsg = array(
					"Type"=>"Success",
					"Insert_Type"=>"modify",
					"Msg"=>"更新成功"
				);
			}else{
				$resMsg = array(
					"Type"=>"Error",
					"Msg"=>"無資料更新"
				);
			}
			return json_encode($resMsg);
		}
	}
?>