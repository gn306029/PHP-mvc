<?php
	class db{

		public function __construct(){}

		public static function Get_Conn(){
			$content = file_get_contents("./config.json");
			$content = json_decode($content,true);
			$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8',$content['db']['db_host'],$content['db']['db_name']);
			$conn = new PDO($dsn,$content['db']['db_user'],$content['db']['db_password']);
			return $conn;
		}

	}
	
?>