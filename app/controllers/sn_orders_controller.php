<?php
class SnOrdersController extends AppController {
	var $name = 'SnOrders';
	
	/**
	 * Stahne zdrojovy kod indexu objednavek
	 */
	function download_all() {
		// nastaveni
		$order_types = $this->SnOrder->types();
		
		// prihlasim se na SN
		if ($this->SnOrder->sn_connect()) {
			// pro kazdy typ objednavky
			foreach ($order_types as $order_type) {
				// zjistim, kolik podstranek ma dany index
				if ($result = $this->SnOrder->sn_download($order_type['url'])) {
					$last_index = $this->SnOrder->get_last_pagination_index($result);
					// stahnu a ulozim kazdou podstranku lokalne
					for ($page = 1; $page <= $last_index; $page++) {
						// sestavim jmeno souboru
						$file_name = $this->SnOrder->folder . '/' . $order_type['type'] . '-' . $page . '.html';
						if (!file_exists($file_name)) {
							$page_url = $order_type['url'] . $page;

							if (!$this->SnOrder->sn_save_page($page_url, $file_name)) {
								debug('nepodarilo se ulozit stranku ' . $order_type['url'] . ' do souboru ' . $file_name);
							}
						}
					}
				} else {
					debug('nepodarilo se stahnout stranku ' . $order_type['url']);
				}
			}
			$this->SnOrder->sn_disconnect();
		}
		die('stazeno');
	}
	
	// z lokalne ulozenych souboru vyparsuje data do db (do prechodnych tabulek)
	function parse() {
		// z nastaveni parseru nactu, ktery soubor byl zpracovan jako posledni
		$settings = $this->SnOrder->settings();
		foreach ($settings as $name => $value) {
			$$name = $value;
		}

		// pokud je v nastaveni prazdny nazev posledniho zpracovaneho souboru
		if (!$order_file_last_processed) {
			// vyprazdnim obsah tabulek sn_orders a sn_order_items
			$this->SnOrder->query('TRUNCATE TABLE sn_orders');
			$this->SnOrder->query('TRUNCATE TABLE sn_order_items');
		}
		
		// zjistim nazev souboru, ktery mam zpracovat (nasledujici za poslednim zpracovanym)
		if ($file = $this->SnOrder->get_actual_file($order_file_last_processed)) {
			$file_uri = $this->SnOrder->folder . '/' . $file;
			debug('zpracovavam ' . $file_uri);
			if ($file_content = file_get_contents($file_uri)) {
				// vyparsuju data z obsahu
				if ($orders = $this->SnOrder->parse($file_content)) {
					// prochazim objednavky
					foreach ($orders as $order) {
						if (!$this->SnOrder->hasAny(array('SnOrder.id' => $order['SnOrder']['id']))) {
							// ulozim do systemu
							$this->SnOrder->create();
							$this->SnOrder->SnOrderItem->create();
							if (!$this->SnOrder->saveAll($order)) {
								debug($order);
								debug('objednavku se nepodarilo ulozit');
							}
						}
					}
				} else {
					debug('parsovani obsahu v souboru ' . $file_uri . ' selhalo nebo pro dany typ nejsou na SN zadne objednavky');
				}
			} else {
				debug('nenahral se soubor ' . $file_uri);
			}
			// jakmile zpracuju soubor, ulozim do nastaveni jeho nazev jako nazev posledniho zpracovaneho
			$this->SnOrder->update_setting('order_file_last_processed', $file);
		}
		
		die('hotovo');
	}
	
