<?php
App::import('Controller', 'AdMails');
class MissYouAdMailsController extends AdMailsController {
	var $name = 'MissYouAdMails';
	
	function beforeFilter() {
		parent::beforeFilter();
	}
	
	function send_batch($notificateAdmins = true, $test = false, $date = null) {
		parent::send_batch($notificateAdmins, $test, $date);
	}
}
?>