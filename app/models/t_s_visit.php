<?php
class TSVisit extends AppModel {
	var $name = 'TSVisit';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSCustomerDevice');
	
	var $hasMany = array('TSVisitCategory');
	
	var $visitId = null;
	
	function getActive() {
		$trackingKey = $this->TSCustomerDevice->trackingKey;
		if (!$trackingKey) {
			return false;
		}
		
		$visit = $this->find('first', array(
			'conditions' => array(
				't_s_customer_device_id' => $trackingKey,
				'closed' => false
			),
			'contain' => array(),
		));
		
		return $visit;
	}

	// check - zkontrolovat, jesti mam "otevrenou" navstevu
	// tedy jestli mam navstevu, ktera neni pro dany trackovaci klic ukoncena
	function check() {
		$t_s_visit = $this->getActive();
		if (empty($t_s_visit)) {
			return false;
		}
		$this->visitId = $t_s_visit['TSVisit']['id'];
		return $t_s_visit['TSVisit']['id'];
	}
	
	// set - zalozit navstevu
	function myCreate() {
		$t_s_customer_device_id = $this->TSCustomerDevice->trackingKey;
		$save = array(
			'TSVisit' => array(
				't_s_customer_device_id' => $t_s_customer_device_id
			)
		);
		if ($this->save($save)) {
			$this->visitId = $this->id;
			return $this->visitId;
		} 
		return false;
	}
	
	/*
	 * vyhleda navstevu definovanou dle visitId
	 */
	function get() {
		if (!$this->check()) {
			$this->myCreate();
		}
		
		$visit = $this->find('first', array(
			'conditions' => array('TSVisit.id' => $this->visitId),
			'contain' => array()
		));
		
		return $visit;
	}
	
	// close - ukoncit navstevu (delka)
	function close() {
		$visit = $this->get();

		$start = strtotime($visit['TSVisit']['created']);
		$end =  strtotime(date('Y-m-d H:i:s'));
		// delka navstevy v sekundach
		$duration = $end - $start;

		$visit['TSVisit']['closed'] = true;
		$visit['TSVisit']['duration'] = $duration;
		return $this->save($visit);
	}
	
	// byla objednavka - k otevrene navsteve pridat, ze byla provedena objednavka (jaka)
	function setOrder($orderId) {
		$visit = $this->get();
		
		$visit['TSVisit']['order_id'] = $orderId;
		if ($this->save($visit)) {
			return $this->close();
		}
		return false;
	}

}