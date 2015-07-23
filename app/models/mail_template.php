<?
class MailTemplate extends AppModel{
	var $name = 'MailTemplate';
	
	var $validate = array(
		'subject' => array(
			'minLength' => array(
				'rule' => array('minLength', 2),
				'message' => 'Předmět emailu nesmí zůstat prázdný.'
			)
		),
		'content' => array(
			'minLength' => array(
				'rule' => array('minLength', 2),
				'message' => 'Obsah mailu nesmí zůstat prázdný.'
			)
		)
	);
	
	function process($id, $subject_id = null, $options = array()) {
		$template = $this->find('first', array(
			'conditions' => array('MailTemplate.id' => $id),
			'contain' => array()
		));
		
		if (empty($template)) {
			return false;
		}
		
		// template musim zpracovat, najdu si promenne
		$matches = '';
		$matches2 = '';
		preg_match_all("/%([a-zA-Z_]+\.[a-zA-Z_]+)%/U", $template['MailTemplate']['content'], $matches, PREG_SET_ORDER);
		preg_match_all("/%([a-zA-Z_]+\.[a-zA-Z_]+)%/U", $template['MailTemplate']['subject'], $matches2, PREG_SET_ORDER);
		
		// spojim matches
		$matches = array_merge($matches, $matches2);

		foreach ($matches as $match) {
			$wildcard = $match[1];
			$replace = $this->getWildcardValue($wildcard, $subject_id, $options);

			// nahradim to
			$template['MailTemplate']['content'] = str_replace($match[0], $replace, $template['MailTemplate']['content']);
			$template['MailTemplate']['subject'] = str_replace($match[0], $replace, $template['MailTemplate']['subject']);
		}
		return $template;
	}
	
