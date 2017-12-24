<?php
	
	require_once("includes/php/db.php");
	abstract class Model{
		protected $conn = null;
		public function __construct(){
			$this->conn = db::Get_conn();
		}

	}

?>