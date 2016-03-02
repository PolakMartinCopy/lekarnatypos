<?php 
App::import('Model', 'AdMail');
class MissYouAdMail extends AdMail {
	var $name = 'MissYouAdMail';
	
	var $actsAs = array('Containable');
	
	var $useTable = 'ad_mails';
	
	var $interval = '- 1 month';
	
	var $mailTemplateType = 'miss_you';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	// seznam lidi, kterym chci v ramci davky newsletter poslat (pokud byl uzivatel naposled na webu v den pred intervalem)
	function getRecipients() {
		$theDay = date('Y-m-d', strtotime($this->interval));
		// kvuli optimalizaci vykonu vyberu nejdriv uzivatele, kteri meli navstevu v dany den a pak vyfiltruju ty, pro ktere nebyla posledni
		// vyberu uzivatele, kteri maji posledni navstevu v den pred intervalem
		$visits = $this->Customer->TSCustomerDevice->TSVisit->find('all', array(
			'conditions' => array(
				'DATE(TSVisit.created)' => $theDay,
				'TSCustomerDevice.customer_id IS NOT NULL'
			),
			'contain' => array('TSCustomerDevice'),
			'fields' => array('DISTINCT TSCustomerDevice.customer_id')
		));
		$res = array();
		// do vystupu dam jen ty uzivatele, u kterych byla navsteva v dany den posledni
		foreach ($visits as $visit) {
			$futureVisit = $this->Customer->TSCustomerDevice->TSVisit->find('first', array(
				'conditions' => array(
					'DATE(TSVisit.created) >' => $theDay,
					'TSCustomerDevice.customer_id' => $visit['TSCustomerDevice']['customer_id']
				),
				'contain' => array('TSCustomerDevice')
			));
			if (!$futureVisit) {
				$res[] = $this->Customer->find('first', array(
					'conditions' => array('Customer.id' => $visit['TSCustomerDevice']['customer_id']),
					'contain' => array()
				));
			}
		}
		
		return $res;
	}
	
	function body($customerId) {
		$mailTemplate = $this->AdMailTemplate->findByType($this->mailTemplateType);
		
		if (empty($mailTemplate)) {
			return false;
		}
		$body = $mailTemplate['AdMailTemplate']['content'];
		
		$customerTypeId = $this->Customer->getFieldValue($customerId, 'customer_type_id');
		
		$productIds = $this->getProductIds($customerId, $customerTypeId);
		$products = $this->getProducts($productIds, $customerTypeId);
		$productsBox = $this->getProductsBox($products, 'ChybiteNam');
		
		// do sablony vlozim produkty
		$body = str_replace('%%products_box%%', $productsBox, $body);
			
		// kryptovane id emailu v db
		$cryptMailId = urlencode(Security::cipher($this->id, Configure::read('Security.salt')));
		$body = str_replace('%%crypt_mail_id%%', $cryptMailId, $body);

		$email = $this->Customer->getFieldValue($customerId, 'email');
		
		$cryptEmail = urlencode(Security::cipher($email, Configure::read('Security.salt')));
		$body = str_replace('%%crypt_email%%', $cryptEmail, $body);

		return $body;
	}
	
	// chci max 3 produkty, ktere si mesic pred posledni navstevou prohlizel doplnene o produkty, ktere jsou v soucasne dobe v akcni nabidce na hlavni strance
	// aby bylo 6 produktu
	function getProductIds($customerId, $customerTypeId, $limit = 6) {
		$to = date('Y-m-d', strtotime($this->interval));
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