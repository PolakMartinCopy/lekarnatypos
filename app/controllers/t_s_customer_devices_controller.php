<?php 
class TSCustomerDevicesController extends AppController {
	var $name = 'TSCustomerDevices';
	
	function init() {
		$result = array(
			'success' => false,
		);
		
		// klic zakaznickeho zarizeni
		$key = $this->TSCustomerDevice->getKey($this->Cookie, $this->Session);
		
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