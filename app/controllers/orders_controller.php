<?php
class OrdersController extends AppController {
	var $name = 'Orders';

	var $helpers = array('Form');

	var $paginate = array(
		'limit' => 20,
		'order' => array(
			'Order.created' => 'desc'
		),
	);

	function admin_index() {
		$status_id = 'all';
		if (isset($this->params['named']['status_id'])) {
			$status_id = $this->params['named']['status_id'];
			// zapamatuju si status v sesne
			$this->Session->write('Search.AdminOrderParams.status_id', $status_id);
		} else {
			// pokud nemam zadany status, podivam se, jestli nemam zapamatovany nejaky v sesne
			if ($this->Session->check('Search.AdminOrderParams.status_id')) {
				$status_id = $this->Session->read('Search.AdminOrderParams.status_id');
			} else {
				// a pokud ne, presmeruju na aktivni objednavky
				$this->redirect(array('status_id' => 'active'));
			}
		}
		
		// reset filtru
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'orders') {
			$this->Session->delete('Search.AdminOrderForm');
			$this->Session->delete('Search.AdminOrderParams');
			$redirect = array('controller' => 'orders', 'action' => 'index');
			if ($status_id) {
				$redirect['status_id'] = $status_id;
			}
			$this->redirect($redirect);
		}

		// implicitne si vyhledavam do seznamu vsechny objednavky (status_id:all)
		$conditions = array();
		// kdyz chci omezit vypis na urcity status
		$status = null;
		$statuses = null;
		if (is_numeric($status_id)) {
			$status = $this->Order->Status->find('first', array(
				'conditions' => array('Status.id' => $status_id),
				'contain' => array(),
				'fields' => array('Status.id', 'Status.name')	
			));
			$conditions = array('Order.status_id' => $status_id);
		// pokud mam nektery z vyjmenovanych stavu
		} else {
			$this->Order->Status->recursive = -1;
			$statuses = $this->Order->Status->find('all');
			foreach ($statuses as $key => $value) {
				$statuses[$key]['Status']['count'] = $this->Order->find('count', array(
					'conditions' => array('Order.status_id' => $statuses[$key]['Status']['id'])
				));
			}
			// aktivni objednavky - "otevrene statusy"
			if ($status_id == 'active') {
				$conditions = array('Status.closed' => false);
			}
		}
		$this->set('status_id', $status_id);
		$this->set('status', $status);
		$this->set('statuses', $statuses);
		
		if (isset($this->data['AdminOrderForm']['Order']['search_form']) && $this->data['AdminOrderForm']['Order']['search_form']) {
			$this->Session->write('Search.AdminOrderForm', $this->data['AdminOrderForm']);
			$conditions = $this->Order->do_form_search($conditions, $this->data['AdminOrderForm']);
		} elseif ($this->Session->check('Search.AdminOrderForm')) {
			$this->data['AdminOrderForm'] = $this->Session->read('Search.AdminOrderForm');
			$conditions = $this->Order->do_form_search($conditions, $this->data['AdminOrderForm']);
		}
		
		$page = 1;
		if (isset($this->params['named']['page'])) {
			$page = $this->params['named']['page'];
			$this->Session->write('Search.AdminOrderParams.page', $page);
		} else {
			$page = $this->Session->read('Search.AdminOrderParams.page');
		}
		
		$sort = false;
		if (isset($this->params['named']['sort'])) {
			$sort = $this->params['named']['sort'];
			$this->Session->write('Search.AdminOrderParams.sort', $sort);
		} else {
			$sort = $this->Session->read('Search.AdminOrderParams.sort');
		}
		
		$direction = 'asc';
		if ($sort && isset($this->params['named']['direction'])) {
			$direction = $this->params['named']['direction'];
			$this->Session->write('Search.AdminOrderParams.direction', $direction);
		} else {
			$direction = $this->Session->read('Search.AdminOrderParams.direction');
		}

		$joins = array(
			array(
				'table' => 'ordered_products',
				'alias' => 'OrderedProduct',
				'type' => 'LEFT',
				'conditions' => array('Order.id = OrderedProduct.order_id')
			),
			array(
				'table' => 'shippings',
				'alias' => 'Shipping',
				'type' => 'LEFT',
				'conditions' => array('Shipping.id = Order.shipping_id')
			),
			array(
				'table' => 'payments',
				'alias' => 'Payment',
				'type' => 'LEFT',
				'conditions' => array('Payment.id = Order.payment_id')
			),
			array(
				'table' => 'statuses',
				'alias' => 'Status',
				'type' => 'LEFT',
				'conditions' => array('Status.id = Order.status_id')
			)
		);
		
		// pokud se jedna o specifickeho uzivatele, omezim zobrazene objednavky a zbozi v nich
		// viz definice v Administrator->adminDefinedCategories
		$admin = $this->Session->read('Administrator');
		// pokud se jedna o administratora, ktery ma mit pristup jen k nekterym kategoriim shopu
		if ($this->Order->OrderedProduct->Product->CategoriesProduct->Category->AdministratorsCategory->hasAny(array('administrator_id' => $admin['id']))) {
			$categoryConditions = $this->Order->getAdminConditions($admin['id']);
			$categoryJoins = $this->Order->getAdminJoins();
			
			$conditions = array_merge($conditions, $categoryConditions);
			// nastavim si idcka kategorii, jejichz produkty chci vypisovat v objednavkach
			$categoryIds = $categoryConditions['CategoriesProduct.category_id'];
			$joins = array_merge($joins, $categoryJoins);
		}
		$fields = array('Order.id');

