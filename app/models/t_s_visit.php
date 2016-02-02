<?php
class TSVisit extends AppModel {
	var $name = 'TSVisit';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSCustomerDevice');
	
	var $hasMany = array('TSVisitCategory', 'TSVisitProduct', 'Cart', 'Order');
	
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
		if (!$this->checkRobots()) {
			return false;
		}
		
		if (!$this->check()) {
			$this->myCreate();
		}

		$visit = $this->find('first', array(
			'conditions' => array('TSVisit.id' => $this->visitId),
			'contain' => array()
		));
		return $visit;
	}
	
	// vraci true, pokud neni prichozi robot z definovanych
	function checkRobots() {
		$robots = array('Googlebot', 'SeznamBot', 'bingbot', 'AdsBot', 'Seznam screenshot-generator');
		$robots = implode('|', $robots);
		$pattern = '/' . $robots . '/';
		return !preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
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
	
	function getVisitCategoriesSubquery($from, $to) {
		$dataSource = $this->getDataSource();
		$visitCategoriesSubquery = array(
			'conditions' => array(
				'DATE(TSVisit.created) >=' => $from,
				'DATE(TSVisit.created) <=' => $to
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 't_s_customer_devices',
					'alias' => 'TSCustomerDevice',
					'type' => 'INNER',
					'conditions' => array('TSVisit.t_s_customer_device_id = TSCustomerDevice.id')
				),
				array(
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'LEFT',
					'conditions' => array('TSCustomerDevice.customer_id = Customer.id')
				),
				array(
					'table' => 't_s_visit_categories',
					'alias' => 'TSVisitCategory',
					'type' => 'LEFT',
					'conditions' => array('TSVisitCategory.t_s_visit_id = TSVisit.id')
				),
				array(
					'table' => 'categories',
					'alias' => 'Category',
					'type' => 'INNER',
					'conditions' => array('Category.id = TSVisitCategory.category_id')
				),
			),
			'fields' => array(
				'Customer.id AS customer_id',
				'TSVisit.id AS visit_id',
				'TSVisit.created AS visit_created',
				'TSVisit.duration AS visit_duration',
				'"category" AS visit_type',
				'TSVisitCategory.id AS visit_element_id',
				'TSVisitCategory.created AS visit_element_created',
				'Category.id AS element_id',
				'Category.name AS element_name',
				'Category.parent_id AS category_parent_id',
				'"0" AS product_description_show',
				'"0" AS product_comments_show',
			),
			'order' => null,
			'table' => $dataSource->fullTableName($this),
			'alias' => 'TSVisit',
			'limit' => null,
			'offset' => null,
			'group' => null,
		);
		$visitCategoriesSubquery = $dataSource->buildStatement($visitCategoriesSubquery, $this);
		return $visitCategoriesSubquery;
	}
	
	function getVisitProductsSubquery($from, $to) {
		$dataSource = $this->getDataSource();
		$visitProductsSubquery = array(
			'conditions' => array(
				'DATE(TSVisit.created) >=' => $from,
				'DATE(TSVisit.created) <=' => $to
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 't_s_customer_devices',
					'alias' => 'TSCustomerDevice',
					'type' => 'INNER',
					'conditions' => array('TSVisit.t_s_customer_device_id = TSCustomerDevice.id')
				),
				array(
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'LEFT',
					'conditions' => array('TSCustomerDevice.customer_id = Customer.id')
				),
				array(
					'table' => 't_s_visit_products',
					'alias' => 'TSVisitProduct',
					'type' => 'LEFT',
					'conditions' => array('TSVisitProduct.t_s_visit_id = TSVisit.id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = TSVisitProduct.product_id')
				)
			),
			'fields' => array(
				'Customer.id AS customer_id',
				'TSVisit.id AS visit_id',
				'TSVisit.created AS visit_created',
				'TSVisit.duration AS visit_duration',
				'"product" AS visit_type',
				'TSVisitProduct.id AS visit_element_id',
				'TSVisitProduct.created AS visit_element_created',
				'Product.id AS element_id',
				'Product.name AS element_name',
				'"0" AS category_parent_id',
				'TSVisitProduct.description_show AS product_description_show',
				'TSVisitProduct.comments_show AS product_comments_show',
			),
			'order' => null,
			'table' => $dataSource->fullTableName($this),
			'alias' => 'TSVisit',
			'limit' => null,
			'offset' => null,
			'group' => null,
		);
		$visitProductsSubquery = $dataSource->buildStatement($visitProductsSubquery, $this);
		return $visitProductsSubquery;
	}
	
	// vlozil behem dane navstevy produkt do kosiku?
	function productCartInserted($id, $productId) {
		$productCartInsertion = $this->Cart->find('first', array(
			'conditions' => array(
				'Cart.t_s_visit_id' => $id,
				'TSCartAddition.product_id' => $productId
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 't_s_cart_additions',
					'alias' => 'TSCartAddition',
					'type' => 'LEFT',
					'conditions' => array('Cart.id = TSCartAddition.cart_id')
				)
			),
			'fields' => array('*')
		));
		
		return !empty($productCartInsertion);
	}
	
	// objednal si behem dane navstevy produkt?
	function productOrdered($id, $productId) {
		$productOrder = $this->Order->find('first', array(
			'conditions' => array(
				'Order.t_s_visit_id' => $id,
				'OrderedProduct.product_id' => $productId
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
			'fields' => array('*')
		));

		return !empty($productOrder);
	}
}