	/**
	 * Transformace SPORTNUTRITION objednavek z pomocne tabulky do struktury obchodu
	 */
	function transform() {
		// vytahnu si netransformovane objednavky
		$orders = $this->SnOrder->find('all', array(
			'conditions' => array('SnOrder.transformed' => false),
			'contain' => array('SnOrderItem'),
			'order' => array('SnOrder.id' => 'asc'),
			'limit' => 200
		));

		// natahnu modely, ktere budu potrebovat
		App::import('Model', 'Customer');
		$this->Customer = new Customer;
		
		App::import('Model', 'Order');
		$this->Order = new Order;
		
		App::import('Model', 'Product');
		$this->Product = new Product;

		// natahnu zpusoby doruceni
		$shippings = $this->Order->Shipping->find('all', array(
			'conditions' => array('Shipping.sn_name !=' => ''),
			'contain' => array(),
			'fields' => array('Shipping.id', 'Shipping.sn_name')	
		));
		$shippings = Set::combine($shippings, '{n}.Shipping.sn_name', '{n}.Shipping.id');
		// natahnu zpusoby platby
		$payments = $this->Order->Payment->find('all', array(
			'conditions' => array('Payment.sn_name !=' => ''),
			'contain' => array(),
			'fields' => array('Payment.id', 'Payment.sn_name'),
		));
		$payments = Set::combine($payments, '{n}.Payment.sn_name', '{n}.Payment.id');
		// natahnu stavy objednavek
		$statuses = $this->Order->Status->find('all', array(
			'conditions' => array('Status.sn_name !=' => ''),
			'contain' => array(),
			'fields' => array('Status.id', 'Status.sn_name')
		));
		$statuses = Set::combine($statuses, '{n}.Status.sn_name', '{n}.Status.id');
		
		foreach ($orders as $order) {
			// inicializace dat k ulozeni - save_order
			$save_order = array();
			// k objednavce musim najit SN zakaznika podle emailu
			$customer = $this->Customer->find('all', array(
				'conditions' => array('Customer.email' => $order['SnOrder']['email']),
				'contain' => array(
					'Address' => array(
						'fields' => array('Address.id', 'Address.name', 'Address.street', 'Address.street_no', 'Address.city', 'Address.zip', 'Address.state', 'Address.type')
					)
				),
				'fields' => array('Customer.id', 'Customer.first_name', 'Customer.last_name', 'Customer.company_name', 'Customer.company_ico', 'Customer.company_dic', 'Customer.phone', 'Customer.email')
			));

			// ZACATEK TRANSAKCE
			$c_dataSource = $this->Order->getDataSource();
			$c_dataSource->begin($this->Order);
			try {
			
				// jestlize je zakaznik s danym emailem prave jeden
				if (count($customer) == 1) {
					$customer = $customer[0];
					$customer_name = '';
					// zjistim jeho jmeno
					if ($customer['Customer']['company_name']) {
						$customer_name = $customer['Customer']['company_name'];
					} elseif ($customer['Customer']['first_name'] && $customer['Customer']['last_name']) {
						$customer_name = $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'];
					}
					$delivery_address = null;
					$invoice_address = null;
					// zjistim jeho adresu
					foreach ($customer['Address'] as $address) {
						if ($address['type'] == 'f') {
							$invoice_address = $address;
						} elseif ($address['type'] == 'd') {
							$delivery_address = $address;
						}
					}
				
					// pokud mam adresu doruceni i adresu fakturacni
					if (!($delivery_address && $invoice_address)) {
						debug($order);
						debug($customer);
						throw new Exception('NEMAM DORUCOVACI NEBO FAKTURACNI ADRESU');
					} else {
						// poskladam objednavku
						$save_order = array(
							'Order' => array(
								'created' => $order['SnOrder']['date'],
								'customer_id' => $customer['Customer']['id'],
								'customer_name' => $customer_name,
								'customer_ico' => $customer['Customer']['company_ico'],
								'customer_dic' => $customer['Customer']['company_dic'],
								'customer_first_name' => $customer['Customer']['first_name'],
								'customer_last_name' => $customer['Customer']['last_name'],
								'customer_street' => trim($invoice_address['street'] . ' ' . $invoice_address['street_no']),
								'customer_city' => $invoice_address['city'],
								'customer_zip' => $invoice_address['zip'],
								'customer_state' => $invoice_address['state'],
								'customer_phone' => $customer['Customer']['phone'],
								'customer_email' => $customer['Customer']['email'],
								'delivery_name' => $customer_name,
								'delivery_first_name' => $customer['Customer']['first_name'],
								'delivery_last_name' => $customer['Customer']['last_name'],
								'delivery_street' => trim($delivery_address['street'] . ' ' . $delivery_address['street_no']),
								'delivery_city' => $delivery_address['city'],
								'delivery_zip' => $delivery_address['zip'],
								'delivery_state' => $delivery_address['state'],
								'status_id' => $statuses[$order['SnOrder']['state']],
								'comments' => $order['SnOrder']['note'],
								'sn_id' => $order['SnOrder']['id']
							),
							'OrderedProduct' => array()	
						);
	
						// spocitam si cenu produktu v objednavce
						$subtotal_with_dph = 0;
						$subtotal_wout_dph = 0;
	
						// naparuju order items
						foreach ($order['SnOrderItem'] as $order_item) {
							// inicializace ordered_product
							$ordered_product = array();
							// nejdriv se podivam, jestli ma order_item product_id, tim padem vim, ze je to produkt
							if ($order_item['product_id']) {
								// naplnim ordered_product
								$ordered_product = array(
									'product_price_with_dph' => $order_item['price_vat'],
									'product_price_wout_dph' => $order_item['price'],
									'product_quantity' => $order_item['quantity']	
								);
								// podivam se, jestli nemam produkt s danym sportnutrition_id
								$product = $this->Product->find('first', array(
									'conditions' => array('Product.sportnutrition_id' => $order_item['product_id']),
									'contain' => array(),
									'fields' => array('Product.id')	
								));
	
								// pokud takovej produkt nemam
								if (empty($product)) {
									// ulozim nazev produktu v objednavce
									$ordered_product['product_name'] = $order_item['name'];
								} else {
									// ulozim id produktu v objednavce
									$ordered_product['product_id'] = $product['Product']['id'];
								}
							// test, jestli neni order item zpusob doruceni
							} elseif (array_key_exists($order_item['name'], $shippings)) {
								// podivam se, jestli neni order item zpusob doruceni
								$save_order['Order']['shipping_id'] = $shippings[$order_item['name']];
								$save_order['Order']['shipping_cost'] = $order_item['price_vat'];
							// test, jestli neni order item zpusob platby
							} elseif (array_key_exists($order_item['name'], $payments)) {
								// pak se podivam, jestli neni order item zpusob platby
								$save_order['Order']['payment_id'] = $payments[$order_item['name']];
							
							} else {
								// order_item nema product_id a neni ani zpusob doruceni ani zpusob platby, pak je to produkt, u ktereho jsem nemohl zjistit product_id
								$ordered_product = array(
									'product_price_with_dph' => $order_item['price_vat'],
									'product_price_wout_dph' => $order_item['price'],
									'product_quantity' => $order_item['quantity'],
									'product_name' => $order_item['name']
								);
							}
							// pokud je order item produkt, pocitam cenu
							if (!empty($ordered_product)) {
								$subtotal_with_dph += $ordered_product['product_price_with_dph'];
								$subtotal_wout_dph += $ordered_product['product_price_wout_dph'];
								$save_order['OrderedProduct'][] = $ordered_product;
							}
						}
						
						$save_order['Order']['subtotal_with_dph'] = $subtotal_with_dph;
						$save_order['Order']['subtotal_wout_dph'] = $subtotal_wout_dph;
					
						// zkontroluju, jestli ma objednavka udaje o zpusobu doruceni a platbe
						if (!isset($save_order['Order']['shipping_id']) || !isset($save_order['Order']['payment_id'])) {
							debug($order);
							debug($save_order);
							throw new Exception('NEPODARILO SE VYPARSOVAT UDAJE O ZPUSOBU DORUCENI NEBO PLATBE');
						}
					
						// pokud mam objednavku s timto sn_id vlozenou, pouziju jeji id
						$db_order = $this->Order->find('first', array(
							'conditions' => array('Order.sn_id' => $save_order['Order']['sn_id']),
							'contain' => array(),
							'fields' => array('Order.id')
						));
					
						// musim smazat objednavku (s tim se mi smazou i jeji ordered_products (atributy produktu neparsuju)
						if (!empty($db_order)) {
							if (!$this->Order->delete($db_order['Order']['id'])) {
								debug($db_order);
								throw new Exception('OBJEDNAVKU SE NEPODARILO SMAZAT');
							}
							$save_order['Order']['id'] = $db_order['Order']['id'];
						}
						// ulozim vyparsovanou objednavku
						if ($this->Order->saveAll($save_order)) {
							// objednavku oznacim jako transformovanou
							$order['SnOrder']['transformed'] = true;
							if (!$this->SnOrder->save($order)) {
								debug($order);
								throw new Exception('OBJEDNAVKU SE NEPODARILO OZNACIT JAKO TRANSFORMOVANOU');
							}
						} else {
							debug($save_order);
							throw new Exception('OBJEDNAVKU SE NEPODARILO ULOZIT');
						}
					}

				} else {
					debug($order);
					debug($customer);
					throw new Exception('PRO DANY EMAIL NENI UNIKATNI UZIVATEL');
				}
			} catch (Exception $e) {
				// nekde je chyba
				$c_dataSource->rollback($this->Order);
				// vypisu hlasku
				debug('SELHALO ULOZENI OBJEDNAVKY');
				debug($e->getMessage());
				// upravim atribut transformed na -1, aby mi to dane objednavky preskakovalo
				$order['SnOrder']['transformed'] = -1;
				if (!$this->SnOrder->save($order)) {
					die('NEPODARILO SE NASTAVIT STAV OBJEDNAVKY, ABY SE PRESKAKOVALA');
				}
			}
					
			// KONEC TRANSAKCE
			$c_dataSource->commit($this->Order);
			
		}
		die('hotovo');
	}
}