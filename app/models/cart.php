<?php
class Cart extends AppModel {
	var $name = 'Cart';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSVisit');
	
	var $hasOne = array('AbandonedCartAdMail');

	var $hasMany = array(
		'CartsProduct' => array(
			'className' => 'CartsProduct',
			'dependent' => true
		),
		'TSCartAddition'
	);
	
	function get_id() {
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		
		$rand = $this->Session->read('Config.rand');
		$userAgent = $this->Session->read('Config.userAgent');
		
		// zkusim najit v databazi kosik pro daneho uzivatele a aktualni navstevu
		$data = $this->find('first', array(
			'conditions' => array(
				'Cart.rand' => $this->Session->read('Config.rand'),
				'Cart.userAgent' => $this->Session->read('Config.userAgent'),
			),
			'contain' => array(),
			'fields' => array('Cart.id')
		));

		// kosik jsem v databazi nenasel,
		// musim ho zalozit
		if ( empty($data) ){
			return $this->_create();
		} else {
			return $data['Cart']['id'];
		}
	}

	function _create(){
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		
		$rand = $this->Session->read('Config.rand');
		$userAgent = $this->Session->read('Config.userAgent');
		
		$this->data['Cart']['rand'] = $rand;
		$this->data['Cart']['userAgent'] = $userAgent;

		// neukladam kosik, pokud je uzivatel monitorovaci system
		if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] != 'Brko Web Page Monitor') {
			$this->save($this->data);
		}

		return $this->getLastInsertID();
	}
	
	/* vrati cenu zbozi v kosiku */
	function totalPrice($id = null) {
		if (!$id) {
			// id kosiku
			$id = $this->get_id();
		}
		// vytahnu si vsechny produkty z db a spoctu si to
		$carts_products = $this->CartsProduct->find('all', array(
			'conditions' => array('cart_id' => $id),
			'fields' => array('quantity', 'price_with_dph')
		));
			
		$total_price = 0;
		foreach ($carts_products as $carts_product) {
			$total_price += $carts_product['CartsProduct']['price_with_dph'] * $carts_product['CartsProduct']['quantity'];
		}
		
		return $total_price;
	}
	
	// muzu mit pro dany obsah kosiku dopravu zdarma?
	function isFreeShipping($total_price = null) {
		// doprava muze byt zdarma bud proto, ze CENA ZBOZI V KOSIKU JE VYSSI NEZ NEJNIZSI MOZNA MEZ PRO DOPRAVU ZDARMA
		// anebo v kosiku mam ZBOZI, KTERE UMOZNUJE DOPRAVU ZDARMA ((neostrata nad 1200,- || syncare nad 700,-) && GEIS s platbou predem) 
		/*
		 * zjistim nejmensi moznou cenu objednavky, od ktere je doprava zdarma (mimo osobni odber)
		 * a pokud mam objednavku alespon v dane hodnote, je mozna doprava zdarma
		 */
		App::import('Model', 'Shipping');
		$this->Shipping = &new Shipping;
		$shipping = $this->Shipping->lowestFreeShipping();
		
		// pokud nemam rovnou zadanou cenu zbozi, musim vytahnout data z kosiku
		if (!$total_price) {
			$total_price = $this->totalPrice();
		}

		$free = $total_price > $shipping['Shipping']['free'];
		
		// doprava je mozna zdarma vzdy jen pro GEIS platbu predem - ID 32, 35
		$manufacturer_free_shipping_ids = array(32, 35);
		$i = 0;
		while (!$free && $i < count($manufacturer_free_shipping_ids)) {
			$manufacturer_free_shipping_id = $manufacturer_free_shipping_ids[$i];
			$free = $free || $this->CartsProduct->Product->OrderedProduct->Order->manufacturers_free_shipping($manufacturer_free_shipping_id);
			$i++;
		}
		
		if (!$free) {
			$free = $free || $this->CartsProduct->Product->OrderedProduct->Order->cart_product_count_free_shipping($manufacturer_free_shipping_id);
		}

		return $free;
	}
	
	// kolik mi zbyva, abych mohl mit dopravu zdarma (do hranice pro nulove dopravne u dane dopravy)
	function freeShippingRemaining($total_price = null) {
		/*
		 * zjistim nejmensi moznou cenu objednavky, od ktere je doprava zdarma (mimo osobni odber)
		 * a pokud mam objednavku alespon v dane hodnote, je mozna doprava zdarma
		 */
		App::import('Model', 'Shipping');
		$this->Shipping = &new Shipping;
		$shipping = $this->Shipping->lowestFreeShipping();
		
		// pokud nemam rovnou zadanou cenu zbozi, musim vytahnout data z kosiku
		if (!$total_price) {
			$total_price = $this->totalPrice();
		}

		$remaining = $shipping['Shipping']['free'] - $total_price + 1;
		return $remaining;
	}
	
	function getProducts($id) {
		// vytahnu si vsechny produkty, ktere patri
		// do zakaznikova kose
		$cart_products = $this->CartsProduct->find('all', array(
			'conditions' => array('CartsProduct.cart_id' => $id),
			'contain' => array(
				'Product' => array(
					'fields' => array('id', 'name')
				),
				'Subproduct' => array(
					'AttributesSubproduct' => array(
						'fields' => array('attribute_id')
					)
				)
			)
		));

		foreach ($cart_products as $index => $cart_product) {
			$cart_products[$index]['CartsProduct']['product_attributes'] = array();
			if (!empty($cart_product['CartsProduct']['subproduct_id'])) {
				$subproduct = $this->CartsProduct->Product->Subproduct->find('first', array(
					'conditions' => array('Subproduct.id' => $cart_product['CartsProduct']['subproduct_id']),
					'contain' => array(
						'AttributesSubproduct' => array(
							'Attribute' => array(
								'Option'
							)
						)
					)	
				));
				$product_attributes = array();
				foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
					$product_attributes[$attributes_subproduct['Attribute']['Option']['name']] = $attributes_subproduct['Attribute']['value'];
				}
				$cart_products[$index]['CartsProduct']['product_attributes'] = $product_attributes;
			}
		}
		return $cart_products;
	}
	
	function getCustomer($id) {
		$customer = $this->find('first', array(
			'conditions' => array('Cart.id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 't_s_visits',
					'alias' => 'TSVisit',
					'type' => 'INNER',
					'conditions' => array('TSVisit.id = Cart.t_s_visit_id')
				),
				array(
					'table' => 't_s_customer_devices',
					'alias' => 'TSCustomerDevice',
					'type' => 'INNER',
					'conditions' => array('TSCustomerDevice.id = TSVisit.t_s_customer_device_id')
				),
				array(
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'INNER',
					'conditions' => array('TSCustomerDevice.customer_id = Customer.id')
				)
			),
			'fields' => array('Customer.*')
		));
		return $customer;
	}
	
	// vyprazdni kosik (vymaze z nej produkty)
	function dump($id) {
		return $this->CartsProduct->deleteAll(array('CartsProduct.cart_id' => $id));
	}
	
	function copy($oldId, $newId) {
		$products = $this->CartsProduct->find('all', array(
			'conditions' => array('CartsProduct.cart_id' => $oldId),
			'contain' => array(),
			'fields' => array(
				'CartsProduct.product_id',
				'CartsProduct.subproduct_id',
				'CartsProduct.product_attributes',
				'CartsProduct.quantity',
				'CartsProduct.price_with_dph',
				'CartsProduct.price_wout_dph'
			)
		));
		
		$saveAll = array();
		foreach ($products as $product) {
			$product['CartsProduct']['cart_id'] = $newId;
			$saveAll[] = $product['CartsProduct'];
		}
		return $this->CartsProduct->saveAll($saveAll);
	}
}
?>