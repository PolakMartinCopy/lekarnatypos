<?php 
class AdMail extends AppModel {
	var $name = 'AdMail';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('AdMailTemplate', 'Customer');
	
	function init($customerId) {
		$adMailTemplate = $this->AdMailTemplate->findByType($this->mailTemplateType);
		$save = array(
			$this->name => array(
				'sent' => false,
				'opened' => false,
				'ad_mail_template_id' => $adMailTemplate['AdMailTemplate']['id'],
				'customer_id' => $customerId
			)
		);

		$this->create();
		return $this->save($save);
	}
	
	function subject() {
		$mailTemplate = $this->AdMailTemplate->findByType($this->mailTemplateType);
	
		if (empty($mailTemplate)) {
			return false;
		}
		return $mailTemplate['AdMailTemplate']['subject'];
	}
	
	function bodyAlternative() {
		return 'http://www.lekarnatypos.cz/';
	}
	
	function setSent($id) {
		return $this->setAttribute($id, 'sent', true);
	}
	
	function setOpened($id) {
		return $this->setAttribute($id, 'opened', true);
	}
	
	function body($customerId, $date, $campaign) {
		$mailTemplate = $this->AdMailTemplate->findByType($this->mailTemplateType);
	
		if (empty($mailTemplate)) {
			return false;
		}
		$body = $mailTemplate['AdMailTemplate']['content'];
	
		$customerTypeId = $this->Customer->getFieldValue($customerId, 'customer_type_id');
	
		$productIds = $this->getProductIds($customerId, $customerTypeId, $date);
		if (empty($productIds)) {
			return false;
		}
		$products = $this->getProducts($productIds, $customerTypeId);
		$productsBox = $this->getProductsBox($products, $campaign);
	
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
	
	function getProductsBox($products, $campaignName) {
		$res = '';
		foreach ($products as $product) {
			$res .= '
	<tr>
	    <td valign="middle" style="padding: 3px 5px 3px 5px"><a href="http://www.lekarnatypos.cz/' . $product['Product']['url'] . '?utm_source=newsletter&utm_medium=email&utm_campaing=' . $campaignName . '" target="_blank"><img src="http://www.lekarnatypos.cz/product-images/small/' . $product['Image']['name'] . '" width="70"/></a></td>
	    <td valign="middle" style="padding:3px 5px 3px 0"><a href="http://www.lekarnatypos.cz/' . $product['Product']['url'] . '?utm_source=newsletter&utm_medium=email&utm_campaing=' . $campaignName . '" style="color: #63af29;" target="_blank">' . $product['Product']['name'] . '</a></td>
	    <td align="right" valign="middle" style="padding:3px 5px">' . $product['Product']['price'] . '&nbsp;Kč</td>
	</tr>';
		}
		return $res;
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
	function getProducts($productIds, $customerTypeId) {
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
	
	function sendMail($subject, $body, $bodyAlternative, $email) {
		App::import('Vendor', 'MailKomplet', array('file' => 'mail_komplet.php'));
		$mailKomplet = &new MailKomplet;
	
		$mailKomplet->login();
	
		// dispatcherId je pevne dano
		$dispatcherId = 1677;

		// poslu uzivateli / zakaznikovi pres mail komplet
		$mailKompletSent = $mailKomplet->sendMail(CUST_NAME, CUST_MAIL, $email, $subject, $body, $bodyAlternative, $dispatcherId);
	
		return $mailKompletSent;
	}
	

	function notificateAdmins($subject, $body) {
		$adminNotifications = array(
			array(
				'email' => 'brko11@gmail.com',
				'name' => 'Martin Polák'
			),
			array(
				'email' => 'martin@drdla.eu',
				'name' => 'Martin Drdla'
			),
			/*		array(
				 'email' => 'nejedly.lukyn@gmail.com',
				'name' => 'Lukáš Nejedlý'
			),*/
		);
		$success = true;
		foreach ($adminNotifications as $adminNotification) {
			$success = sendMail($subject, $body, $adminNotification['email'], $adminNotification['name'], true, 'no-reply@lekarnatypos.cz', false) && $success;
		}
		return $success;
	}
	
	function isOpened($cryptId, $cryptEmail = null) {
		$url = 'http://www.lekarnatypos.cz/files/ad_mail_templates_images/lekarnatypos-logo.png';
		$image = download_url($url);
		
		if ($cryptEmail) {
			$email = urldecode(Security::cipher($cryptEmail, Configure::read('Security.salt')));
			$adminEmails = array(
				'brko11@gmail.com',
				'nejedly.lukyn@gmail.com',
				'martin@drdla.eu'
			);
			// pokud jsem se sem dostal z administratorskeho emailu (notifikace), tak neoznacuju email jako otevreny
			if (in_array($email, $adminEmails)) {
				die();
			}
		}
		$id = urldecode(Security::cipher($cryptId, Configure::read('Security.salt')));
		$this->setOpened($id);
		
		return $image;
	}
}
?>