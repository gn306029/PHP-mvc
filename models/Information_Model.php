<?php
	/*
	基本資料查詢
	1.帳號登入判斷
	2.學生、老師、行政人員基本資料查詢修改
	*/
	class Information_Model{

		public function __construct(){}

		public function login($user,$pwd,$type){
			$conn = db::Get_Conn();
			switch ($type) {
				case "student":
					$sql = "SELECT Name From student Where SID = :SID AND Password = :Password";
					break;
				
				case "teacher":
					$sql = "SELECT Name From teacher Where Teacher_ID = :SID AND Password = :Password";
					break;
			}
			$parm = array(
				":SID"=>$user,
				":Password"=>md5($pwd)
			);
			$stmt = $conn->prepare($sql);
			$stmt->execute($parm);
			$response = $stmt->fetchAll();
			if(count($response) > 0){
				$conn = null;
				return json_encode($response);
			}else{
				$conn = null;
				return;
			}
		}
		/* 忘記密碼直接重設 ， 因為密碼使用 MD5 加密過了*/
		public function forget($user,$id,$pwd,$type){
			$conn = db::Get_Conn();
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
			$stmt = $conn->prepare($sql);
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