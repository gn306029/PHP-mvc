<?php
	require_once("models/Model.php");
	class Course_Model extends Model{
		public function __construct(){
			parent::__construct();
		}
		// 用於查詢各類型之課程，目前為學生用
		public function search($user,$course_type){
			$sql = "SELECT
					course.Course_ID,course.Name As C_Name,CONCAT(School_Year,Semester) As Year_Sem,
					teacher.Name As T_Name,classroom.Name As R_Name,
					category.Name As C_Name,remarks.Name As RE_Name,
					Outline,Course_Credit,Day,Time,shc.Status,shc.Score
					From course 
					JOIN teacher
					On course.Teacher_ID = teacher.Teacher_ID
					Join classroom
					On course.Classroom_ID = classroom.Classroom_ID
					Join category
					On course.Category_ID = category.Category_ID
					Join remarks
					On course.Remarks_ID = remarks.Remarks_ID
					Left Join student_history_course As shc
					On course.Course_ID = shc.Course_ID
					Where
					category.Category_ID = :Category_ID
					And
					shc.SID = :SID;";
			$parm = array(
				":Category_ID"=>$course_type,
				":SID"=>$user
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
				return json_encode($response);
			}else{
				return;
			}

		}

		public function Grade_search($sid){
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
				":SID"=>$sid
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response)>0){
				return json_encode($response);
			}else{
				return;
			}
		}
		//取得歷史修課紀錄
		public function History_Course($sid){

			$sql = "SELECT
					course.Course_ID,course.Name,CONCAT(course.School_Year,course.Semester) As 'year',
					teacher.Name As 'teacher',classroom.Name As 'room',
					category.Name As 'category',remarks.Name As 'remarks',
					course.Outline,course.Course_Credit,CONCAT(course.Day,course.Time) As 'time'
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
				":SID"=>$sid
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response)>0){
				return json_encode($response);
			}else{
				return;
			}
		}
		//取得課程資訊
		public function information($course_id){
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
				":course_id"=>$course_id
			);
			$stmt = $this->conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			$resdata = array();
			if(count($response)>0){
				$resdata["information"] = $response;
			}else{
				return;
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
			return json_encode($resdata);
		}
		// 取當當前學期修課清單
		public function now($sid){
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
				":SID"=>$sid,
				":year"=>$content["nowyear"]
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
	}
?>