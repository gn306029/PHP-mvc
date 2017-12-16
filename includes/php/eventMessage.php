<?php
	/*
		
		每個 Router 皆會將 GET 與 POST 參數值放入這個物件中
		再將這個物件傳遞到 Controller 之中

	*/
	class eventMessage{

		private $get;
		private $post;

		public function __construct($get,$post){

			$this->get = $get;
			$this->post = $post;

		}

		public function Get(){
			return $this->get;
		}

		public function Post(){
			return $this->post;
		}

	}

?>