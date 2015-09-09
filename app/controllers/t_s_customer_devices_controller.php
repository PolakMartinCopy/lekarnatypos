<?php 
class TSCustomerDevicesController extends AppController {
	var $name = 'TSCustomerDevices';
	
	function init() {
		$result = array(
			'success' => false,
		);
		// je zakaznik zalogovany
		$is_logged_in = false;
		if ($this->Session->check('Customer')) {
			$customer = $this->Session->read('Customer');
			if (isset($customer['id']) && !empty($customer['id']) && !isset($customer['noreg'])) {
				$is_logged_in = true;
			}
		}
		
		if ($key = $this->Cookie->read('TSCustomerDevice.key')) {
			$this->TSCustomerDevice->trackingKey = $key;
		} else {
			$customer_id = null;
			if ($is_logged_in) {
				$customer_id = $this->Session->read('Customer.id');
			}
			if ($key = $this->TSCustomerDevice->setKey($customer_id)) {
				$this->Cookie->write('TSCustomerDevice.key', $key, true, '1 year');
			} else {
				debug('nemam trackovaci klic');
				$key = false;
			}
		}
		
		if ($t_s_visit = $this->TSCustomerDevice->TSVisit->get()) {
			$this->TSCustomerDevice->TSVisit->pairCart();
			$result['success'] = true;
			$result['visitId'] = $t_s_visit['TSVisit']['id'];
		}

		echo json_encode($result);
		die();
	}
}
?>