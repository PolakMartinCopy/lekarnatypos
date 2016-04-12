<?php
class FirstNamesController extends AppController {
	var $name = 'FirstNames';
	
	function import() {
		
		$sources = array(
			array('type' => 'm', 'name' => 'files/first_names/muzska-jmena.csv'),
			array('type' => 'f', 'name' => 'files/first_names/zenska-jmena.csv')
		);
		
		foreach ($sources as $source) {
			$this->FirstName->import($source['name'], $source['type']);
		}
		die('here');
	}
	
	function customer_gender_recognize() {
		$this->FirstName->customerGenderRecognize();
		die('here');
	}
}