		$orders = $this->Order->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => $joins,
			'fields' => $fields
		));

		$orders_ids = Set::extract('/Order/id', $orders);
		$order_ids_conditions = array('Order.id' => $orders_ids);
		$this->paginate['conditions'] = $order_ids_conditions;
		$this->paginate['contain'] = array(
			'OrderedProduct' => array(
				'OrderedProductsAttribute' => array(
					'Attribute' => array(
						'Option'
					)
				),
				'Product'
			),
			'Ordernote' => array(
				'order' => array('Ordernote.created' => 'desc'),
				'Status',
				'Administrator'
			),
			'Status',
			'Shipping',
			'Payment',
			'Customer' => array(
				'CustomerType'
			),
			'DiscountCoupon'
		);
		$this->paginate['page'] = $page;
		if ($sort && $direction) {
			$this->paginate['order'] = array($sort => $direction);
		}

		$this->Order->virtualFields['total_price'] = 'Order.shipping_cost + Order.subtotal_with_dph';
		$this->Order->virtualFields['date'] = 'CONCAT(DATE_FORMAT(DATE(Order.created), "%d.%m.%Y"), " ", TIME(Order.created))';
		$orders = $this->paginate('Order');
		unset($this->Order->virtualFields['date']);
		unset($this->Order->virtualFields['total_price']);

		foreach ($orders as &$order) {
			$order['Customer']['orders_count'] = $this->Order->Customer->orders_count($order['Customer']['id']);
			$orderedProducts = array();
			foreach ($order['OrderedProduct'] as &$ordered_product) {
				// nastavim, jestli chci produkt zobrazit nebo ne
				// pouzivam to v pripade, ze v administraci mam cloveka, ktery ma na starost je cast eshopu (sportovni vyziva, drogerie)
				$ordered_product['show'] = false;				
				if ((empty($categoryIds)) || $this->Order->OrderedProduct->Product->CategoriesProduct->hasAny(array('category_id' => $categoryIds, 'product_id' => $ordered_product['product_id']))) {
					$ordered_product['show'] = true;
				}
				if ((!isset($ordered_product['product_name']) || (empty($ordered_product['product_name']))) && isset($ordered_product['Product']['name'])) {
					$ordered_product['product_name'] = $ordered_product['Product']['name'];
				}
			}
		}

		$this->set('orders', $orders);
		
		$this->Order->virtualFields['total_vat'] = 'SUM(Order.subtotal_with_dph + Order.shipping_cost)';	

		$total_vat = $this->Order->find('first', array(
			'conditions' => $order_ids_conditions,
			'contain' => array('Status'),
			'fields' => array('Order.total_vat')	
		));
		unset($this->Order->virtualFields['total_vat']);
		$total_vat = round($total_vat['Order']['total_vat']);
		$this->set('total_vat', $total_vat);
		
		$statuses_options = $this->Order->Status->find('list');
		$this->set('statuses_options', $statuses_options);

		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_view($id) {
		// nactu si data o objednavce
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('OrderedProduct.id', 'OrderedProduct.product_id', 'OrderedProduct.product_name', 'OrderedProduct.product_quantity', 'OrderedProduct.product_price_with_dph'),
					'OrderedProductsAttribute' => array(
						'fields' => array('OrderedProductsAttribute.id', 'OrderedProductsAttribute.ordered_product_id', 'OrderedProductsAttribute.attribute_id'),
						'Attribute' => array(
							'fields' => array('Attribute.id', 'Attribute.value'),
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							)
						)
					),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.url', 'Product.manufacturer_id'),
						'Manufacturer' => array(
							'fields' => array('Manufacturer.id', 'Manufacturer.name')
						)
					),
				),
				'Shipping' => array(
					'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.tracker_prefix', 'Shipping.tracker_postfix')
				),
				'Customer' => array(
					'fields' => array('Customer.id', 'Customer.first_name', 'Customer.last_name', 'Customer.email', 'Customer.phone')
				),
				'Status' => array(
					'fields' => array('Status.id', 'Status.name', 'Status.color'),
				), 
				'Payment' => array(
					'fields' => array('Payment.id', 'Payment.name')
				),
				'Ordernote' => array(
					'fields' => array('Ordernote.id', 'Ordernote.created', 'Ordernote.note'),
					'Status' => array(
						'fields' => array('Status.id', 'Status.name')
					),
					'Administrator' => array(
						'fields' => array('Administrator.id', 'Administrator.first_name', 'Administrator.last_name')
					)
				),
				'DiscountCoupon'
			),
		));

		// pokud je zadano spatne id, nic se nenacte,
		// osetrim presmerovanim
		if (empty($order)){
			$this->Session->setFlash('Neexistující objednávka!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'), null, true);
		}

		// potrebuju vytahnout mozne statusy
		$statuses = $this->Order->Status->find('list');

		// predam data do view
		$this->set(compact(array('order', 'statuses', 'notes', 'manufacturers')));
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_stats() {
		$virtual_fields = array(
			'date' => 'CONCAT(Month(created), "/", Year(created))',
			'income' => 'SUM(shipping_cost + subtotal_with_dph)',
			'count' => 'COUNT(*)',
			'month' => 'Month(created)',
			'year' => 'Year(created)'
		);
		
		$this->Order->virtualFields = array_merge($this->Order->virtualFields, $virtual_fields);
		
		$no_storno_conditions = array('Order.status_id !=' => 5);
		
		$no_storno_orders = $this->Order->find('all', array(
			'conditions' => $no_storno_conditions,
			'contain' => array(),
			'fields' => array('Order.date', 'Order.income', 'Order.count'),
			'group' => array('Order.month', 'Order.year'),
			'order' => array('Order.year' => 'asc', 'Order.month.asc')
		));
		$this->set('no_storno_orders', $no_storno_orders);
		
		$no_storno_orders_sum = $this->Order->find('first', array(
			'conditions' => $no_storno_conditions,
			'contain' => array(),
			'fields' => array('Order.income', 'Order.count'),
		));
		$this->set('no_storno_orders_sum', $no_storno_orders_sum);
		
		$finished_orders_conditions = array('Order.status_id' => 4);
		
		$finished_orders = $this->Order->find('all', array(
			'conditions' => $finished_orders_conditions,
			'contain' => array(),
			'fields' => array('Order.date', 'Order.income', 'Order.count'),
			'group' => array('Order.month', 'Order.year'),
			'order' => array('Order.year' => 'asc', 'Order.month.asc')
		));
		$this->set('finished_orders', $finished_orders);
		
		$finished_orders_sum = $this->Order->find('first', array(
			'conditions' => $finished_orders_conditions,
			'contain' => array(),
			'fields' => array('Order.income', 'Order.count'),
		));
		$this->set('finished_orders_sum', $finished_orders_sum);
		
		unset($this->Order->virtualFields['date']);
		unset($this->Order->virtualFields['income']);
		unset($this->Order->virtualFields['count']);
		unset($this->Order->virtualFields['month']);
		unset($this->Order->virtualFields['year']);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_print($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána objednávka, kterou chcete vytisknout.');
			$this->redirect(array('action' => 'index'));
		}
		// nactu si data o objednavce
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('OrderedProduct.id', 'OrderedProduct.product_id', 'OrderedProduct.product_name', 'OrderedProduct.product_quantity', 'OrderedProduct.product_price_with_dph'),
					'OrderedProductsAttribute' => array(
						'fields' => array('OrderedProductsAttribute.id', 'OrderedProductsAttribute.ordered_product_id', 'OrderedProductsAttribute.attribute_id'),
						'Attribute' => array(
							'fields' => array('Attribute.id', 'Attribute.value'),
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							)
						)
					),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.url', 'Product.manufacturer_id'),
						'Manufacturer' => array(
							'fields' => array('Manufacturer.id', 'Manufacturer.name')
						)
					),
				),
				'Shipping' => array(
					'fields' => array('Shipping.id', 'Shipping.name', 'Shipping.tracker_prefix', 'Shipping.tracker_postfix')
				),
				'Payment' => array(
					'fields' => array('Payment.id', 'Payment.name')
				),
				'DiscountCoupon' => array(
					'fields' => array('DiscountCoupon.id', 'DiscountCoupon.value')
				)
			)
		));
		
		// pokud je zadano spatne id, nic se nenacte,
		// osetrim presmerovanim
		if (empty($order)){
			$this->Session->setFlash('Neexistující objednávka!');
			$this->redirect(array('action' => 'index'), null, true);
		}
		
		$this->set('order', $order);
		
		$this->layout = REDESIGN_PATH . 'print';
	}

	function admin_delete($id){
		$this->Order->delete($id, true);
		$this->Session->setFlash('Objednávka byla smazána!');
		$this->redirect(array('action' => 'index'), null, true);
	}

	function admin_edit(){
		// kontrola, zda jsou pro dany status vyzadovana nejake pole
		$valid_requested_fields = array();
		$requested_fields = $this->Order->Status->has_requested($this->data['Order']['status_id']);
		if ( !empty($requested_fields) ){
			// nejaka pole jsou vyzadovana, takze si to musim zkontrolovat
			$this->Order->recursive = -1;
			$order = $this->Order->read(null, $this->data['Order']['id']);
			
			foreach ( $requested_fields as $key => $value ){
				if ( empty($order['Order'][$key]) && empty($this->data['Order'][$key])  ){
					$valid_requested_fields[] = $value;
				}
			}
		}
		
		if ( empty($valid_requested_fields) ){

			// ukladani poznamky o zmene stavu
			// vytvorim si data pro poznamku o zmene objednavky
			$this->data['Ordernote']['administrator_id'] = $this->Session->read('Administrator.id');
			$this->data['Ordernote']['status_id'] = $this->data['Order']['status_id'];
			$this->data['Ordernote']['order_id'] = $this->data['Order']['id'];
	
			// osetrim, zda dochazi ke zmene cisla baliku,
			// pokud ne, unsetnu si cislo baliku
			if ( empty($this->data['Order']['shipping_number']) ){
				unset($this->data['Order']['shipping_number']);
			} else {
				$this->data['Ordernote']['note'] .= "\n" . 'přidáno číslo balíku: ' . $this->data['Order']['shipping_number'];
			}
			
			// osetrim, zda dochazi ke zmene variabilniho symbolu,
			// pokud ne, unsetnu si variablni symbol
			if ( empty($this->data['Order']['variable_symbol']) ){
				unset($this->data['Order']['variable_symbol']);
			} else {
				$this->data['Ordernote']['note'] .= "\n" . 'přidán variabilní symbol: ' . $this->data['Order']['variable_symbol'];
			}

			$this->Order->Ordernote->save($this->data, false);
				
			
			// zalozim si idecko, abych updatoval
			$this->Order->id = $this->data['Order']['id'];
			unset($this->data['Order']['id']);

			// zjistim si id stavu, ve kterem byla objednavka pred zmenou
			$prev_status_id = $this->Order->getFieldValue($this->Order->id, 'status_id');
			
			// zmena stavu			
			// ulozim bez validace
			$this->Order->save($this->data, false);

			// odeslat na mail notifikaci zakaznikovi
			// notifikaci chci poslat pouze v pripade, ze se stavy lisi
			if ($prev_status_id != $this->data['Order']['status_id']) {
				if ($this->Order->Status->change_notification($this->Order->id, $this->data['Order']['status_id'])) {
					$this->Session->setFlash('Objednávka byla změněna!', REDESIGN_PATH . 'flash_success');
				} else {
					$this->Session->setFlash('Stav objednávky ' . $id . ' byl úspěšně upraven, ale nepodařilo se odeslat informační email zákazníkovi.', REDESIGN_PATH . 'flash_success');					
				}
			} else {
				$this->Session->setFlash('Objednávka byla změněna!', REDESIGN_PATH . 'flash_success');
			}
			
			$this->redirect(array('action' => 'view', $this->Order->id), null, true);
		} else {
			$message = implode("<br />", $valid_requested_fields);
			$this->Session->setFlash('Chyba při změně statusu!<br />' . $message, REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'view', $this->Order->id), null, true);
		}
	}

	// zmenu zpusobu platby v editaci objednavky
	function admin_edit_payment($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('payment_id'));
		$this->Session->setFlash('Způsob platby byl změněn.', REDESIGN_PATH . 'flash_success');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
	}

	// zmenu zpusobu doruceni v editaci objednavky
	function admin_edit_shipping($id = null){
		$this->Order->id = $id;
		$this->Order->save($this->data, false, array('shipping_id'));
		$this->Order->reCount($id);
		$this->Session->setFlash('Způsob dopravy byl změněn.', REDESIGN_PATH . 'flash_success');
		$this->redirect(array('controller' => 'ordered_products', 'action' => 'edit', $id));
	}
	
	function admin_edit_status() {
		$data = array(
			'success' => false,
			'message' => null	
		);
		if (!isset($_POST)) {
			$data['message'] = 'Nejsou nastavena data formuláře pro změnu stavu objednávky.';
		} else {
			if (!isset($_POST['id']) || !isset($_POST['statusId']) || !isset($_POST['variableSymbol']) || !isset($_POST['shippingNumber'])) {
				$data['message'] = 'Není nastavena informace potřebná k uložení změny stavu objednávky.';
			} else {
				$id = $_POST['id'];
				$status_id = $_POST['statusId'];
				$variable_symbol = $_POST['variableSymbol'];
				$shipping_number = $_POST['shippingNumber'];
				
				
				// kontrola, zda jsou pro dany status vyzadovana nejake pole
				$valid_requested_fields = array();
				$requested_fields = $this->Order->Status->has_requested($status_id);
				if (!empty($requested_fields)){
					// nejaka pole jsou vyzadovana, takze si to musim zkontrolovat
					$order = $this->Order->find('first', array(
						'conditions' => array('Order.id' => $id),
						'contain' => array(),
					));
						
					foreach ($requested_fields as $key => $value) {
						if (empty($order['Order'][$key]) && empty($$key)) {
							$valid_requested_fields[] = $value;
						}
					}
				}
				if (empty($valid_requested_fields)) {
					$order = array(
						'Order' => array(
							'id' => $id,
							'status_id' => $status_id
						),
						'Ordernote' => array(
							0 => array(
								'administrator_id' => $this->Session->read('Administrator.id'),
								'status_id' => $status_id,
							)		
						)
					);
					
					if (!empty($variable_symbol)) {
						$order['Order']['variable_symbol'] = $variable_symbol;
						// hlaska, ze je pridan variabilni symbol
						$order['Ordernote'][0]['note'] = 'přidán variabilní symbol: ' . $variable_symbol;
					}
					
					if (!empty($shipping_number)) {
						$order['Order']['shipping_number'] = $shipping_number;
						// hlaska, ze je pridano cislo baliku
						$order['Ordernote'][0]['note'] = 'přidáno číslo balíku: ' . $shipping_number;
					}
					
					// zjistim si id stavu, ve kterem byla objednavka pred zmenou
					$prev_status_id = $this->Order->getFieldValue($id, 'status_id');

					if ($this->Order->saveAll($order)) {
						// notifikaci chci poslat pouze v pripade, ze se stavy lisi
						if ($prev_status_id != $status_id) {
							if (!$this->Order->Status->change_notification($id, $status_id)) {
								$data['message'] = 'Stav objednávky ' . $id . ' byl úspěšně upraven, ale nepodařilo se odeslat informační email zákazníkovi.';
							}
						}
						$data['success'] = true;
						$data['message'] = 'Stav objednávky ' . $id . ' byl úspěšně upraven.';
					} else {
						$data['message'] = 'Stav objednávky ' . $id . ' se nepodařilo upravit.';
					}
				} else {
					$message = implode(" ", $valid_requested_fields);
					$data['message'] = 'Chyba při změně statusu! ' . $message;
				}
			}
		}
		echo json_encode($data);
		die();
	}
	
	/**
	 * Kontroluje stavy nedorucenych objednavek podle dopravcu.
	 *
	 */
	function admin_track(){
		$this->Order->recursive = -1;
		
		$orders = $this->Order->find('all',
			array('conditions' => array(
					// nekontroluju terminalni stavy objednavek (closed == true)
					'Status.closed' => false,
 					"Order.shipping_number != ''",
					//'id' => 3914
				),
				'contain' => array('Status', 'Shipping'),
				'fields' => array('Order.id', 'Shipping.heureka_id'),
			)
		);

		$bad_orders = array();
		foreach( $orders as $order ){
			// rozlisit zpusob doruceni
			switch ( $order['Shipping']['heureka_id'] ){
				case "CESKA_POSTA":
					// ceska posta
					$result = $this->Order->track_cpost($order['Order']['id']);
					break;
				break;
				case "GEIS":
					// general parcel
					$result = $this->Order->track_gparcel($order['Order']['id']);
					break;
				break;
				case "DPD":
					// DPD
					$result = $this->Order->track_dpd($order['Order']['id']);
					break;
				case "PPL":
					// PPL
					$result = $this->Order->track_ppl($order['Order']['id']);
					break;
				default:
					$result = $order['Order']['id'];
				break;
			}
			
			if ( $result !== true ){
				$bad_orders[] = $result;
			}
		}
		
		$this->set('bad_orders', $bad_orders);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_pohoda_view() {
		$orders_to_export = array();
		$backtrace_url = '/admin/orders';
		$flash_messages = array();
		$orders = array();
		if (isset($this->data)) {

			// je nastavena adresa, na kterou se bude po zpracovani pozadavku presmerovavat
			if (isset($this->data['Order']['backtrace_url'])) {
				$backtrace_url = $this->data['Order']['backtrace_url'];
			}
			// hledam objednavky, ktere jsem chtel exportovat
			foreach ($this->data['Order'] as $order_id => $export) {
				if (isset($export['export']) && $export['export'] && is_int($order_id)) {
					// mam opravdu v systemu objednavky s timto ideckem
					if ($this->Order->hasAny(array('Order.id' => $order_id))) {
						// objednavka je jiz fakturovana
						if ($this->Order->hasAny(array('Order.id' => $order_id, 'Order.invoice' => false))) {
							// zapamatuju si objednavku, kterou chci fakturovat
							$orders[] = $order_id;
						} else {
							$flash_messages[] = 'Objednávka č. ' . $order_id . ' je již vyfakturovaná, není součástí exportu.';
						}
					} else {
						$flash_messages[] = 'Objednávka č. ' . $order_id . ' není v systému, není součástí exportu.';
					}
				}
			}

			if (empty($orders)) {
				$flash_messages[] = 'Žádná objednávka není k fakturaci, export nebyl vytvořen.';
			} else {

				$file_name = $this->Order->create_pohoda_file($orders);

				if ($file_name !== false) {
					if (!$this->Order->set_attribute($orders, 'invoice', true)) {
						$flash_messages[] = 'Nepodařilo se uložit informaci o tom, že objednávky byly vyfakturovány.';
					}
				}

				// objednavky, ktere jsem vyexportoval do xml a byly neprijate, dam do stavu prijate
				$unreceived_orders = $this->Order->find('all', array(
					'conditions' => array('Order.id' => $orders, 'Order.status_id' => 1),
					'contain' => array(),
					'fields' => array('Order.id', 'Order.comments')	
				));
				$unreceived_orders = Set::extract('/Order/id', $unreceived_orders);

				if (!$this->Order->set_attribute($orders, 'status_id', 2)) {
					$flash_messages[] = 'Nepodařilo se uložit informaci o tom, že objednávky byly vyfakturovány.';
				} else {
					foreach ($unreceived_orders as $unreceived_order) {
						$this->Order->Status->change_notification($unreceived_order, 2);
					}
				}

				$flash_messages[] = 'Export objednávek do účetního systému Pohoda naleznete <a href="/admin/orders/pohoda_download/' . urlencode($file_name) . '">zde</a>.';
			}
		} else {
			$flash_messages[] = 'Není zadáno, které objednávky chcete exportovat';
		}
			
		$flash_messages = implode('<br/>', $flash_messages);
		$this->Session->setFlash($flash_messages, REDESIGN_PATH . 'flash_failure');
		$this->redirect($backtrace_url);
	}
	
	function admin_pohoda_download($file_name = null) {
		if ($file_name) {
			header('Content-Type: text/xml');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="' . basename(DS . POHODA_EXPORT_DIR . DS . $file_name) . '"');
			readfile(POHODA_EXPORT_DIR . DS . $file_name); // do the double-download-dance (dirty but worky)
			die();
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function admin_eform_download($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno ID objednávky, u které chcete stáhnout eform.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->Order->hasAny(array('Order.id' => $id))) {
			$this->Session->setFlash('Objednávka, ke které chcete stánout eform, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		header('Content-Type: text/xml');
		header('Content-Transfer-Encoding: Binary');
		header('Content-disposition: attachment; filename="' . basename('pohodaorder.xph') . '"');
		readfile('http://www.' . CUST_ROOT . '/orders/eform/' . $id); // do the double-download-dance (dirty but worky)
		die();
	}
	
	function geis($ids = null) {
		if (!empty($ids) && $ids != base64_encode(serialize(array()))) {
			$ids = unserialize(base64_decode($ids));
			$orders = array();
			foreach ($ids as $id) {
				$order = $this->Order->find('first', array(
					'conditions' => array('Order.id' => $id, 'Shipping.provider_name' => 'Geis'),
					'contain' => array('Shipping'),
					'fields' => array('Order.*')
				));
				
				if (!empty($order)) {
					$orders[] = $order;
				}
			}

			$this->set('orders', $orders);
			$this->layout = 'empty';
			$this->set('geis_point_shipping_ids', json_decode(GEIS_POINT_SHIPPING_IDS));
		}
	}
	
	function admin_geis_export() {
		$backtrace_url = array('controller' => 'orders', 'action' => 'index');
		$flash_messages = array();
		$flash_element = 'flash_failure';
		
		if (isset($this->data)) {
			// je nastavena adresa, na kterou se bude po zpracovani pozadavku presmerovavat
			if (isset($this->data['Order']['backtrace_url'])) {
				$backtrace_url = $this->data['Order']['backtrace_url'];
			}
			// hledam objednavky, ktere jsem chtel exportovat
			$export_ids = array();
			foreach ($this->data['Order'] as $order_id => $export) {
				if (isset($export['export']) && $export['export'] && is_int($order_id)) {
					// mam opravdu v systemu objednavky s timto ideckem
					if ($this->Order->hasAny(array('Order.id' => $order_id))) {
						// pridam ji do exportu
						$export_ids[] = $order_id;
					} else {
						$flash_messages[] = 'Objednávka č. ' . $order_id . ' není v systému nebo ji nedopravuje společnost Geis, není součástí exportu.';
					}
				}
			}
			
			$url = 'http://' . $_SERVER['HTTP_HOST'] . '/orders/geis/' . base64_encode(serialize($export_ids));
			$content = download_url($url);
			
			header('Content-Type: text/xml');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="' . basename('geis.xml') . '"');
			echo $content;
			
			die();
		}

		if (empty($flash_messages)) {
			$flash_messages = array(0 => 'Objednávky byly exportovány');
			$flash_element = 'flash_success';
		}
		$flash_messages = implode('<br/>', $flash_messages);
		$this->Session->setFlash($flash_messages, REDESIGN_PATH . $flash_element);
		$this->redirect($backtrace_url);
	}
	
	function cpost($ids = null) {
		if (!empty($ids) && $ids != base64_encode(serialize(array()))) {
			$ids = unserialize(base64_decode($ids));
			$orders = array();
			foreach ($ids as $id) {
				$order = $this->Order->find('first', array(
					'conditions' => array('Order.id' => $id, 'Shipping.provider_name' => 'Česká pošta'),
					'contain' => array('Shipping'),
					'fields' => array('Order.*')
				));
				
				if (!empty($order)) {
					$orders[] = $order;
				}
			}

			$this->set('orders', $orders);
			$this->layout = 'empty';
		}
	}
	
	function admin_cpost_export() {
		$backtrace_url = array('controller' => 'orders', 'action' => 'index');
		$flash_messages = array();
		$flash_element = 'flash_failure';
		
		if (isset($this->data)) {
			// je nastavena adresa, na kterou se bude po zpracovani pozadavku presmerovavat
			if (isset($this->data['Order']['backtrace_url'])) {
				$backtrace_url = $this->data['Order']['backtrace_url'];
			}
			// hledam objednavky, ktere jsem chtel exportovat
			$export_ids = array();
			foreach ($this->data['Order'] as $order_id => $export) {
				if (isset($export['export']) && $export['export'] && is_int($order_id)) {
					// mam opravdu v systemu objednavky s timto ideckem
					if ($this->Order->hasAny(array('Order.id' => $order_id))) {
						// pridam ji do exportu
						$export_ids[] = $order_id;
					} else {
						$flash_messages[] = 'Objednávka č. ' . $order_id . ' není v systému nebo ji nedopravuje společnost Česká Pošta, není součástí exportu.';
					}
				}
			}
				
			$url = 'http://' . $_SERVER['HTTP_HOST'] . '/orders/cpost/' . base64_encode(serialize($export_ids));

			$content = download_url($url);
				
			header('Content-Type: text/csv');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="' . basename('cpost.csv') . '"');
			echo $content;
				
			die();
		}
		
		if (empty($flash_messages)) {
			$flash_messages = array(0 => 'Objednávky byly exportovány');
			$flash_element = 'flash_success';
		}
		$flash_messages = implode('<br/>', $flash_messages);
		$this->Session->setFlash($flash_messages, REDESIGN_PATH . $flash_element);
		$this->redirect($backtrace_url);
	}
	
	function admin_notify_admin($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno ID objednávky, u které chcete odeslat email.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->Order->hasAny(array('Order.id' => $id))) {
			$this->Session->setFlash('Objednávka, ke které chcete odeslat email, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->Order->notifyAdmin($id);
		$this->Session->setFlash('Informace o nové objednávce byla odeslána', REDESIGN_PATH . 'flash_success');
		$this->redirect(array('action' => 'index'));
	}
	
	function eform($id = null) {
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('OrderedProduct.id', 'OrderedProduct.product_name', 'OrderedProduct.product_quantity', 'OrderedProduct.product_price_with_dph'),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.tax_class_id', 'Product.pohoda_id'),
						'Manufacturer' => array(
							'fields' => array('Manufacturer.id', 'Manufacturer.name')
						)
					),
					'OrderedProductsAttribute' => array(
						'fields' => array('OrderedProductsAttribute.id'),
						'Attribute' => array(
							'fields' => array('Attribute.id', 'Attribute.value'),
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							)
						)
					)
				),
				'Shipping' => array(
					'fields' => array('Shipping.id', 'Shipping.name')
				),
				'DiscountCoupon' => array(
					'fields' => array('DiscountCoupon.id', 'DiscountCoupon.value')
				)
			),
			'joins' => array(
				array(
					'table' => 'payments',
					'alias' => 'Payment',
					'type' => 'LEFT',
					'conditions' => array('Order.payment_id = Payment.id')
				),
				array(
					'table' => 'payment_types',
					'alias' => 'PaymentType',
					'type' => 'LEFT',
					'conditions' => array('Payment.payment_type_id = PaymentType.id')
				)
			),
			'fields' => array(
				'Order.*',
		
				'Payment.id',
				'Payment.name',
					
				'PaymentType.id',
				'PaymentType.name'
			),
		));

		$date = explode(' ', $order['Order']['created']);
		$order['Order']['date'] = $date[0];
		
		$this->set('order', $order);
		$this->layout = REDESIGN_PATH . 'pohoda';
	}
	
	function one_step_order() {
		App::import('Model', 'Cart');
		$this->Order->Cart = &new Cart;
	
		$cart_id = $this->Order->Cart->get_id();
		// vytahnu si vsechny produkty, ktere patri
		// do zakaznikova kose
		$cart_products = $this->Order->Cart->CartsProduct->find('all', array(
			'conditions' => array('CartsProduct.cart_id' => $cart_id),
			'contain' => array(
				'Product' => array(
					'Image' => array(
						'conditions' => array('Image.is_main' => true)
					),
					'fields' => array(
						'Product.id',
						'Product.name',
						'Product.url'
					)
				)
			)
		));

		// podivam se, jestli je zakaznik prihlaseny
		if ($this->Session->check('Customer.id')) {
			// pokud ano, predvyplnim formular jeho udaji
			$customer = $this->Order->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->Session->read('Customer.id')),
				'contain' => array(
					'Address' => array(
						'order' => array('FIELD(Address.type, "d", "f")')
					)
				)
			));
		}

		if (isset($this->data)) {
			if (isset($this->data['Order']['action'])) {
				switch ($this->data['Order']['action']) {
					// upravuju obsah kosiku
					case 'cart_edit':
						// najdu si produkt a upravim ho
						$cart_product = $this->Order->Cart->CartsProduct->find('first', array(
						'conditions' => array(
						'CartsProduct.id' => $this->data['CartsProduct']['id'],
						'CartsProduct.cart_id' => $cart_id
						),
						'contain' => array(),
						'fields' => array('CartsProduct.id')
						));
						if (!empty($cart_product)) {
							$cart_product['CartsProduct']['quantity'] = $this->data['CartsProduct']['quantity'];
							// nastavil jsem nulove mnozstvi, smazu produkt z kosiku
							if ($cart_product['CartsProduct']['quantity'] == 0) {
								if ($this->Order->Cart->CartsProduct->delete($this->data['CartsProduct']['id'])) {
									$this->Session->setFlash('Zboží bylo z košíku odstraněno.', REDESIGN_PATH . 'flash_success', array('type' => 'shopping_cart'));
								} else {
									$this->Session->setFlash('Zboží se nepodařilo z košíku odstranit, zkuste to prosím ještě jednou', REDESIGN_PATH . 'flash_failure', array('type' => 'shopping_cart'));
								}
							} else {
								if ($this->Order->Cart->CartsProduct->save($cart_product)) {
									$this->Session->setFlash('Množství zboží bylo upraveno', REDESIGN_PATH . 'flash_success', array('type' => 'shopping_cart'));
								} else {
									$this->Session->setFlash('Nepodařilo se upravit množství zboží v košíku, zkuste to prosím ještě jednou.', REDESIGN_PATH . 'flash_failure', array('type' => 'shopping_cart'));
								}
							}
						} else {
							$this->Session->setFlash('Košík daný produkt neobsahuje, nelze jej proto vymazat.', REDESIGN_PATH . 'flash_failure', array('type' => 'shopping_cart'));
						}
						$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order', '#ShoppingCart'));
						break;
					case 'customer_login':
						$conditions = array(
						'CustomerLogin.login' => $this->data['Customer']['login'],
						'CustomerLogin.password' => md5($this->data['Customer']['password']),
						'Customer.active' => true
						);
	
						// pokus o zalogovani podle SNV - existuje v SNV pro dane prihlasovaci udaje zakaznik?
						$customer = $this->Order->Customer->CustomerLogin->find('first', array(
							'conditions' => $conditions,
							'contain' => array('Customer'),
						));
							
						if (empty($customer)) {
							$this->Session->setFlash('Neplatný login nebo heslo!', REDESIGN_PATH . 'flash_failure', array('type' => 'customer_login'));
							$this->data['Customer']['is_registered'] = 1;
						} else {
							// ulozim si info o zakaznikovi do session
							$this->Session->write('Customer', $customer['Customer']);
							
							// podivam se, jestli mam u sledovace sparovane zarizeni s danym zakaznikem
							if ($key = $this->Order->Customer->TSCustomerDevice->getKey($this->Cookie, $this->Session)) {
								$this->Order->Customer->TSCustomerDevice->setCustomerId($customer['Customer']['id']);
							}
								
							// ze session odstranim data o objednavce,
							// pokud se snazil zakaznik pred prihlasenim neco
							// vyplnovat v objednavce, delalo by mi to bordel
							$this->Session->delete('Order');
								
							// na pocitadle si inkrementuju pocet prihlaseni
							$customer_update = array(
								'Customer' => array(
									'id' => $customer['Customer']['id'],
									'login_count' => $customer['Customer']['login_count'] + 1,
									'login_date' => date('Y-m-d H:i:s')
								)
							);
							$this->Order->Customer->save($customer_update);
								
							// presmeruju
							$this->Session->setFlash('Jste přihlášen(a) jako ' . $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'] . '.', REDESIGN_PATH . 'flash_success', array('type' => 'customer_login'));
							$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
						}
	
						break;
					case 'order_finish':
						$shipping_id = $this->data['Order']['shipping_id'];
						// nechci kontrolovat, jestli je zakaznikuv email unikatni (aby i zakaznik, ktery neni prihlaseny, ale jeho email je v systemu, mohl dokoncit objednavku
						if (isset($this->data['Customer']['id']) && empty($this->data['Customer']['id'])) {
							unset($this->data['Customer']['id']);
						}
	
						// jsou data o zakaznikovi validni?
						unset($this->Order->Customer->validate['email']['isUnique']);
	
						$address_data = null;
						// pokud je zvolena doprava osobnim odberem
						if ($shipping_id == PERSONAL_PURCHASE_SHIPPING_ID) {
							// nechci adresy
							unset($this->data['Address']);
						// doprava na geis point
						} elseif ($this->Order->Shipping->isGeisPoint($shipping_id)) {
							// nemam dorucovaci adresu, ale jen fakturacni
							unset($this->data['Address'][0]);
							// fakturacni
							if (empty($this->data['Address'][1]['name'])) {
								$this->data['Address'][1]['name'] = full_name($this->data['Customer']['first_name'], $this->data['Customer']['last_name']);
							}
							$address_data = $this->data['Address'];
						// jinak
						} else {
							// dorucovaci
							if (empty($this->data['Address'][0]['name'])) {
								$this->data['Address'][0]['name'] = full_name($this->data['Customer']['first_name'], $this->data['Customer']['last_name']);
							}
							// fakturacni
							if (empty($this->data['Address'][1]['name'])) {
								$this->data['Address'][1]['name'] = full_name($this->data['Customer']['first_name'], $this->data['Customer']['last_name']);
							}
							// pokud mam zadano, ze dodaci adresa je shodna s fakturacni, nakopiruju hodnoty
							if (!$this->data['Customer']['is_delivery_address_different']) {
								$this->data['Address'][1]['name'] = $this->data['Address'][0]['name'];
								$this->data['Address'][1]['contact_first_name'] = $this->data['Address'][0]['contact_first_name'];
								$this->data['Address'][1]['contact_last_name'] = $this->data['Address'][0]['contact_last_name'];
								$this->data['Address'][1]['street'] = $this->data['Address'][0]['street'];
								$this->data['Address'][1]['street_no'] = $this->data['Address'][0]['street_no'];
								$this->data['Address'][1]['city'] = $this->data['Address'][0]['city'];
								$this->data['Address'][1]['zip'] = $this->data['Address'][0]['zip'];
								$this->data['Address'][1]['state'] = $this->data['Address'][0]['state'];
							}
							
							$address_data = $this->data['Address'];
						}
						$customer_data['Customer'] = $this->data['Customer'];
						if ($address_data) {
							$customer_data['Address'] = $address_data;
						}

						// jestlize jsou data o zakaznikovy validni
						if ($this->Order->Customer->saveAll($customer_data, array('validate' => 'only'))) {
							// jestli neni zakaznik prihlaseny a zaroven existuje zakaznik se zadanou emailovou adresou
							if (!$this->Session->check('Customer.id')) {
								$customer = $this->Order->Customer->find('first', array(
									'conditions' => array('Customer.email' => $this->data['Customer']['email']),
									'contain' => array(),
									'fields' => array('Customer.id')
								));
								// pokud existuje, priradim k objednavce zakaznikovo idcko (at nezakladam noveho a nevznikaji mi ucty s duplicitnim emailem
								if (!empty($customer)) {
									$this->data['Customer']['id'] = $customer['Customer']['id'];
									// podivam se, jestli mam u sledovace sparovane zarizeni s danym zakaznikem
									if ($key = $this->Order->Customer->TSCustomerDevice->getKey($this->Cookie, $this->Session)) {
										$this->Order->Customer->TSCustomerDevice->setCustomerId($customer['Customer']['id']);
									}
								}
								
								// pamatuju si, ze zakaznik neni prihlaseny v objednavce (protoze to vsude testuju z historickych duvodu
								// pres customer id v sesne a to je mi ted na nic
								$this->data['Customer']['noreg'] = true;
							}

							$this->Session->write('Customer', $this->data['Customer']);
							if (isset($this->data['Address'][0])) {
								$this->Session->write('Address', $this->data['Address'][0]);
							}
							if (isset($this->data['Address'][1])) {
								$this->Session->write('Address_payment', $this->data['Address'][1]);
							}
							
							$couponUse = false;
							if (isset($this->data['DiscountCoupon']['name']) && !empty($this->data['DiscountCoupon']['name'])) {
								$couponUse = true;
								$couponCheck = false;
								if ($couponId = $this->Order->DiscountCoupon->getIdByField($this->data['DiscountCoupon']['name'], 'name')) {
									$customerId = null;
									if (isset($this->data['Customer']['id'])) {
										$customerId = $this->data['Customer']['id'];
									}
									$couponCheck = $this->Order->DiscountCoupon->checkCart($couponId, $customerId);
								}
							}
							
							if (!$couponUse || ($couponUse && $couponCheck)) {
								$this->data['Order']['payment_id'] =  $this->Order->Shipping->get_payment_id($this->data['Order']['shipping_id']);
								$this->Session->write('Order', $this->data['Order']);
								if ($couponUse) {
									$this->Session->write('Order.discount_coupon_id', $couponId);
								}
								// pokud je jako zpusob dopravy vybrano Geis Point (doruceni na odberne misto), presmeruju na plugin pro vyber odberneho
								// mista s tim, aby se po navratu presmeroval na ulozeni informaci o vyberu odberneho mista
								if ($this->Order->Shipping->isGeisPoint($shipping_id)) {
									if ($service_url = $this->Order->Shipping->geis_point_url($this->Session, true)) {
										$this->redirect($service_url);
									} else {
										$this->Session->setFlash('Zadejte prosím Vaši doručovací adresu');
										$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order', '#' => 'OrderDetailsCustomer'));
									}
								}
								
								// presmeruju do finalizace objednavky, kde se data ulozena v sesne ulozi do systemu
								$this->redirect(array('controller' => 'orders', 'action' => 'finalize'));
							} else {
								if ($this->Session->check('Customer.noreg')) {
									$this->Session->delete('Customer.id');
									$this->Session->delete('Customer.noreg');
									unset($this->data['Customer']['id']);
								}
								if ($couponUse) {
									$this->Session->setFlash($this->Order->DiscountCoupon->checkError, REDESIGN_PATH . 'flash_failure', array('type' => 'customer_info'));
								}
							}
						} else {
							// pokud jsem nakopiroval dorucovaci adresu pred ulozenim, protoze zakaznik nerekl, ze je jina, nez fakturacni, tak ji zase vynuluju
							if (!$this->data['Customer']['is_delivery_address_different']) {
								unset($this->data['Address'][1]);
							}
							$this->Session->setFlash('Údaje o zákazníkovi obsahují chybu, opravte ji prosím a formulář uložte znovu.', REDESIGN_PATH . 'flash_failure', array('type' => 'customer_info'));
						}
	
						break;
				}
			}
		} else {
			if (isset($customer)) {
				$this->data = $customer;
			}
			$this->data['Customer']['is_registered'] = 0;
			
			// pokud se lisi adresy, zobrazim element pro fakturacni adresu
			if (
				isset($customer) && isset($customer['Address'][1]) && (
					$customer['Address'][0]['name'] != $customer['Address'][1]['name'] ||
					$customer['Address'][0]['street'] != $customer['Address'][1]['street'] ||
					$customer['Address'][0]['street_no'] != $customer['Address'][1]['street_no'] ||
					$customer['Address'][0]['city'] != $customer['Address'][1]['city'] ||
					$customer['Address'][0]['zip'] != $customer['Address'][1]['zip']
				)
			) {
				$this->data['Customer']['is_delivery_address_different'] = true;
			}
		}
	
		// data o zbozi v kosiku
		foreach ($cart_products as $index => $cart_product) {
			// u produktu si pridam jmenne atributy
			// chci tam dostat pole napr (barva -> bila, velikost -> S) ... takze (option_name -> value)
			// pokud znam id subproduktu, tak ma produkt varianty a muzu si je jednoduse vytahnout
			$cart_products[$index]['CartsProduct']['product_attributes'] = array();
			if (!empty($cart_product['CartsProduct']['subproduct_id'])) {
				$subproduct = $this->Order->Cart->CartsProduct->Product->Subproduct->find('first', array(
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
				if (!empty($subproduct)) {
					foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
						$product_attributes[$attributes_subproduct['Attribute']['Option']['name']] = $attributes_subproduct['Attribute']['value'];
					}
				}
				$cart_products[$index]['CartsProduct']['product_attributes'] = $product_attributes;
			}
		}
		$this->set('cart_products', $cart_products);
	
		// data pro volbu dopravy a platby
		$providers = $this->Order->Shipping->find('all', array(
			'conditions' => array('Shipping.active'),
			'contain' => array(),
			'fields' => array('DISTINCT Shipping.provider_name'),
			'order' => array('Shipping.order' => 'asc')
		));
		
		foreach ($providers as &$provider) {
			$provider['shippings'] = $this->Order->Shipping->find('all', array(
				'conditions' => array('Shipping.active' => true, 'Shipping.provider_name' => $provider['Shipping']['provider_name']),
				'contain' => array(),
				'order' => array('Shipping.order' => 'asc'),
			));
			foreach ($provider['shippings'] as &$shipping) {
				$shipping['Shipping']['price'] = $this->Order->get_shipping_cost($shipping['Shipping']['id']);
				if (isset($shipping['Shipping']['note']) && !empty($shipping['Shipping']['note'])) {
					$shipping['Shipping']['name'] .= ' (' . $shipping['Shipping']['note'] . ')';
				}
			}
		}
		
		$this->set(compact('providers'));
	
		if (!isset($this->data['Order']['payment_id']) && $this->Session->check('Order.payment_id')) {
			$this->data['Order']['payment_id'] = $this->Session->read('Order.payment_id');
		}
	
		if (!isset($this->data['Order']['shipping_id']) && $this->Session->check('Order.shipping_id')) {
			$this->data['Order']['shipping_id'] = $this->Session->read('Order.shipping_id');
		}
	
		if (!isset($this->data['Order']['comments']) && $this->Session->check('Order.comments')) {
			$this->data['Order']['comments'] = $this->Session->read('Order.comments');
		}
		
		// souvisejici produkty k tem, co ma v kosiku
		$customerTypeId = 2;
		if ($this->Session->check('Customer.customer_type_id')) {
			$customerTypeId = $this->Session->read('Customer.customer_type_id');
		}
		$similarProductIds = $this->Order->Cart->similarProductIds(null, $customerTypeId);
		$similarProducts = $this->Order->Cart->similarProducts($similarProductIds, $customerTypeId);
		$this->set('similarProducts', $similarProducts);
		
		// nastavim si titulek stranky
		$this->set('page_heading', 'Objednávka');
		$this->set('_title', 'Objednávka');
		$this->set('_description', 'Objednávka');
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Objednávka', 'href' => '/objednavka')
		);
		$this->set('breadcrumbs', $breadcrumbs);
	
		// layout
		$this->layout = REDESIGN_PATH . 'homepage';
	
	}

	function finalize() {
		if (!$this->Session->check('Order.shipping_id')) {
			$this->Session->setFlash('Není zvolena doprava pro Vaši objednávku', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
		}
		
		$sess_customer = $this->Session->read('Customer');
		$customer['Customer'] = $sess_customer;

		$order = $this->Session->read('Order');
		
		$shipping_id = $order['shipping_id'];
		// pokud mam zvoleno dodani na vydejni misto geis point, nactu parametry pro doruceni (z GET nebo sesny)
		if ($this->Order->Shipping->isGeisPoint($shipping_id)) {
			// parametry jsou v GET
			if (isset($this->params['url']['GPName']) && isset($this->params['url']['GPAddress']) && isset($this->params['url']['GPID'])) {
				$gp_name = urldecode($this->params['url']['GPName']);
				$gp_address = urldecode($this->params['url']['GPAddress']);
				$gp_address = explode(';', $gp_address);
				$gp_street = '';
				if (isset($gp_address[0])) {
					$gp_street = $gp_address[0];
				}
				$gp_city = '';
				if (isset($gp_address[1])) {
					$gp_city = $gp_address[1];
				}
				$gp_zip = '';
				if (isset($gp_address[2])) {
					$gp_zip = $gp_address[2];
				}
				$gp_id = urldecode($this->params['url']['GPID']);
				// ulozim do sesny jako dorucovaci adresu
				$this->Session->write('Address.name', $gp_name . ', ' . $gp_id);
				$this->Session->write('Address.street', $gp_street);
				$this->Session->write('Address.street_no', '');
				$this->Session->write('Address.city', $gp_city);
				$this->Session->write('Address.zip', $gp_zip);
				$this->Session->write('Address.state', 'Česká republika');
				// poznacim si, ze adresa je vybrana pomoci pluginu
				$this->Session->write('Address.plugin_check', true);
			} elseif (!$this->Session->check('Address') || !$this->Session->check('Address.plugin_check') || !$this->Session->read('Address.plugin_check')) {
				// nemam data pro vydejni misto ani v sesne ani v GET, ale potrebuju je, takze presmeruju znova na plugin
				// pro vyber vydejniho mista a z nej se sem vratim
				if ($service_url = $this->Order->Shipping->geis_point_url($this->Session)) {
					$this->redirect($service_url);
				} else {
					$this->Session->setFlash('Zadejte prosím Vaši doručovací adresu.');
					$this->redirect(array('controller' => 'customers', 'action' => 'order_personal_info'));
				}
			}
		}

		// pridam adresy
		if ($this->Session->check('Address')) {
			$customer['Address'][] = $this->Session->read('Address');
		}
		if ($this->Session->check('Address_payment')) {
			$customer['Address'][] = $this->Session->read('Address_payment');
		}

		// jedna se o neprihlaseneho a nezaregistrovaneho zakaznika
		if (!isset($customer['Customer']['id']) || empty($customer['Customer']['id'])) {
			// musim vytvorit novy zakaznicky ucet, takze vygeneruju login a heslo
			$customer['CustomerLogin'][0]['login'] = $this->Order->Customer->generateLogin($sess_customer);
			$customer_password = $this->Order->Customer->generatePassword($sess_customer);
			$customer['CustomerLogin'][0]['password'] = md5($customer_password);
			$customer['Customer']['confirmed'] = 1;
			$customer['Customer']['registration_source'] = 'eshop';
			$customer['Customer']['customer_type_id'] = 1;
			
			$c_dataSource = $this->Order->Customer->getDataSource();
			$c_dataSource->begin($this->Order->Customer);
			try {
				$this->Order->Customer->saveAll($customer);
			} catch (Exception $e) {
				$c_dataSource->rollback($this->Order->Customer);
				$this->Session->setFlash('Nepodařilo se uložit data o zákazníkovi, zopakujte prosím dokončení objednávky.');
				$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
			}
			$c_dataSource->commit($this->Order->Customer);
			
			// naparuju uzivatelske zarizeni na noveho zakaznika
			if ($key = $this->Order->Customer->TSCustomerDevice->getKey($this->Cookie, $this->Session)) {
				$this->Order->Customer->TSCustomerDevice->setCustomerId($this->Order->Customer->id);
			}
			
			// jedna se o nove zalozeny zakaznicky ucet, takze mu poslu notifikaci, pokud pri registraci uvedl svou emailovou adresu
			$customer['CustomerLogin'][0]['password'] = $customer_password;
			$this->Session->write('cpass', $customer_password);
			$this->Session->write('login', $customer['CustomerLogin'][0]['login']);
			$this->Order->Customer->notify_account_created($customer);
			$customer['Customer']['id'] = $this->Order->Customer->id;
//			$this->Session->write('Customer.id', $customer['Customer']['id']);
			$this->Session->delete('Customer.noreg');
			
		}

		//data pro objednavku
		$order = $this->Order->build($customer, $this->Cookie);

		if ($order === false) {
			$this->Session->setFlash('Objednávku se nepodařilo uložit, máte správně zadané adresy?', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
		}

		if (empty($order[1])) {
			$this->Session->setFlash('Vaše objednávka neobsahuje žádné produkty. Pravděpodobně byl Váš prohlížeč delší dobu nečinný.<br/>Prosím vložte produkty znovu do košíku a dokončete objednávku.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'orders', 'action' => 'one_step_order'));
		}

		$dataSource = $this->Order->getDataSource();
		$dataSource->begin($this->Order);
		try {
			if (!$this->Order->save($order[0])) {
				$dataSource->rollback($this->Order);
				debug($this->Order->validationErrors);
				trigger_error('Objednávku se nepodařilo uložit', E_USER_ERROR);
				die();
			}
			// musim ulozit objednavku a smazat produkty z kosiku
			foreach ($order[1] as $ordered_product) {
				$ordered_product['OrderedProduct']['order_id'] = $this->Order->id;
				if (!$this->Order->OrderedProduct->saveAll($ordered_product)) {
					$dataSource->rollback($this->Order);
					debug($this->Order->OrderedProduct->validationErrors);
					trigger_error('Produkty na objednavce se nepodařilo uložit', E_USER_ERROR);
					die();
				}
			}
			if (isset($order[0]['Order']['discount_coupon_id'])) {
				$discountCouponSave = array(
					'DiscountCoupon' => array(
						'id' => $order[0]['Order']['discount_coupon_id'],
						'order_id' => $this->Order->id
					)
				);
				if (!$this->Order->DiscountCoupon->save($discountCouponSave)) {
					$dataSource->rollback($this->Order);
					debug($this->Order->OrderedProduct->validationErrors);
					trigger_error('Produkty na objednavce se nepodařilo uložit', E_USER_ERROR);
					die();
				}
			}
		} catch (Exception $e) {
			$dataSource->rollback($this->Order);
			$this->Session->setFlash('Uložení objednávky se nepodařilo, zopakujte prosím znovu dokončení objednávky.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'orders', 'action' => 'recapitulation'));
		}
		$this->Order->cleanCartsProducts();
		$dataSource->commit($this->Order);
		
		$this->Order->notifyCustomer($customer['Customer']);
		$this->Order->Customer->SimilarProductsAdMail->sendBatch(true, false, $this->Order->id);

		$this->Order->notifyAdmin();

		// uklidim promenne
		$this->Session->delete('Order');
		
		// potrebuju na dekovaci strance vedet cislo objednavky
		$this->Session->write('Order.id', $this->Order->id);

		// nastavim hlasku a presmeruju
		$this->Session->setFlash('Vaše objednávka byla úspešně uložena!', REDESIGN_PATH . 'flash_success');

		$this->redirect(array('action' => 'finished'), null, true);
	} // konec funkce
	
	function finished() {
		$id = $this->Session->read('Order.id');
		if ( empty($id) ){
			$this->redirect(array('controller' => 'carts_products', 'action' => 'index'), null, true);
		}

		if (!$this->Session->check('Customer.id') || ($this->Session->check('Customer.id') && $this->Session->check('Customer.noreg'))) {
			// tenhle zaznam mazu jen kdyz se jedna o neprihlaseneho
			$this->Session->delete('Customer');
		}
		// smazu zaznamy o objednavce ze session
		$pass = $this->Session->read('cpass');
		$login = $this->Session->read('login');
		$this->Session->delete('Order');
		$this->Session->delete('Address');
		$this->Session->delete('Address_payment');
		$this->Session->delete('cpass');
		$this->Session->delete('login');
				
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'content';
		$this->set('_title', 'Potvrzení objednávky');
		$this->set('_description', 'Potvrzení odeslání objednávky do systému ' . CUST_NAME);
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Potvrzení objednávky', 'href' => '/orders/finished')

		);
		$this->set('breadcrumbs', $breadcrumbs);

		// nastavim si pro menu zakladni idecko
		$this->set('opened_category_id', ROOT_CATEGORY_ID);

		// nastavim nadpis stranky
		$this->set('page_heading', 'Objednávka byla dokončena');
		
		$conditions = array(
			'Order.id' => $id
		);
		
		$contain = array(
			'OrderedProduct' => array(
				'fields' => array(
					'id', 'product_id', 'product_price_with_dph', 'product_quantity'
				),
				'OrderedProductsAttribute' => array(
					'Attribute' => array(
						'Option'
					)
				),
				'Product' => array(
					'fields' => array(
						'id', 'name', 'tax_class_id'
					),
					'TaxClass' => array(
						'fields' => array(
							'id', 'value'
						)
					)
				)
			),
			'Payment'
		);
		
		$fields = array('id', 'subtotal_with_dph', 'shipping_cost', 'customer_city', 'customer_state', 'customer_email');
		
		$order = $this->Order->find('first', array(
			'conditions' => $conditions,
			'contain' => $contain,
			'fields' => $fields
		));

		$jscript_code = '';
		// celkova dan vsech produktu v objednavce
		$tax_value = 0;
		
		// heureka overeno zakazniky
		App::import('Vendor', 'HeurekaOvereno', array('file' => 'HeurekaOvereno.php'));
		try {
			$overeno = new HeurekaOvereno('f4ce59a7e0b67e0468b557653c8db5b2');
			$overeno->setEmail($order['Order']['customer_email']);
			foreach ($order['OrderedProduct'] as $op) {
				$overeno->addProductItemId($op['Product']['id']);
				$overeno->addProduct($op['Product']['name']);
			}
			$overeno->addOrderId($order['Order']['id']);
			$overeno->send();
		} catch (Exception $e) {}

		foreach ( $order['OrderedProduct'] as $op ){
			$sku = $op['Product']['id'];
			$variations = '';
			
			// dan pro konkretni produkt
			$p_tax_value = $op['product_price_with_dph'] - (round($op['product_price_with_dph'] / (1 + ($op['Product']['TaxClass']['value'] / 100)), 0));

			$tax_value = $tax_value + $p_tax_value;
			
			foreach ( $op['OrderedProductsAttribute'] as $opa ) {
				$variations[] = $opa['Attribute']['Option']['name'] . ': ' . $opa['Attribute']['value'];
			}
			
			if (!empty($variations)) {
				$sku .= ' / ' . implode(' - ', $variations);
				$variations = implode(' - ', $variations);
			}
			
			// add item might be called for every item in the shopping cart
			// where your ecommerce engine loops through each item in the cart and
			// prints out _addItem for each
			$jscript_code .= "
				_gaq.push(['_addItem',
					'" . $order['Order']['id'] . "',           // order ID - required
					'" . $sku ."',           // SKU/code - required
					'" . $op['Product']['name'] . "',        // product name
					'" . $variations . "',   // category or variation
					'" . $op['product_price_with_dph'] . "',          // unit price - required
					'" . $op['product_quantity'] . "'               // quantity - required
				]);
			";
		}

		$jscript_code = "
			_gaq.push(['_addTrans',
				'" . $order['Order']['id'] . "',           // order ID - required
				'www." . CUST_ROOT . "',  // affiliation or store name
				'" . $order['Order']['orderfinaltotal'] . "',          // total - required
				'" . $tax_value . "',           // tax
				'" . $order['Order']['shipping_cost'] . "',              // shipping
				'" . $order['Order']['customer_city'] . "',       // city
				'',     // state or province
				'" . $order['Order']['customer_state'] . "'             // country
			]);
		" . "\n\n" . $jscript_code;
		
		$jscript_code .= "\n\n" . "_gaq.push(['_trackTrans']);"; //submits transaction to the Analytics servers

		$this->set('jscript_code', $jscript_code);

		$order['Customer']['password'] = $pass;
		$order['Customer']['login'] = $login;
		
		$this->set('order', $order);

	}
} // konec tridy
?>
