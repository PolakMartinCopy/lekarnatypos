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
		$customer = $this->Cart->getCustomer($cartId);

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
	
	function sendMail($subject, $body, $email, $customerName) {
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = new phpmailer();
		// uvodni nastaveni
		$mail->CharSet = 'utf-8';
		$mail->Hostname = CUST_ROOT;
		$mail->Sender = CUST_MAIL;
		$mail->IsHtml(true);
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = CUST_MAIL;
		$mail->FromName = CUST_NAME;
		
		$mail->AddReplyTo(CUST_MAIL, CUST_NAME);
		
		$mail->AddAddress($email, $customerName);
		$mail->AddBCC('brko11@gmail.com');
		$mail->Subject = $subject;
		$mail->Body = $body;

		return $mail->Send();
	}
}