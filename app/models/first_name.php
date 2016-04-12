<?php
class FirstName extends AppModel {
	var $name = 'FirstName';
	
	function import($file, $gender) {
		if (file_exists($file)) {
			$content = file_get_contents($file);
			$names = explode("\n", $content);
			$save = array();
			foreach ($names as $name) {
				$name = trim($name);
				if (!$this->hasAny(array('name' => $name))) {
					$save[] = array(
						'name' => $name,
						'gender' => $gender == 'm'
					);
				}
			};
			if (!empty($save)) {
				return $this->saveAll($save);
			}
		}
		return false;
	}
	
	function customerGenderRecognize() {
		App::import('Model', 'Customer');
		$this->Customer = &new Customer;
		$customers = $this->Customer->find('all', array(
			'conditions' => array('Customer.gender IS NULL'),
			'contain' => array(),
			'fields' => array('Customer.id', 'Customer.first_name')
		));
		
		$save = array();
		foreach ($customers as $customer) {
			if (!empty($customer['Customer']['first_name'])) {
				$gender = $this->recognizeGender($customer['Customer']['first_name']);
				
				$save[] = array(
					'id' => $customer['Customer']['id'],
					'gender' => $gender
				);
			}
		}
		return $this->Customer->saveAll($save);
	}
	
	function recognizeGender($name) {
		$name = trim($name);
		$firstName = $this->find('first', array(
			'conditions' => array('FirstName.name' => $name),
			'contain' => array()
		));

		if (!empty($firstName)) {
			return $firstName['FirstName']['gender'];
		}
		return null;
	}
}