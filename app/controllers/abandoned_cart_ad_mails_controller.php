<?php
class AbandonedCartAdMailsController extends AppController {
	var $name = 'AbandonedCartAdMails';
	
	function mail_complet_api_test($id = null) {
		App::import('Vendor', 'MailKomplet', array('file' => 'mail_komplet.php'));
		$mailKomplet = &new MailKomplet;
	
		$mailKomplet->login();
		//		$mailKomplet->getBusinessUnits();
		$mailKomplet->logout();
		die();
	}
			
	function send($cartId = 601437) { // XXX - jiny clovek, XXX - anonymni navsteva, 601437 - moje navsteva
		// ziskam sablonu
		$adMailTemplate = $this->AbandonedCartAdMail->AdMailTemplate->findByType('abandoned_cart');
		// vygeneruju element s produkty
		$productsBox = $this->AbandonedCartAdMail->getProductsBox($cartId);
		// zjistim uzivatele
 		$customer = $this->AbandonedCartAdMail->Cart->getCustomer($cartId);
		if ($adMailTemplate && $productsBox && $customer) {
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
debug($body); die();
				$customerName = $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'];
				// pokud poslu email
				if ($this->AbandonedCartAdMail->sendMail($adMailTemplate['AdMailTemplate']['subject'], $body, $customer['Customer']['email'], $customerName)) {
					$this->AbandonedCartAdMail->setSent($this->AbandonedCartAdMail->id);
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