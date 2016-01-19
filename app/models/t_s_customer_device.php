<?php 
class TSCustomerDevice extends AppModel {
	var $name = 'TSCustomerDevice';

	var $actsAs = array('Containable');
	
 	var $belongsTo = array('Customer');
 	
 	var $hasMany = array('TSVisit');
 	
 	var $trackingKey = null;
 	
	function setKey($customer_id) {
		$save = array(
			'TSCustomerDevice' => array(
				'customer_id' => $customer_id
			)
		);
		
		if ($this->save($save)) {
			$this->trackingKey = $this->id;
			return $this->id;
		}
		return false;
	}
	
	function getKey($cookie, $session) {
		if ($key = $cookie->read('TSCustomerDevice.key')) {
			$this->trackingKey = $key;
		} else {
			$customer_id = null;
			if ($this->Customer->is_logged_in($session)) {
				$customer_id = $session->read('Customer.id');
			}
			if ($key = $this->setKey($customer_id)) {
				$cookie->write('TSCustomerDevice.key', $key, true, '1 year');
			} else {
				debug('nemam trackovaci klic');
				$key = false;
			}
		}
		return $key;
	}
	
	function setCustomerId($customerId) {
		if (isset($this->trackingKey)) {
			$save = array(
				'TSCustomerDevice' => array(
					'id' => $this->trackingKey,
					'customer_id' => $customerId
				)
			);
			$this->create();
			return $this->save($save);
		}
		return false;
	}
}
?>