<?php
class AdMailsController extends AppController {
	var $name = 'AdMails';
	
	function send_batch($notificateAdmins = false, $test = true, $date = null) {
		$model = $this->modelNames[0];
		$this->$model->sendBatch($notificateAdmins, $test, $date);
		die('here');
	}
	
	function is_opened($cryptId, $cryptEmail = null) {
		$model = $this->modelNames[0];
		echo $this->$model->isOpened($cryptId, $cryptEmail);
		die();
	}
}