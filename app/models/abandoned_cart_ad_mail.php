<?php
class AbandonedCartAdMail extends AppModel {
	var $name = 'AbandonedCartAdMail';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Cart', 'AdMailTemplate');
	
	function init($cartId, $adMailTemplateId) {
		$save = array(
			'AbandonedCartAdMail' => array(
				'sent' => false,
				'opened' => false,
				'cart_id' => $cartId,
				'ad_mail_template_id' => $adMailTemplateId
			)
		);
		$this->create();
		return $this->save($save);
	}
	
	function setSent($id) {
		return $this->setAttribute($id, 'sent', true);
	}
	
	function setOpened($id) {
		return $this->setAttribute($id, 'opened', true);
	}
	
	function getProductsBox($cartId) {
		if ($customer = $this->Cart->getCustomer($cartId)) {
			$cart_products = $this->Cart->getProducts($cartId);
			
			$res = '';
			foreach ($cart_products as $cart_product) {
				$product = $this->getProduct($cart_product['Product']['id'], $customer['Customer']['customer_type_id']);
				$variant = false;
				if ($cart_product['CartsProduct']['subproduct_id']) {
					$variant = $this->Cart->CartsProduct->Product->Subproduct->getById($cart_product['CartsProduct']['subproduct_id']);
					$product['Product']['name'] .= ' - ' . $variant['Subproduct']['name'];
					$product['Product']['price'] += $variant['Subproduct']['price_with_dph'];
				}
			
				$res .= '
	<tr>
	    <td valign="middle" style="padding: 3px 5px 3px 5px"><a href="http://www.lekarnatypos.cz/' . $product['Product']['url'] . '?utm_source=newsletter&utm_medium=email&utm_campaing=OpustenyKosik" target="_blank"><img src="http://www.lekarnatypos.cz/product-images/' . $product['Image']['name'] . '" width="70" /></a></td>
	    <td valign="middle" style="padding:3px 5px 3px 0"><a href="http://www.lekarnatypos.cz/' . $product['Product']['url'] . '?utm_source=newsletter&utm_medium=email&utm_campaing=OpustenyKosik" style="color: #63af29;" target="_blank">' . $product['Product']['name'] . '</a></td>
	    <td align="right" valign="middle" style="padding:3px 5px 3px 5px">' . $cart_product['CartsProduct']['quantity'] . '&nbsp;×</td>
	    <td align="right" valign="middle" style="padding:3px 5px">' . $product['Product']['price'] . '&nbsp;Kč</td>
	</tr>';
			}
			
			return $res;
		}
		return false;
	}
	
	function getProduct($productId, $customerTypeId) {
		$productIds = array(0 => $productId);
		$product = $this->getProducts($productIds, $customerTypeId);
		if (!empty($product)) {
			$product = $product[0];
		}

		return $product;
	}
	
	function getProducts($productIds, $customerTypeId) {
		$this->Cart->CartsProduct->Product->virtualFields['price'] = $this->Cart->CartsProduct->Product->price;
		$products = $this->Cart->CartsProduct->Product->find('all', array(
			'conditions' => array(
				'Product.id' => $productIds,
				'Product.active' => true,
				'Availability.cart_allowed' => true
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
		
		// a pro kontrolu jeste sobe, MD a LN (adresy adminu definovane v metode v bootstrapu)
		$admin_subject = 'Zapomenuty košík pro ' . $email;
		notificate_admins($admin_subject, $body);

		return $mailKompletSent;
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