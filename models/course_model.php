<?php
	
	class Course_Model{
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
			$conn = db::Get_Conn();
			$stmt = $conn->prepare($sql);
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
				$conn = null;
				return json_encode($response);
			}else{
				$conn = null;
				return;
			}

		}
	}

?>