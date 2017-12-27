<?php
	abstract class Controller{
		abstract public function CheckSession();
		abstract public function router($action);
	}

?>