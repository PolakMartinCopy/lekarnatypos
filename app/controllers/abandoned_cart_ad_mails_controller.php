<?php
class AbandonedCartAdMailsController extends AppController {
	var $name = 'AbandonedCartAdMails';
	
	// odesle davku emailu s informaci o zapomenutem kosiku
	// chci volat 2x denne, o pulnoci odeslu zapomenute kosiky z rana predesleho dne, v poledne pak zapomenute kosiky z odpoledne predesleho dne
	function send_morning_batch($date = null) {
		return $this->send_batch('a', $date);
	}
	
	function send_afternoon_batch($date = null) {
		return $this->send_batch('p', $date);
	}
	
	// type a - rano (AM), p - odpoledne (PM)
	function send_batch($type = null, $date = null) {
		if (!$type) {
			die('neni dan typ rozesilky emailu se zapomenutym kosikem');
		}
		
		if (!$date) {
			$date = date('Y-m-d', strtotime('-1 day'));
		}
		
		if (!($conditions = $this->AbandonedCartAdMail->buildConditions($type, $date))) {
			die('nepodarilo se poskladat podminky: ' . $date . ', typ: ' . $type);
		}

		$carts = $this->AbandonedCartAdMail->Cart->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'abandoned_cart_ad_mails',
					'alias' => 'AbandonedCartAdMail',
					'type' => 'LEFT',
					'conditions' => array('Cart.id = AbandonedCartAdMail.new_cart_id')
				),
				array(
					'table' => 't_s_visits',
					'alias' => 'TSVisit',
					'type' => 'INNER',
					'conditions' => array('TSVisit.id = Cart.t_s_visit_id')
				),
				array(
					'table' => 'orders',
					'alias' => 'Order',
					'type' => 'LEFT',
					'conditions' => arraY('Order.t_s_visit_id = TSVisit.id')
				),
				array(
					'table' => 't_s_customer_devices',
					'alias' => 'TSCustomerDevice',
					'type' => 'INNER',
					'conditions' => array('TSCustomerDevice.id = TSVisit.t_s_customer_device_id'),
				),
				array(
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'INNER',
					'conditions' => array('Customer.id = TSCustomerDevice.customer_id')
				),
				array(
					'table' => 'carts_products',
					'alias' => 'CartsProduct',
					'type' => 'LEFT',
					'conditions' => array('Cart.id = CartsProduct.cart_id')
				)
			),
			'fields' => array('DISTINCT Cart.id')
		));

		$to = date('Y-m-d H:i:s');

		foreach ($carts as $cart) {
			// zakaznik
			$customer = $this->AbandonedCartAdMail->Cart->getCustomer($cart['Cart']['id']);
			// od kdy neudelal objednavku
			$from = $this->AbandonedCartAdMail->Cart->getFieldValue($cart['Cart']['id'], 'created');
			// pokud zakaznik neudelal objednavku v dobe od zalozeni kosiku do teto chvile
			if (!$this->AbandonedCartAdMail->Cart->TSVisit->TSCustomerDevice->Customer->hasOrderInInterval($customer['Customer']['id'], $from, $to)) {
				// poslu mu email o zapomenutem kosiku		
				$this->send($cart['Cart']['id']);
			}
		}
		die('here');
	}
			
	function send($cartId = null) {
		if (!$cartId) {
			return false;
		}
		// nevznikl dany kosik tim, ze jsem ho vytvorit pomoci tohoto emailu? (nechci posilat zapomenuty kosik porad dokola...)
		if (!$this->AbandonedCartAdMail->Cart->isBuiltFromAbandoned($cartId)) {
			// zjistim uzivatele
			if ($customer = $this->AbandonedCartAdMail->Cart->getCustomer($cartId)) {
				// ziskam sablonu
				$adMailTemplate = $this->AbandonedCartAdMail->AdMailTemplate->findByType('abandoned_cart');
				// vygeneruju element s produkty
				$productsBox = $this->AbandonedCartAdMail->getProductsBox($cartId);

				if ($adMailTemplate && $productsBox) {
					// ulozim odeslani emailu
					if ($this->AbandonedCartAdMail->init($cartId, $adMailTemplate['AdMailTemplate']['id'])) {
						// do sablony vlozim produkty
						$body = str_replace('%%products_box%%', $productsBox, $adMailTemplate['AdMailTemplate']['content']);
					
						// a kryptovane id kosiku
						$cryptCartId = urlencode(Security::cipher($cartId, Configure::read('Security.salt')));
						$body = str_replace('%%crypt_cart_id%%', $cryptCartId, $body);

						// kryptovane id emailu v db
						$cryptMailId = urlencode(Security::cipher($this->AbandonedCartAdMail->id, Configure::read('Security.salt')));
						$body = str_replace('%%crypt_mail_id%%', $cryptMailId, $body);
						
						$bodyAlternative = 'Pokud se Vám tento email nezobrazuje správně, zkopírujte prosím následující internetovou adresu do Vašeho prohlížeče: http://www.lekarnatypos.cz/carts/re_build_abandoned_cart/' . $cryptCartId . '/' . $cryptMailId . '?utm_source=newsletter&utm_medium=email&utm_campaing=OpustenyKosik&utm_content=bodyAlternative';
						
						// pokud poslu email
						if ($this->AbandonedCartAdMail->sendMail($adMailTemplate['AdMailTemplate']['subject'], $body, $bodyAlternative, $customer['Customer']['email'])) {
							$this->AbandonedCartAdMail->setSent($this->AbandonedCartAdMail->id);
						}
					}
				}
			}
		}
		return false;
	}
	
	function is_opened($cryptId) {
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/files/ad_mail_templates_images/lekarnatypos-logo.png'; 
		$image = download_url($url);
		echo $image;
		
		$id = urldecode(Security::cipher($cryptId, Configure::read('Security.salt')));
		$this->AbandonedCartAdMail->setOpened($id);
		die();
	}
	
}