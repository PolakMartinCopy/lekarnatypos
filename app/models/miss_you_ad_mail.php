<?php 
App::import('Model', 'AdMail');
class MissYouAdMail extends AdMail {
	var $name = 'MissYouAdMail';
	
	var $actsAs = array('Containable');
	
	var $useTable = 'ad_mails';
	
	var $interval = '- 1 month';
	
	var $mailTemplateType = 'miss_you';
	
	var $campaignName = 'ChybiteName';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	// seznam lidi, kterym chci v ramci davky newsletter poslat (pokud byl uzivatel naposled na webu v den pred intervalem a prohlidl si nejaky produkt)
	function getRecipients($date) {
		$theDay = date('Y-m-d', strtotime($this->interval, strtotime($date)));
		// kvuli optimalizaci vykonu vyberu nejdriv uzivatele, kteri meli navstevu v dany den a pak vyfiltruju ty, pro ktere nebyla posledni
		// vyberu uzivatele, kteri maji posledni navstevu v den pred intervalem
		// navstevy v dany den u lidi, ke kterym znam email
		$visits = $this->Customer->TSCustomerDevice->TSVisit->find('all', array(
			'conditions' => array(
				'DATE(TSVisit.created)' => $theDay,
				'TSCustomerDevice.customer_id IS NOT NULL'
			),
			'contain' => array('TSCustomerDevice'),
			'fields' => array('DISTINCT TSCustomerDevice.customer_id')
		));

		$res = array(
			'ids' => array(),
			'values' => array()
		);
		// do vystupu dam jen ty uzivatele, u kterych byla navsteva v dany den posledni
		foreach ($visits as $visit) {
			if (!in_array($visit['TSCustomerDevice']['customer_id'], $res['ids'])) {
				$futureVisit = $this->Customer->TSCustomerDevice->TSVisit->find('first', array(
					'conditions' => array(
						'DATE(TSVisit.created) >' => $theDay,
						'TSCustomerDevice.customer_id' => $visit['TSCustomerDevice']['customer_id']
					),
					'contain' => array('TSCustomerDevice'),
					'fields' => array('TSVisit.id')
				));
				// navsteva je posledni (neni zadna novejsi)
				if (!$futureVisit) {
					// uzivatele dam do vystupu
					$customer = $this->Customer->find('first', array(
						'conditions' => array('Customer.id' => $visit['TSCustomerDevice']['customer_id']),
						'contain' => array()
					));
					$res['ids'][] = $customer['Customer']['id'];
					$res['values'][] = $customer;
				}
			}
		}
		return $res['values'];
	}
	
	// chci max 3 produkty, ktere si mesic pred posledni navstevou prohlizel doplnene o produkty, ktere jsou v soucasne dobe v akcni nabidce na hlavni strance
	// aby bylo 6 produktu
	function getProductIds($customerId, $customerTypeId, $date, $limit = 6) {
		$to = date('Y-m-d', strtotime($this->interval, strtotime($date)));
		$from = date('Y-m-d', strtotime($this->interval, strtotime($to)));
		$productIds = $this->Customer->TSCustomerDevice->TSVisit->TSVisitProduct->customerMostVisitedProductIds($customerId, $customerTypeId, $from, $to);
		$limit -= count($productIds);
		
		if ($limit > 0) {
			// preferovane
			$preferedProductIds = $this->Customer->TSCustomerDevice->TSVisit->TSVisitProduct->Product->MostSoldProduct->mostSoldProductIds($customerId, $customerTypeId, $productIds, $limit);
			$productIds = array_merge($productIds, $preferedProductIds);
		}
		
		return $productIds;
	}
}
?>