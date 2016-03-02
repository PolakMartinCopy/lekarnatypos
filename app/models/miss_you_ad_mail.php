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
		$products = $this->getProducts($customerId, $customerTypeId);
		
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
	
	function getProduct($productId, $customerTypeId) {
		$productIds = array(0 => $productId);
		$product = $this->getProducts($productIds, $customerTypeId);
		if (!empty($product)) {
			$product = $product[0];
		}
	
		return $product;
	}
	
	// produkty do newsletteru
	// chci max 3 produkty, ktere si mesic pred posledni navstevou prohlizel doplnene o produkty, ktere jsou v soucasne dobe v akcni nabidce na hlavni strance
	// aby bylo 6 produktu
	function getProducts($customerId, $customerTypeId, $limit = 6) {
		$to = date('Y-m-d', strtotime($this->interval));
		$from = date('Y-m-d', strtotime($this->interval, strtotime($to)));
		$productIds = $this->Customer->TSCustomerDevice->TSVisit->TSVisitProduct->customerMostVisitedProductIds($customerId, $customerTypeId, $from, $to);
		$limit -= count($productIds);
		
		if ($limit > 0) {
			// preferovane
			$preferedProductIds = $this->Customer->TSCustomerDevice->TSVisit->TSVisitProduct->Product->MostSoldProduct->mostSoldProductIds($customerId, $customerTypeId, $productIds, $limit);
			$productIds = array_merge($productIds, $preferedProductIds);
		}
		
		$this->Customer->Order->OrderedProduct->Product->virtualFields['price'] = $this->Customer->Order->OrderedProduct->Product->price;
		$products = $this->Customer->Order->OrderedProduct->Product->find('all', array(
			'conditions' => array(
				'Product.id' => $productIds,
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id AND Image.is_main = "1"')
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customerTypeId)
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.price',
							
				'Image.id',
				'Image.name',
			),
			'order' => array('FIELD(Product.id, ' . implode(',', $productIds) . ')')
		));

		return $products;
	}
	
	function buildConditions($type, $yesterdayDate) {
		switch ($type) {
			case 'a':
				$start = $yesterdayDate . ' 00:00:00';
				$end = $yesterdayDate . ' 11:59:59';
				break;
			case 'p':
				$start = $yesterdayDate . ' 12:00:00';
				$end = $yesterdayDate . ' 23:59:59';
				break;
		}
	
		if (isset($start) && isset($end)) {
			$conditions = array(
					'Cart.created >= "' . $start . '"',
					'Cart.created <= "' . $end . '"',
					// ke kosiku neni objednavka
					'Order.id IS NULL',
					// kosik nevznikl navstevou z emailu o opustenem kosiku
					'AbandonedCartAdMail.id IS NULL',
					// znam uzivatele k danemu kosiku
					'Customer.id IS NOT NULL',
					// musi mit produkty
					'CartsProduct.id IS NOT NULL'
			);
	
			return $conditions;
		}
		return false;
	
	}
}
?>