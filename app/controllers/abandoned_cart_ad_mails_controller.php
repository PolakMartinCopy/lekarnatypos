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
			
	function send($cartId = 601797) { // XXX - jiny clovek, XXX - anonymni navsteva, 593830 - moje navsteva
		// nevznikl dany kosik tim, ze jsem ho vytvorit pomoci tohoto emailu? (nechci posilat zapomenuty kosik porad dokola...)
		$isBuiltFromAbandoned = $this->AbandonedCartAdMail->Cart->isBuiltFromAbandoned($cartId);
		if (!$isBuiltFromAbandoned) {
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

						$customerName = $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'];
						// pokud poslu email
						if ($this->AbandonedCartAdMail->sendMail($adMailTemplate['AdMailTemplate']['subject'], $body, $customer['Customer']['email'], $customerName)) {
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