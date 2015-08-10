<?php
class Cart extends AppModel {
	var $name = 'Cart';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSVisit');

	var $hasMany = array(
		'CartsProduct' => array(
			'className' => 'CartsProduct',
			'dependent' => true
		)
	);
	
	function get_id() {
		App::import('Model', 'CakeSession');
		$this->Session = &new CakeSession;
		
		$rand = $this->Session->read('Config.rand');
		$userAgent = $this->Session->read('Config.userAgent');
		$t_s_visit = $this->TSVisit->get();
		
		// zkusim najit v databazi kosik
		// pro daneho uzivatele
		$data = $this->find('first', array(
			'conditions' => array(
				'Cart.rand' => $this->Session->read('Config.rand'),
				'Cart.userAgent' => $this->Session->read('Config.userAgent'),
				'Cart.t_s_visit_id' => $t_s_visit['TSVisit']['id']
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
		$t_s_visit = $this->TSVisit->get();
		
		$this->data['Cart']['rand'] = $rand;
		$this->data['Cart']['userAgent'] = $userAgent;
		$this->data['Cart']['t_s_visit_id'] = $t_s_visit['TSVisit']['id'];

		$this->save($this->data);
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

		$remaining = $shipping['Shipping']['free'] - $total_price;
		return $remaining;
	}
}
?>