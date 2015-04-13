<?php 
class CustomerLoginsController extends AppController {
	var $name = 'CustomerLogins';
	
	function admin_import() {
		$customers = $this->CustomerLogin->Customer->find('all', array(
			'contain' => array(),
			'fields' => array('Customer.id', 'Customer.login', 'Customer.password')
		));
		
		$customer_logins = array(
			'CustomerLogin' => array()
		);
		
		foreach ($customers as $customer) {
			$customer_logins['CustomerLogin'][] = array(
				'customer_id' => $customer['Customer']['id'],
				'login' => $customer['Customer']['login'],
				'password' => $customer['Customer']['password']
			);
		}
		$this->CustomerLogin->saveAll($customer_logins['CustomerLogin']);
		die('hotovo');
	}
}
?>