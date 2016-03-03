<?php 
App::import('Model', 'AdMail');
class SimilarProductsAdMail extends AdMail {
	var $name = 'SimilarProductsAdMail';
	
	var $actsAs = array('Containable');
	
	var $useTable = 'ad_mails';
	
	var $interval = '- 1 day';
	
	var $mailTemplateType = 'similar_products';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	// seznam lidi, kterym chci v ramci davky newsletter poslat
	// lidi, kteri nakoupili naposled v den pred intervalem???
	function getRecipients($date) {
		$theDay = date('Y-m-d', strtotime($this->interval, strtotime($date)));
		// kvuli optimalizaci vykonu vyberu nejdriv uzivatele, kteri meli objednavku v dany den a pak vyfiltruju ty, pro ktere nebyla posledni
		// vyberu uzivatele, kteri maji objednavku v den pred intervalem
		$orders = $this->Customer->Order->find('all', array(
			'conditions' => array(
				'DATE(Order.created)' => $theDay,
			),
			'contain' => array(),
			'fields' => array('DISTINCT Order.customer_id'),
		));

		$res = array();
		// do vystupu dam jen ty uzivatele, u kterych byla objednavka v dany den posledni
		foreach ($orders as $order) {
			$futureOrder = $this->Customer->Order->find('first', array(
				'conditions' => array(
					'DATE(Order.created) >' => $theDay,
					'Order.customer_id' => $order['Order']['customer_id']
				),
				'contain' => array()
			));
			if (!$futureOrder) {
				$res[] = $this->Customer->find('first', array(
					'conditions' => array('Customer.id' => $order['Order']['customer_id']),
					'contain' => array()
				));
			}
		}

		return $res;
	}
	
	// IDcka produktu do emailu
	// idcka souvisejicich produktu k tem, co mel v objednavce (den pred intervalem)
	function getProductIds($customerId, $customerTypeId, $date, $limit = 6) {
		$theDay = date('Y-m-d', strtotime($this->interval, strtotime($date)));
		// IDcka produkty, ktere si v dany den dany uzivatel objednal
		$orderedProductIds = $this->Customer->Order->find('all', array(
			'conditions' => array(
				'DATE(Order.created)' => $theDay,
				'Order.customer_id' => $customerId
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'ordered_products',
					'alias' => 'OrderedProduct',
					'type' => 'LEFT',
					'conditions' => array('Order.id = OrderedProduct.order_id')
				)
			),
			'fields' => array('DISTINCT OrderedProduct.product_id')
		));

		$orderedProductIds = Set::extract('/OrderedProduct/product_id', $orderedProductIds);
		// idcka souvisejicich produktu k produktum v objednavce
		$productIds = $this->Customer->Order->OrderedProduct->Product->similarProductIds($orderedProductIds, $customerTypeId, $limit);
		// pokud je produktu mene nez 3, potom vratim prazdne pole, aby se email vubec neposlal
		if (count($productIds) < 3) {
			return array();
		}

		return $productIds;
	}
}
?>