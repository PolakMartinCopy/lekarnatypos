<?php
App::import('Model', 'TSVisitSomething');
class TSVisitProduct extends TSVisitSomething {
	var $name = 'TSVisitProduct';
	
	var $belongsTo = array('Product', 'TSVisit');
	
	var $somethingName = 'product';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	// k zobrazeni produktu behem navstevy pridat, ze byl zobrazen dlouhy popis produktu
	function productDescriptionShow() {
		$last = $this->getLastBySth();
		if (!empty($last)) {
			$last['TSVisitProduct']['description_show'] = true;
			return $this->save($last);
		}
		return false;
	}
	
	// k zobrazeni produktu behem navstevy pridat, ze byly zobrazeny komentare produktu
	function productCommentsShow() {
		$last = $this->getLastBySth();
		if (!empty($last)) {
			$last['TSVisitProduct']['comments_show'] = true;
			return $this->save($last);
		}
		return false;
	}
	
	// nejprohlizenejsi produkty daneho uzivatele v danem rozmezi
	function customerMostVisitedProductIds($customerId, $customerTypeId, $from, $to, $limit = 3) {
		$this->virtualFields['visit_count'] = 'COUNT(*)';

		// vyberu vsechny navstivene produkty v danem obdobi, ktere jsou v prodeji
		$conditions = array(
			'TSCustomerDevice.customer_id' => $customerId,
			'DATE(TSVisitProduct.created) >=' => $from,
			'DATE(TSVisitProduct.created) <=' => $to,
			'Availability.cart_allowed' => true,
			'Product.active' => true,
			$this->Product->price . ' >' => 0,
		);
		
		$productIds = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 't_s_visits',
					'alias' => 'TSVisit',
					'type' => 'INNER',
					'conditions' => array('TSVisitProduct.t_s_visit_id = TSVisit.id')
				),
				array(
					'table' => 't_s_customer_devices',
					'alias' => 'TSCustomerDevice',
					'type' => 'INNER',
					'conditions' => array('TSCustomerDevice.id = TSVisit.t_s_customer_device_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('TSVisitProduct.product_id = Product.id')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Product.availability_id = Availability.id')
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
			),
			'group' => 'TSVisitProduct.product_id',
			'fields' => array('TSVisitProduct.product_id', 'TSVisitProduct.visit_count'),
			'order' => array('TSVisitProduct.visit_count' => 'DESC'),
			'limit' => 100
		));

		$productIds = Set::extract('/TSVisitProduct/product_id', $productIds);
		// z nich pak vyfiltruju prvnich $limit produktu, ktere zakaznik v danem obdobi neobjednal
		$res = array();
		$i = 0;
		while (count($res) < $limit && $i < count($productIds)) {
			$productId = $productIds[$i];
			$orderedProduct = $this->TSVisit->TSCustomerDevice->Customer->Order->OrderedProduct->find('first', array(
				'conditions' => array(
					'OrderedProduct.product_id' => $productId,
					'Order.customer_id' => $customerId,
					'DATE(OrderedProduct.created) >=' => $from,
					'DATE(OrderedProduct.created) <=' => $to,
				),
				'contain' => array('Order'),
				'fields' => array('OrderedProduct.id'),
			));
			if (empty($orderedProduct)) {
				$res[] = $productId;
			}
			$i++;
		}
		return $res;
	}
}
?>