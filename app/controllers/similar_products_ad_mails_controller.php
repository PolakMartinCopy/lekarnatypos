<?php
App::import('Controller', 'AdMails');
class SimilarProductsAdMailsController extends AdMailsController {
	var $name = 'SimilarProductsAdMails';
	
	function beforeFilter() {
		parent::beforeFilter();
	}
}
?>