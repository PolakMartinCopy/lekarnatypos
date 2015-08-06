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
			$this->TRCustomerDevice->trackingKey = $this->id;
			return $this->id;
		}
		return false;
	}
}
?>