	function getWildcardValue($wildcard, $subject_id = null, $options = array()) {
		$res = null;
		// natahnu modely
		App::import('Model', 'Order');
		$this->Order = &new Order;
		App::import('Model', 'Comment');
		$this->Comment = &new Comment;
		
		switch ($wildcard) {
			case 'Order.id':
			case 'Customer.id':
				if ($subject_id) {
					$res = $subject_id;
				}
				break;
			case 'Order.created':
			case 'Order.customer_name':
			case 'Order.customer_phone':
			case 'Order.shipping_number':
			case 'Order.variable_symbol':
			case 'Order.comments':
			case 'Comment.subject':
			case 'Comment.body':
			case 'Comment.reply':
				if ($subject_id) {
					$objects = explode('.', $wildcard);
					$field = $objects[1];
					$model = $objects[0];
					$value = $this->{$model}->getFieldValue($subject_id, $field);
					$res = $value;
					if ($field == 'created') {
						$res = cz_date_time($value, '.');
					}
				}
				break;
			case 'Order.shipping_name':
				if ($subject_id) {
					$shipping_id = $this->Order->getFieldValue($subject_id, 'shipping_id');
					if ($shipping_id) {
						$res = $this->Order->Shipping->getFieldValue($shipping_id, 'name');
					}
				}
				break;
			case 'Order.total_price':
				if ($subject_id) {
					$order = $this->Order->find('first', array(
						'conditions' => array('Order.id' => $subject_id),
						'contain' => array()
					));
					if (!empty($order)) {
						$res = $order['Order']['orderfinaltotal'];
					}
				}
				break;
 			case 'Order.delivery_address':
 				if ($subject_id) {
 					$order = $this->Order->find('first', array(
 						'conditions' => array('Order.id' => $subject_id),
 						'contain' => array(),
 					));
 					if (!empty($order)) {
 						$res = $order['Order']['delivery_name'];
						$name = full_name($order['Order']['delivery_first_name'], $order['Order']['delivery_last_name']);
						if (!empty($name) && $name != $order['Order']['delivery_name']) {
							$res .= '<br/>' . $name;
						}
						$res .= '<br/>' . $order['Order']['delivery_street'] . '<br/>' . $order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city'] . '<br/>' . $order['Order']['delivery_state'];
 					}
 				}
 				break;
 			case 'Order.invoice_address':
 				if ($subject_id) {
 					$order = $this->Order->find('first', array(
 						'conditions' => array('Order.id' => $subject_id),
 						'contain' => array(),
 					));
 					if (!empty($order)) {
 						$res = $order['Order']['customer_name'];
 						$name = full_name($order['Order']['customer_first_name'], $order['Order']['customer_last_name']);
 						if (!empty($name) && $name != $order['Order']['customer_name']) {
 							$res .= '<br/>' . $name;
 						}
 						$res .= '<br/>' . $order['Order']['customer_street'] . '<br/>' . $order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city'] . '<br/>' . $order['Order']['customer_state'];
 					}
 				}
 				break;
 			case 'Order.order_items_table':
 				if ($subject_id) {
 					$order = $this->Order->find('first', array(
 						'conditions' => array('Order.id' => $subject_id),
 						'contain' => array(
 							'OrderedProduct' => array(
	 							'OrderedProductsAttribute' => array(
	 									'Attribute' => array(
	 											'Option'
	 									)
	 							),
	 							'Product',
 							),
 							'Shipping'
 						)
 					));
 					if (!empty($order['OrderedProduct'])) {
 						$table = '
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;padding:3px;margin-bottom:10px;border:solid 1px #d8d7d7">
	<tbody>
		<tr>
			<th style="background-color:#dafadb" width="50"></th>
			<th align="left" style="font-size:11px;font-weight:normal;padding:4px 5px 4px 0;background-color:#dafadb">Název produktu</th>
			<th align="right" style="font-size:11px;font-weight:normal;padding:4px 5px 4px 0;background-color:#dafadb">Cena</th>
		</tr>';
 						foreach ($order['OrderedProduct'] as $ordered_product) {
 							$product_name = $ordered_product['product_name'];
 							if (!empty($ordered_product['OrderedProductsAttribute'])) {
 								$attributes = array();
 								foreach ($$ordered_product['OrderedProductsAttribute'] as $opa) {
 									$attributes[] = $opa['Attribute']['Option']['name'] . ': ' . $opa['Attribute']['value'];
 								}
 								$attributes = implode(', ', $attributes);
 							}
 							if (!empty($attributes)) {
 								$product_name .= ' - ' . $attributes;
 							}
 							$product_name = '<a href="http://www.lekarnatypos.cz/' . $ordered_product['Product']['url'] . '">' . $product_name . '</a>';
 							$product_price = 
 							
 							$table .= '
		<tr>
			<td align="right" valign="top" style="padding:3px 5px 3px 5px">' . $ordered_product['product_quantity']  . '&nbsp;&times;</td>
			<td valign="top" style="padding:3px 5px 3px 0">' . $product_name . '</td>
			<td align="right" valign="top" style="padding:3px 5px">' . front_end_display_price($ordered_product['product_price_with_dph']) . '&nbsp;Kč</td>
		</tr>';
 						}
 						$table .= '
		<tr>
			<td>&nbsp;</td>
			<td style="padding:6px 5px 3px 0">' . $order['Shipping']['name'] . '</td>
			<td align="right" style="padding:3px 5px">' . front_end_display_price($order['Order']['shipping_cost']) . '&nbsp;Kč</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td valign="baseline" style="padding:6px 5px 6px 0"><b>Cena za zásilku:</b> (včetně dopravy a DPH)</td>
			<td align="right" valign="baseline" style="padding:6px 5px 6px 5px"><b>' . front_end_display_price($order['Order']['orderfinaltotal']) . '&nbsp;Kč</b></td>
		</tr>
	</tbody>
</table>';
 						$res = $table;
 					}
 				}
 				break;
 			case 'Order.personal_purchase_items_table':
 				if ($subject_id) {
	 				$order = $this->Order->find('first', array(
 						'conditions' => array('Order.id' => $subject_id),
 						'contain' => array(
 						'OrderedProduct' => array(
 							'OrderedProductsAttribute' => array(
 								'Attribute' => array(
 									'Option'
 								)
	 						),
 							'Product',
 							),
 							'Shipping'
 						)
	 				));
 					if (!empty($order['OrderedProduct'])) {
 						$table = '
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;padding:3px;margin-bottom:10px;border:solid 1px #d8d7d7">
	<tbody>
		<tr>
			<th style="background-color:#dafadb" width="50"></th>
			<th align="left" style="font-size:11px;font-weight:normal;padding:4px 5px 4px 0;background-color:#dafadb">Název produktu</th>
			<th align="right" style="font-size:11px;font-weight:normal;padding:4px 5px 4px 0;background-color:#dafadb">Cena</th>
		</tr>';
 						foreach ($order['OrderedProduct'] as $ordered_product) {
 							$product_name = $ordered_product['product_name'];
 							if (!empty($ordered_product['OrderedProductsAttribute'])) {
 								$attributes = array();
 								foreach ($$ordered_product['OrderedProductsAttribute'] as $opa) {
 									$attributes[] = $opa['Attribute']['Option']['name'] . ': ' . $opa['Attribute']['value'];
 								}
 								$attributes = implode(', ', $attributes);
	 						}
 							if (!empty($attributes)) {
 								$product_name .= ' - ' . $attributes;
 							}
 							$product_name = '<a href="http://www.lekarnatypos.cz/' . $ordered_product['Product']['url'] . '">' . $product_name . '</a>';
 							$product_price =
 					
 							$table .= '
		<tr>
			<td align="right" valign="top" style="padding:3px 5px 3px 5px">' . $ordered_product['product_quantity']  . '&nbsp;&times;</td>
			<td valign="top" style="padding:3px 5px 3px 0">' . $product_name . '</td>
			<td align="right" valign="top" style="padding:3px 5px">' . front_end_display_price($ordered_product['product_price_with_dph']) . '&nbsp;Kč</td>
		</tr>';
 						}
 						$table .= '
		<tr>
			<td>&nbsp;</td>
			<td valign="baseline" style="padding:6px 5px 6px 0"><b>Cena celkem:</b></td>
			<td align="right" valign="baseline" style="padding:6px 5px 6px 5px"><b>' . front_end_display_price($order['Order']['orderfinaltotal']) . '&nbsp;Kč</b></td>
		</tr>
	</tbody>
</table>';
 						$res = $table;
 					}
 				}
 				break;
 			case 'Order.cash':
 				if ($subject_id) {
 					$order = $this->Order->find('first', array(
 						'conditions' => array('Order.id' => $subject_id),
 						'contain' => array(),
 					));
 					// pokud jsem platil bankovnim prevodem, v hotovosti pri prevzeti neplatim nic
 					$res = $order['Order']['orderfinaltotal'];
 					if ($order['Order']['payment_id'] == 2) {
 						$res = 0;
 					}
 				}
 				break;
 			case 'Order.tracking_url':
 				if ($subject_id) {
 					$order = $this->Order->find('first', array(
 						'conditions' => array('Order.id' => $subject_id),
 						'contain' => array('Shipping'),
 					));
 					$res = $order['Shipping']['tracker_prefix'] . $order['Order']['shipping_number'] . $order['Shipping']['tracker_postfix'];
 				}
 				break;
 			case 'Order.due_date':
 				$res = date('d.m.Y', strtotime('+7 days'));
 				break;
			case 'Order.delivery_date':
				$res = next_work_day();
				$res = cz_date($res, '.');
				break;
			case 'CustomerLogin.login':
				if (isset($options['login'])) {
					$res = $options['login'];
				}
				break;
			case 'CustomerLogin.password':
				if (isset($options['password'])) {
					$res = $options['password'];
				}
				break;
			case 'Customer.verification_hash':
				if (isset($subject_id)) {
					App::import('Model', 'Customer');
					$this->Customer = &new Customer;
					$res = $this->Customer->createVerifyHash($subject_id);
				}
		}
		return $res;
	}
}
?>