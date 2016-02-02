<?php
class AdMailsController extends AppController {
	var $name = 'AdMails';
	
	function send($id = null) {
		App::import('Vendor', 'MailKomplet', array('file' => 'mail_komplet.php'));
		$mailKomplet = &new MailKomplet;
		
		$mailKomplet->login();
//		$mailKomplet->getBusinessUnits();
		$mailKomplet->logout();
		$mailKomplet->getBusinessUnits();
		die();
	}
}