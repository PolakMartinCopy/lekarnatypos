<?php
App::import('Controller', 'AdMails');
class MissYouAdMailsController extends AdMailsController {
	var $name = 'MissYouAdMails';
	
	function beforeFilter() {
		parent::beforeFilter();
	}
}
?>