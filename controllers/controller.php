<?php
	/*
	
	定義 Action Performend ，每一個 Controller 皆會繼承該 interface
	Action Performend 為判斷當前參數是什麼並且做處理
	*/
	interface Controller{
		public function ActionPerformed($event_message);
	}

?>