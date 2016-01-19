<?php
class TSVisit extends AppModel {
	var $name = 'TSVisit';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSCustomerDevice');
	
	var $hasMany = array('TSVisitCategory', 'Cart');
	
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
		
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		$useragent = $this->Session->read('Config.userAgent');
		$referer = null;
		
		if (isset($_SERVER['HTTP_REFERER'])) {
			$referer = $_SERVER['HTTP_REFERER'];
		}

		$save = array(
			'TSVisit' => array(
				't_s_customer_device_id' => $t_s_customer_device_id,
				'useragent' => $useragent,
				'full_useragent' => $_SERVER['HTTP_USER_AGENT'],
				'referer' => $referer,
				'ip_address' => $_SERVER['REMOTE_ADDR']
			)
		);

		// neukladam navstevu, pokud je uzivatel monitorovaci system
		if ($_SERVER['HTTP_USER_AGENT'] != 'Brko Web Page Monitor') {
			if ($this->save($save)) {
				$this->visitId = $this->id;
				return $this->visitId;
			} 
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
	
	function recountDuration($id) {
		$visit = $this->find('first', array(
			'conditions' => array('TSVisit.id' => $id),
			'contain' => array(),
		));
		
		if (empty($visit)) {
			return false;
		}
		
		$start = strtotime($visit['TSVisit']['created']);
		$end =  strtotime(date('Y-m-d H:i:s'));
		// delka navstevy v sekundach
		$duration = $end - $start;
		
		$visit['TSVisit']['modified'] = date('Y-m-d H:i:s');
		$visit['TSVisit']['duration'] = $duration;
		return $this->save($visit);
	}
	
	// close - ukoncit navstevu (delka)
	function close($id) {
		$visit = $this->get();
		$visit['TSVisit']['closed'] = true;
		return $this->save($visit);
	}
	
	function closeList($list) {
		if (empty($list)) {
			return true;
		}
		
		$save = array();

		foreach ($list as $id) {
			$save[] = array(
				'id' => $id,
				'closed' => true
			);
		}
		return $this->saveAll($save);
	}
	
	function closeExpired() {
		// vytahnu expirovane navstevy
		// tzn nejsou ukoncene a posledni uprava (prodlozeni) probehla pred vice nez definovanou dobou
		// defaultne si tady nastavim expiraci po 60 minutach
		$visit_expiration = 60;
		// pokud mam v systemu nastaveno jinak
		if (defined('TSVISIT_EXPIRATION')) {
			$visit_expiration = TSVISIT_EXPIRATION;
		}
		$expiration_landmark = date('Y-m-d H:i:s', strtotime('-' . $visit_expiration . ' minutes'));
		
		$expired = $this->find('all', array(
			'conditions' => array(
				'TSVisit.closed' => false,
				'TSVisit.modified <' => $expiration_landmark
			),
			'contain' => array(),
			'fields' => array('TSVisit.id')
		));
		$expired = Set::extract('/TSVisit/id', $expired);

		return $this->closeList($expired);
	}
	
	function pairCart() {
		$visitId = $this->visitId;
		$cartId = $this->Cart->get_id();
		if ($visitId && $cartId) {
			if ($this->Cart->hasAny(array(
				'Cart.id' => $cartId,
				'Cart.t_s_visit_id' => null
			))) {
				$save = array(
					'Cart' => array(
						'id' => $cartId,
						't_s_visit_id' => $visitId
					)
				);
				
				return $this->Cart->save($save);
			}
		}
		return false;
	}
}