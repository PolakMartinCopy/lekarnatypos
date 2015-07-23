<?php
class Order extends AppModel {
	var $name = 'Order';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'OrderedProduct' => array(
			'dependent' => true
		),
		'Ordernote' => array(
			'dependent' => true,
			'order' => array('Ordernote.created' => 'desc')
		)
	);
	var $belongsTo = array('Customer', 'Shipping', 'Payment', 'Status');

	function afterFind($results){
		if ( isset( $results[0]['Order']) && isset($results[0]['Order']['subtotal_with_dph']) && isset($results[0]['Order']['shipping_cost'])  ){
			$count = count($results);
			for ( $i = 0; $i < $count; $i++ ){
				$results[$i]['Order']['orderfinaltotal'] = $results[$i]['Order']['subtotal_with_dph'] + $results[$i]['Order']['shipping_cost']; 
			}
				
		} else {
			if ( isset($results['subtotal_with_dph']) && isset($results['shipping_cost']) ){
				$results['orderfinaltotal'] = $results['subtotal_with_dph'] + $results['shipping_cost'];
			}
		}

		return $results;
	}

	function reCount($id = null){
		// predpokladam, ze postovne bude za 0 Kc
		$order['Order']['shipping_cost'] = 0;

		// nactu si produkty z objednavky a data o ni
		$contain = array(
			'OrderedProduct' => array(
				'fields' => array('OrderedProduct.id', 'product_id', 'product_quantity', 'product_price_with_dph'),
				'Product' => array(
					'fields' => array('Product.id', 'name'),
					'FlagsProduct'
				)
			)
		);
		
		$conditions = array('Order.id' => $id);
		
		$fields = array('Order.id', 'customer_ico', 'customer_dic', 'subtotal_with_dph', 'shipping_cost', 'shipping_id');
		
		$products = $this->find('first', array(
			'conditions' => $conditions,
			'contain' => $contain,
			'fields' => $fields
		));
	
		$order_total = 0;
		$free_shipping = false;
		foreach ( $products['OrderedProduct'] as $product ){
			$order_total = $order_total + $product['product_price_with_dph'] * $product['product_quantity'];
		}
		$order['Order']['subtotal_with_dph'] = $order_total;
		
		// musim zjistit, jestli zakaznik, ktery sestavil objednavku, byl voc, protoze podle toho se pocita cena dopravy
		$customer = $this->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array('Customer'),
			'fields' => array('Customer.id')
		));
		$is_voc = $this->Customer->is_voc($customer['Customer']['id']);
		$order['Order']['shipping_cost'] = $this->Shipping->get_cost($products['Order']['shipping_id'], $order_total, $is_voc);
		$this->id = $id;
		$this->save($order, false, array('subtotal_with_dph', 'shipping_cost'));
	}

	/**
	 * Zjisti stav objednavky odeslane pres ceskou postu.
	 * 
	 * @param unsigned int $id - Cislo objednavky.
	 */
	function track_cpost($id = null){
		App::import('Helper', 'Session');
		$this->Session = new SessionHelper;
		
		// nactu si objednavku, protoze potrebuju vedet
		// cislo baliku v kterem byla objednavka expedovana
		$this->recursive = -1;
		$order = $this->read(null, $id);
		
		$this->Shipping->id = $order['Order']['shipping_id'];
		$this->Shipping->recursive = -1;
		$shipping = $this->Shipping->read();
		
		$tracker_url = $shipping['Shipping']['tracker_prefix'] . trim($order['Order']['shipping_number']) . $shipping['Shipping']['tracker_postfix'];
		// nactu si obsah trackovaci stranky
		$contents = download_url($tracker_url);
		if ($contents !== false){
			$contents = eregi_replace("\r\n", "", $contents);
			$contents = eregi_replace("\t", "", $contents);
			
			// z obsahu vyseknu usek, ktery zminuje jednotlive stavy objednavky
			$pattern = '|<table class="datatable2"> <tr> <th>Datum</th>.*</tr>(.*)</table>|U';
			preg_match_all($pattern, $contents, $table_contents);

			if (!isset($table_contents[1][0])) {
				$pattern = '/<div class="infobox">(.*)<\/div>/';
				if (preg_match($pattern, $contents, $messages)) {
					$message = strip_tags($messages[1]);
					return $id;
				}
				return $id;
				die('nesedi pattern pri zjisteni dorucenosti baliku u CP - tabulka');
			}

			// stavy si rozhodim do jednotlivych prvku pole
			$pattern = '|<tr>(.*)</tr>|U';
			preg_match_all($pattern, $table_contents[1][0], $rows);
			if (!isset($rows[1])) {
				return $id;
				die('nesedi pattern pri zjisteni dorucenosti baliku u CP - radek tabulky');
			}

			// priznak, zda jsem narazil na status ktery meni objednavku
			// na dorucenou, ulozenou na poste apod.
			$found = false;
			
			foreach ($rows[1] as $os){
				if ( eregi('Dodání zásilky.', $os) ){
					// mam dorucenou objednavku, dal neprochazim
					$found = true;

					// pokud byla dorucena, najdu si datum doruceni
					$date = '';
					
					$pattern = '|([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4})|';
					preg_match_all($pattern, $os, $date);
					if (!isset($date[1][0])) {
						return $id;
						die('nesedi pattern pri zjisteni dorucenosti baliku u CP - datum');
					}
					$date = date('d.m.Y', strtotime($date[1][0]));
					// musim zmenit objednavku na doruceno a zapsat poznamku o tom, kdy byla dorucena
					$this->id = $id;
					$this->save(array('status_id' => '4'), false, array('status_id', 'modified'));
					
					// zapisu poznamku o tom, kdy byla dorucena
					$note = array('order_id' => $id,
						'status_id' => '4',
						'administrator_id' => $this->Session->read('Administrator.id'),
						'note' => 'Zásilka byla automaticky identifikována jako doručená zákazníkovi. Datum doručení: ' . $date
					);
					unset($this->Ordernote->id);
					$this->Ordernote->save($note);
				}
			}
			
			// doruceno nemam, hledam, jestli se zasilka nevratila zpet odesilateli
			if ( !$found ){
				foreach ($rows[1] as $os){
					if ( eregi('Vrácení zásilky odesílateli', $os) ){
						$found = true;
						
						// pokud byla vracena, najdu si datum vraceni
						$date = '';
						
						$pattern = '|([0-9]{2}\.[0-9]{2}\.[0-9]{4})|';
						preg_match_all($pattern, $os, $date);
						if (!isset($date[1][0])) {
							return $id;
							die('nesedi pattern pri zjisteni dorucenosti baliku u CP - datum');
						}
						$date = date('d.m.Y', strtotime($date[1][0]));
						
						// musim zmenit objednavku na vraceno a zapsat poznamku o tom, kdy byla vracena
						$this->id = $id;
						$this->save(array('status_id' => '8'), false, array('status_id', 'modified'));
						
						// zapisu poznamku o tom, kdy byla vracena
						$note = array('order_id' => $id,
							'status_id' => '8',
							'administrator_id' => $this->Session->read('Administrator.id'),
							'note' => 'Zásilka byla automaticky identifikována jako vrácená zpět. Datum návratu: ' . $date
						);
						unset($this->Ordernote->id);
						$this->Ordernote->save($note);
					}
				}
			}

			// stav doruceno ani vraceno nemam, hledam ulozeni na poste
			if ( !$found ){
				foreach ($rows[1] as $os){
					// objednavka je ulozena na poste a ceka na vyzvednuti
					// zaroven ale kontroluju, jestli uz clovek nebyl upozornen,
					// tzn ze objednavka uz ma status cislo 9
					if ( eregi('After unsuccessful attempt of delivery', $os) && $order['Order']['status_id'] != 9 ){
						// pokud byla ulozena, najdu si datum ulozeni
						$date = '';
						
						$pattern = '|([0-9]{2}\.[0-9]{2}\.[0-9]{4})|';
						preg_match_all($pattern, $os, $date);
						if (!isset($date[1][0])) {
							return $id;
							die('nesedi pattern pri zjisteni dorucenosti baliku u CP - datum');
						}
						$date = date('d.m.Y', strtotime($date[1][0]));
						
						// musim zmenit objednavku na ulozeno a zapsat poznamku o tom, kdy byla ulozena
						$this->id = $id;
						$this->save(array('status_id' => '9'), false, array('status_id', 'modified'));
						
						// zapisu poznamku o tom, kdy byla ulozena
						$note = array('order_id' => $id,
							'status_id' => '9',
							'administrator_id' => $this->Session->read('Administrator.id'),
							'note' => 'Zásilka byla automaticky identifikována jako uložená na poště. Zákazníkovi byl odeslan email o uložení. Datum uložení: ' . $date
						);
						
						if ( !$this->Status->change_notification($id, 9) ){
							$note['note'] = 'Zásilka byla automaticky identifikována jako uložená na poště. ZÁKAZNÍKOVI NEBYL ODESLÁN MAIL! Datum uložení: ' . $date; 
							return false;
						}
						
						unset($this->Ordernote->id);
						$this->Ordernote->save($note);
					}
					
				}
			}
			return true;
		}
		return $id;
	}
	
	function track_ppl($id = null) {
		// nactu si objednavku, protoze potrebuju vedet
		// cislo baliku v kterem byla objednavka expedovana
		$this->contain('Shipping');
		$order = $this->read(null, $id);

		// sestavim si URL, kde jsou informace o zasilce
		$tracker_url = $order['Shipping']['tracker_prefix'] . trim($order['Order']['shipping_number']) . $order['Shipping']['tracker_postfix'];

		// nactu si obsah trackovaci stranky
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $tracker_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$contents = curl_exec($ch);
		curl_close($ch);
		if ( $contents !== false ){
			if ( eregi('Zásilka nenalezena', $contents) ){
				return $id;
			}
				
			$original = $contents;
			$contents = str_replace("\r\n", "", $contents);
			$contents = str_replace("\t", "", $contents);
			// z obsahu vyseknu usek, ktery zminuje jednotlive stavy objednavky
			$pattern = '/<table class="frm2" style="width:100%;">\s+<caption>Detail<\/caption>(.*)<\/table>/U';
			preg_match_all($pattern, $contents, $contents);
			// stavy si rozhodim do jednotlivych prvku pole
			$pattern = '/<tr class="(?:[^"]+)">\s*<td>(.*)<\/td>\s*<td>(.*)<\/td>\s*<\/tr>/U';
			preg_match_all($pattern, $contents[1][0], $contents);
			$pattern = '/Zásilka doručena/';
	
			$count = count($contents[1]);
			for ( $i = 0; $i < $count; $i++ ){
				// najdu si, zda objednavka byla dorucena
				if (preg_match($pattern, $contents[2][$i])) {
					// pokud byla dorucena, najdu si datum doruceni
					$date = '';
	
					if (!empty($contents[1][$i])) {
						$pattern = '/([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4} [0-9]{1,2}:[0-9]{2})/';
						if (preg_match($pattern, $contents[1][$i], $date)) {
							// potrebuju si nacits admina ze session,
							// takze si pripojim helper pro session
							App::import('Helper', 'Session');
							$this->Session = new SessionHelper;

							$data_source = $this->getDataSource();
							$data_source->begin($this);
							
							// musim zmenit objednavku na doruceno a zapsat poznamku o tom, kdy byla dorucena
							$this->id = $id;
							if (!$this->save(array('status_id' => '4'), false, array('status_id', 'modified'))) {
								$data_source->rollback($this);
								die('neulozil jsem stav objednavky');
								continue;
							}
							
							// zapisu poznamku o tom, kdy byla dorucena
							$note = array('order_id' => $id,
								'status_id' => '4',
								'administrator_id' => $this->Session->read('Administrator.id'),
								'note' => 'Zásilka byla automaticky identifikována jako doručená zákazníkovi. Datum doručení: ' . $date[1],
								'created' => date('Y-m-d H:i:s'),
								'modified' => date('Y-m-d H:i:s')
							);
							
							$this->Ordernote->create();
							if (!$this->Ordernote->save($note)) {
								$data_source->rollback($this);
								die('neulozila se poznamka o zmene stavu');
								continue;
							}
							$data_source->commit($this);
							break;
						} else {
							return $id;
						}
					}
					// zatim nedoruceno, nic se neprovadi
				}
			}
			return true;
		} else {
			return $id;
		}
	}

	/*
	 * @description					Zjisti stav objednavky odeslane pres general parcel.
	 */
	function track_gparcel($id = null){
		App::import('Helper', 'Session');
		$this->Session = new SessionHelper;
		
		// nactu si objednavku, protoze potrebuju vedet
		// cislo baliku v kterem byla objednavka expedovana
		$this->contain('Shipping');
		$order = $this->read(null, $id);

		// natvrdo definovane URL trackeru general parcel
		$tracker_url = $order['Shipping']['tracker_prefix'] . trim($order['Order']['shipping_number']) . $order['Shipping']['tracker_postfix'];

		// nactu si obsah trackovaci stranky
		$contents = download_url($tracker_url);
		
		if ( $contents !== false ){
			$contents = eregi_replace("\r\n", "", $contents);
			$contents = eregi_replace("\t", "", $contents);
			
			$pattern = '|<table class=\"GridView\"(.*)table>|';
			preg_match_all($pattern, $contents, $contents);
			
//			debug($contents);die();
			
			$pattern = '|<td>(.*)</td>|U';
			
			if (!isset($contents[0]) || !isset($contents[0][0])) {
				return false;
			}
			
			preg_match_all($pattern, $contents[0][0], $contents);

			if ( isset($contents[1]) ){
				$rows = array();

				for ( $i = 0; $i < count($contents[1]); $i++) {
					$rows[$i] = trim($contents[1][$i]);
					if ($rows[$i] == 'Doručen&#237;' ){
						// musim zmenit objednavku na doruceno a zapsat poznamku o tom, kdy byla dorucena
						$this->id = $id;
						$this->save(array('status_id' => '4'), false, array('status_id', 'modified'));
						
						$date = date("d.m.Y");
						
						// zapisu poznamku o tom, kdy byla dorucena
						$note = array('order_id' => $id,
							'status_id' => '4',
							'administrator_id' => $this->Session->read('Administrator.id'),
							'note' => 'Zásilka byla automaticky identifikována jako doručená zákazníkovi. Datum doručení: ' . $date
						);
						unset($this->Ordernote->id);
						$this->Ordernote->save($note);
						return true;
					}
				}
			}
			return true;
		}
		return $id;
	}
	
	function track_dpd($id) {
		App::import('Helper', 'Session');
		$this->Session = new SessionHelper;
		
		// nactu si objednavku, protoze potrebuju vedet
		// cislo baliku v kterem byla objednavka expedovana
		$order = $this->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array('Shipping'),
			'fields' => array('Order.id', 'Order.shipping_number', 'Shipping.id', 'Shipping.tracker_prefix', 'Shipping.tracker_postfix')
		));

		// sestavim si URL, kde jsou informace o zasilce
		$tracker_url = $order['Shipping']['tracker_prefix'] . $order['Order']['shipping_number'] . $order['Shipping']['tracker_postfix'];
		
		// nactu si obsah trackovaci stranky
		$contents = file_get_contents($tracker_url);
		
		if ( $contents !== false ){
			$contents = preg_replace('/\r\n/', '', $contents);
			$contents = preg_replace('/\n/', '', $contents);
			$contents = preg_replace('/\t/', '', $contents);
			// vytahnu si tabulku s informacemi o stavu doruceni
			$pattern = '|<table class=\"alternatingTable\"(.*)table>|';
			preg_match_all($pattern, $contents, $contents);
			// tabulku rozsekam na radky
			$pattern = '|<tr [^>]*>(.*)</tr>|U';
			preg_match_all($pattern, $contents[0][0], $contents);

			if (isset($contents[1])) {
				$rows = array();
				// hledam radek, ktery ma informaci o tom, ze zasilka byla dorucena
				for ( $i = 0; $i < count($contents[1]); $i++) {
					$rows[$i] = trim($contents[1][$i]);
					// a pokud jsem ho nasel
					if (preg_match('/>\s*doručeno\s/', $rows[$i])) {

						// musim zmenit objednavku na doruceno a zapsat poznamku o tom, kdy byla dorucena
						$this->id = $id;
						$this->save(array('status_id' => '4'), false, array('status_id', 'modified'));
		
						$date = date("d.m.Y");
		
						// zapisu poznamku o tom, kdy byla dorucena
						$note = array('order_id' => $id,
							'status_id' => '4',
							'administrator_id' => $this->Session->read('Administrator.id'),
							'note' => 'Zásilka byla automaticky identifikována jako doručená zákazníkovi. Datum doručení: ' . $date
						);
						unset($this->Ordernote->id);
						$this->Ordernote->save($note);
						return true;
					}
				}
			}
			return true;
		}
		return $id;
	}

	function build($customer) {
		App::import('model', 'CakeSession');
		$this->Session = &new CakeSession;
		// ze sesny vytahnu data o objednavce a doplnim potrebna data
		$order['Order'] = $this->Session->read('Order');

		$order['Order']['customer_first_name'] = $customer['Customer']['first_name'];
		$order['Order']['customer_last_name'] = $customer['Customer']['last_name'];
		$order['Order']['customer_phone'] = $customer['Customer']['phone'];
		$order['Order']['customer_email'] = $customer['Customer']['email'];
		
		
		if ( isset($customer['Customer']['company_name']) ){
			$order['Order']['customer_company_name'] = $customer['Customer']['company_name'];
		}
		
		if ( isset($customer['Customer']['company_ico']) ){
			$order['Order']['customer_ico'] = $customer['Customer']['company_ico'];
		}

		if ( isset($customer['Customer']['company_dic']) ){
			$order['Order']['customer_dic'] = $customer['Customer']['company_dic'];
		}
		// doplnim data o fakturacni adrese
		if (isset($customer['Address'][1]['name']) && isset($customer['Address'][1]['street']) && isset($customer['Address'][1]['street_no']) && isset($customer['Address'][1]['city']) && isset($customer['Address'][1]['zip']) && isset($customer['Address'][1]['state'])) {	
			$order['Order']['customer_name'] = $customer['Address'][1]['name'];
			if (isset($customer['Address'][1]['contact_first_name'])) {
				$order['Order']['customer_first_name'] = $customer['Address'][1]['contact_first_name'];
			}
			if (isset($customer['Address'][1]['contact_last_name'])) {
				$order['Order']['customer_last_name'] = $customer['Address'][1]['contact_last_name'];
			}
			$order['Order']['customer_street'] = $customer['Address'][1]['street'] . ' ' . $customer['Address'][1]['street_no'];
			$order['Order']['customer_city'] = $customer['Address'][1]['city'];
			$order['Order']['customer_zip'] = $customer['Address'][1]['zip'];
			$order['Order']['customer_state'] = $customer['Address'][1]['state'];
		} else {
			$order['Order']['customer_name'] = $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'];
		}

		// doplnim data o dorucovaci adrese
		if (isset($customer['Address'][0]['name']) && isset($customer['Address'][0]['street']) && isset($customer['Address'][0]['street_no']) && isset($customer['Address'][0]['city']) && isset($customer['Address'][0]['zip']) && isset($customer['Address'][0]['state'])) {
			$order['Order']['delivery_name'] = $customer['Address'][0]['name'];
			if (isset($customer['Address'][0]['contact_first_name'])) {
				$order['Order']['delivery_first_name'] = $customer['Address'][0]['contact_first_name'];
			}
			if (isset($customer['Address'][0]['contact_last_name'])) {
				$order['Order']['delivery_last_name'] = $customer['Address'][0]['contact_last_name'];
			}
			$order['Order']['delivery_street'] = $customer['Address'][0]['street'] . ' ' . $customer['Address'][0]['street_no'];
			$order['Order']['delivery_city'] = $customer['Address'][0]['city'];
			$order['Order']['delivery_zip'] = $customer['Address'][0]['zip'];
			$order['Order']['delivery_state'] = $customer['Address'][0]['state'];
		}
		
		$order['Order']['customer_id'] = $customer['Customer']['id'];
		$order['Order']['status_id'] = 1;

		// data pro produkty objednavky
		App::import('Model', 'CartsProduct');
		$this->CartsProduct = &new CartsProduct;
		$cart_products = $this->CartsProduct->getProducts();
		$cart_id = $this->CartsProduct->Cart->get_id();
		$mail_products = array();
		$order_total_with_dph = 0;
		$order_total_wout_dph = 0;
		$ordered_products = array();

		$cp_count = 0;
		foreach ( $cart_products as $cart_product ){
			// nactu produkt, abych si zapamatoval jeho jmeno
			$product = $this->OrderedProduct->Product->find('first', array(
				'conditions' => array('Product.id' => $cart_product['CartsProduct']['product_id']),
				'contain' => array('Manufacturer', 'TaxClass'),
				'fields' => array('Product.id', 'Product.name', 'Manufacturer.id', 'Manufacturer.name', 'TaxClass.id', 'TaxClass.value')	
			));
			// data pro produkt
			$ordered_products[$cp_count]['OrderedProduct']['product_id'] = $cart_product['CartsProduct']['product_id'];
			$ordered_products[$cp_count]['OrderedProduct']['subproduct_id'] = $cart_product['CartsProduct']['subproduct_id'];
			$ordered_products[$cp_count]['OrderedProduct']['product_price_with_dph'] = $cart_product['CartsProduct']['price_with_dph'];
			$price_wout_dph = $cart_product['CartsProduct']['price_wout_dph'];
			if (empty($price_wout_dph)) {
				$percentage = 100 + $product['TaxClass']['value'];
				$price_wout_dph = round($cart_product['CartsProduct']['price_with_dph'] / $percentage * 100, 2);
			}
			$ordered_products[$cp_count]['OrderedProduct']['product_price_wout_dph'] = $price_wout_dph;
			$ordered_products[$cp_count]['OrderedProduct']['product_quantity'] = $cart_product['CartsProduct']['quantity'];
			$ordered_products[$cp_count]['OrderedProduct']['product_name'] = $this->OrderedProduct->generate_product_name($product['Product']['id']);
			
			$order_total_with_dph = $order_total_with_dph + ($cart_product['CartsProduct']['quantity'] * $cart_product['CartsProduct']['price_with_dph']);
			$order_total_wout_dph = $order_total_wout_dph + ($cart_product['CartsProduct']['quantity'] * $cart_product['CartsProduct']['price_wout_dph']);
			// pamatuju si atributy objednaneho produktu
			$ordered_products[$cp_count]['OrderedProductsAttribute'] = array();
			if ( !empty($cart_product['CartsProduct']['subproduct_id']) ){
				$as_count = 0;
				foreach ( $cart_product['Subproduct']['AttributesSubproduct'] as $attributes_subproduct ){
					// vlozeni dat do atributu
					$ordered_products[$cp_count]['OrderedProductsAttribute'][$as_count]['attribute_id'] = $attributes_subproduct['attribute_id'];
					
					$as_count++;
				}
			}
			$cp_count++;
		}
		$order['Order']['shipping_cost'] = $this->get_shipping_cost($order['Order']['shipping_id']);
		$order['Order']['shipping_tax_class'] = $this->Shipping->get_tax_class_description($order['Order']['shipping_id']);
		// cena produktu v kosiku, bez dopravneho
		$order['Order']['subtotal_with_dph'] = $order_total_with_dph;
		$order['Order']['subtotal_wout_dph'] = $order_total_wout_dph;

		return array($order, $ordered_products);
	}
	
	function get_shipping_cost($shipping_id) {
		// data pro produkty objednavky
		App::import('Model', 'CartsProduct');
		$this->CartsProduct = &new CartsProduct;

		$cart_products = $this->CartsProduct->getProducts();
		$order_total_with_dph = 0;
		$order_total_wout_dph = 0;
		
		$free_shipping = false;

		$cp_count = 0;
		foreach ($cart_products as $cart_product) {
			// projdu vsechny priznaky
			foreach ( $cart_product['Product']['Flag'] as $flags_product ){
				// priznak pro dopravu zdarma je "1"
				if ( $flags_product['FlagsProduct']['flag_id'] == 1 && $cart_product['CartsProduct']['quantity'] >= $flags_product['FlagsProduct']['quantity'] ){
					$free_shipping = true;
				}
			}
				
			$order_total_with_dph = $order_total_with_dph + ($cart_product['CartsProduct']['quantity'] * $cart_product['CartsProduct']['price_with_dph']);
			$order_total_wout_dph = $order_total_wout_dph + ($cart_product['CartsProduct']['quantity'] * $cart_product['CartsProduct']['price_wout_dph']);
			$cp_count++;
		}
		
		// dopocitavam si cenu dopravneho pro objednavku predpokladam nulovou cenu
		$shipping_cost = 0;
		if (!$free_shipping) {
			// objednavka neobsahuje produkt s dopravou zdarma,
			// cenu dopravy si proto dopocitam v zavislosti na
			// cene objednaneho zbozi
			$is_voc = false;
			App::import('model', 'CakeSession');
			$this->Session = &new CakeSession;
			// ze sesny vytahnu data o objednavce a doplnim potrebna data
			$customer = $this->Session->read('Customer');
			if (isset($customer['id'])) {
				$is_voc = $this->Customer->is_voc($customer['id']);
			}
			$shipping_cost = $this->Shipping->get_cost($shipping_id, $order_total_with_dph, $is_voc);
		}
		return $shipping_cost;
	}
	
	function cleanCartsProducts () {
		App::import('model', 'CartsProduct');
		$this->CartsProduct = &new CartsProduct;
		$cart_id = $this->CartsProduct->Cart->get_id();
		
		$carts_products = $this->CartsProduct->find('all', array(
			'conditions' => array('cart_id' => $cart_id),
			'contain' => array(),
			'fields' => array('id')
		));
		
		foreach ($carts_products as $carts_product) {
			$this->CartsProduct->delete($carts_product['CartsProduct']['id']);
		}
	}
	
	function notifyCustomer($customer, $id = null) {
		if (isset($id) && (!isset($this->id) || (isset($this->id) && empty($this->id)))) {
			$this->id = $id;
		}
		
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		if ( isset($customer['email']) && !empty($customer['email']) ){
			$mail_c = new phpmailer();
			// uvodni nastaveni
			$mail_c->CharSet = 'utf-8';
			$mail_c->Hostname = CUST_ROOT;
			$mail_c->Sender = CUST_MAIL;
			$mail_c->IsHtml(true);
	
			// nastavim adresu, od koho se poslal email
			$mail_c->From     = CUST_MAIL;
			$mail_c->FromName = CUST_NAME;
	
			$mail_c->AddReplyTo(CUST_MAIL, CUST_NAME);
	
			$mail_c->AddAddress($customer['email'], $customer['first_name'] . ' ' . $customer['last_name']);
			//$mail_c->AddBCC('brko11@gmail.com');
			$mail_c->Subject = 'POTVRZENÍ OBJEDNÁVKY (č. ' . $this->id . ')';

			$customer_mail = $this->order_mail($this->id);
			if (is_array($customer_mail)) {
				$mail_c->Subject = $customer_mail['MailTemplate']['subject'];
				$customer_mail = $customer_mail['MailTemplate']['content'];
			}
			
			$mail_c->Body = $customer_mail;

			if (!$mail_c->Send()) {
				App::import('Model', 'Tool');
				$this->Tool = &new Tool;
				$this->Tool->log_notification($this->id, 'customer');
			}
		}
	}
	
	function notifyAdmin($id = null) {
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		
		if (isset($id) && (!isset($this->id) || (isset($this->id) && empty($this->id)))) {
			$this->id = $id;
		}

		// notifikacni email prodejci
		// vytvorim tridu pro mailer
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = new phpmailer();

		// uvodni nastaveni
		$mail->CharSet = 'utf-8';
		$mail->Hostname = $this->Setting->findValue('CUST_ROOT');
		$mail->Sender = $this->Setting->findValue('CUST_MAIL');
		$mail->IsHtml(true);

		// nastavim adresu, od koho se poslal email
		$mail->From     = CUST_MAIL;
		$mail->FromName = CUST_NAME;

		$mail->AddReplyTo($this->Setting->findValue('CUST_MAIL'), $this->Setting->findValue('CUST_NAME'));

		$mail->AddAddress($this->Setting->findValue('CUST_MAIL'), $this->Setting->findValue('CUST_NAME'));
//		$mail->AddAddress("vlado.tovarnak@gmail.com", "Vlado Tovarnak");
		$mail->AddAddress('brko11@gmail.com', 'Martin Polák');
		$mail->AddAddress('martin@drdla.eu', 'Martin Drdla');

		$mail->Subject = 'E-SHOP OBJEDNÁVKA (č. ' . $this->id . ')';
		$mail->Body = 'Právě byla přijata nová objednávka pod číslem ' . $this->id . '.' . "\n";
		$mail->Body .= 'Pro její zobrazení se přihlašte v administraci obchodu: http://www.' . $this->Setting->findValue('CUST_ROOT') . '/admin/' . "\n\n";
		// zmenit na false. adminum nechci posilat grafiku
		$customer_mail = $this->order_mail($this->id, false);
		if (is_array($customer_mail)) {
			$mail->Subject = $customer_mail['MailTemplate']['subject'];
			$customer_mail = $customer_mail['MailTemplate']['content'];
		}
		
		$mail->Body .= $customer_mail;
		if (!$mail->Send()) {
			App::import('Model', 'Tool');
			$this->Tool = &new Tool;
			$this->Tool->log_notification($this->id, 'admin');
		}
	}
	
	function order_mail($id, $graphics = true) {
		$order = $this->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'Shipping',
				'Payment',
				'OrderedProduct' => array(
					'Product' => array(
						'fields' => array('Product.id', 'Product.url')
					),
					'OrderedProductsAttribute' => array(
						'Attribute' => array(
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							),
							'fields' => array('Attribute.id', 'Attribute.value')
						),
						'fields' => array('OrderedProductsAttribute.id')
					)
				)
			)
		));
		
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $order['Order']['customer_id']),
			'contain' => array('CustomerType'),
			'fields' => array('CustomerType.id', 'CustomerType.name')
		));
		
		$customer_invoice_address = '&nbsp;';
		$customer_delivery_address = '&nbsp;';
		if ($order['Order']['shipping_id'] != PERSONAL_PURCHASE_SHIPPING_ID) {
			$invoice_full_name = full_name($order['Order']['customer_first_name'], $order['Order']['customer_last_name']);
			$delivery_full_name = full_name($order['Order']['delivery_first_name'], $order['Order']['delivery_last_name']);
			$customer_invoice_address = 'Fakturační adresa: ' . (!empty($invoice_full_name) ? $invoice_full_name . ', ' : '') . $order['Order']['customer_street'] . ', ' . $order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city'] . ' ' . $order['Order']['customer_state'];
			$customer_delivery_address = 'Dodací adresa: ' . $order['Order']['delivery_name'] . ', ' . (!empty($delivery_full_name) ? $delivery_full_name . ', ' : '') . $order['Order']['delivery_street'] . ', ' . $order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city'] . ', ' . $order['Order']['delivery_state'];
		}

		$mail_template_conditions = false;
		if (defined('NEW_ORDER_MAIL_TEMPLATE_ID')) {
			$mail_template_conditions = array('MailTemplate.id' => NEW_ORDER_MAIL_TEMPLATE_ID);
		}
		
		if ($mail_template_conditions && $this->Status->MailTemplate->hasAny($mail_template_conditions) && $graphics) {
			$mail_template = $this->Status->MailTemplate->find('first', array(
				'conditions' => $mail_template_conditions,
				'contain' => array(),
				'fields' => array('MailTemplate.id')
			));
			
			if (empty($mail_template)) {
				return false;
			} else {
				$mail_template = $this->Status->MailTemplate->process($mail_template['MailTemplate']['id'], $id);
				return $mail_template;
			}
		} else {	
			// hlavicka emailu s identifikaci dodavatele a odberatele
			$customer_mail = '<h1>Objednávka č. ' . $id . '</h1>' . "\n";
			$customer_mail .= '<table style="width:100%">' . "\n";
			$customer_mail .= '<tr><th style="text-align:center;width:50%">Dodavatel</th><th style="text-align:center">Odběratel</th></tr>' . "\n";
			$customer_mail .= '<tr><td><strong>' . str_replace('<br/>', ', ', CUST_NAME) . '</strong></td><td><strong>' . $order['Order']['customer_name'] . '</strong>' . (!empty($customer['CustomerType']['name']) ? ' (' . $customer['CustomerType']['name'] . ')' : '') . '</td></tr>' . "\n";
			$customer_mail .= '<tr><td>IČ: ' . CUST_ICO . '</td><td>IČ: ' . $order['Order']['customer_ico'] . '</td></tr>' . "\n";
			$customer_mail .= '<tr><td>DIČ: ' . CUST_DIC . '</td><td>DIČ: ' . $order['Order']['customer_dic'] . '</td></tr>' . "\n";
			$customer_mail .= '<tr><td>Adresa: ' . CUST_STREET . ', ' . CUST_ZIP . ' ' . CUST_CITY . '</td><td>' . $customer_invoice_address . '</td></tr>' . "\n";
			$customer_mail .= '<tr><td>Email: <a href="mailto:' . CUST_MAIL . '">' . CUST_MAIL . '</a></td><td>Email: <a href="mailto:' . $order['Order']['customer_email'] . '">'. $order['Order']['customer_email'] . '</a></td></tr>' . "\n";
			$customer_mail .= '<tr><td>Telefon: ' . CUST_PHONE . '</td><td>Telefon: ' . $order['Order']['customer_phone'] . '</td></tr>' . "\n";
			$customer_mail .= '<tr><td>Web: <a href="http://www.' . CUST_ROOT . '">http://www.' . CUST_ROOT . '</a></td><td><strong>' . $customer_delivery_address . '</strong></td></tr>' . "\n";
			$customer_mail .= '</table><br/>' . "\n";
	
			// telo emailu s obsahem objednavky
			$customer_mail .= '<table style="width:100%">' . "\n";
			$customer_mail .= '<tr><th style="text-align:center;width:10%">Počet</th><th style="text-align:center;width:70%">název, kód, poznámka</th><th style="text-align:center;width:10%">cena/ks</th><th style="text-align:center;width:10%">cena celkem</th></tr>' . "\n";
			foreach ($order['OrderedProduct'] as $ordered_product) {
				$attributes = array();
				if ( !empty($ordered_product['OrderedProductsAttribute']) ){
					foreach ( $ordered_product['OrderedProductsAttribute'] as $attribute ){
						$attributes[] = $attribute['Attribute']['Option']['name'] . ': ' . $attribute['Attribute']['value'];
					}
					$attributes = implode(', ', $attributes);
				}
				
				$customer_mail .= '<tr><td>' . $ordered_product['product_quantity'] . '</td><td><a href="http://www.' . CUST_ROOT . '/' . $ordered_product['Product']['url'] . '">' . $ordered_product['product_name'] . '</a>' . (!empty($attributes) ? ', ' . $attributes : '') . '</td><td>' . round($ordered_product['product_price_with_dph']) . '&nbsp;Kč</td><td>' . ($ordered_product['product_quantity'] * round($ordered_product['product_price_with_dph'])) . '&nbsp;Kč</td></tr>' . "\n";
			}
			$customer_mail .= '<tr><td>1</td><td>' . $order['Shipping']['name'] . '</td><td>' . round($order['Order']['shipping_cost']) . '&nbsp;Kč</td><td>' . round($order['Order']['shipping_cost']) . '&nbsp;Kč</td></tr>' . "\n";
			$customer_mail .= '<tr><td>1</td><td>' . $order['Payment']['name'] . '</td><td>0&nbsp;Kč</td><td>0&nbsp;Kč</td></tr>' . "\n";
			$customer_mail .= '</table>' . "\n";
			
			$customer_mail .= '<h2>Celkem k úhradě: ' . ($order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost']) . '&nbsp;Kč</h2>' . "\n";
			
			if (!empty($order['Order']['comments'])) {
				$customer_mail .= '<p><strong>Poznámka: ' . $order['Order']['comments'] . '</strong></p>' . "\n";
			}

			return $customer_mail; 
		}
	}
	
	function create_pohoda_file($order_ids = array()) {
		// stahnu si data k objednavkam, ktere pujdou do exportu
		$orders = $this->find('all', array(
			'conditions' => array('Order.id' => $order_ids, 'Order.invoice' => false),
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
				'PaymentType.pohoda_name'
			),
		));
	
		$output = '<dat:dataPack id="3478" ico="42956391" application="e-shop" version="2.0" note="Import Objednavky"
	xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd"
	xmlns:ord="http://www.stormware.cz/schema/version_2/order.xsd"
	xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd"
>';
		foreach ($orders as $order) {
			$order_date = $order['Order']['created'];
			$order_date = explode(' ', $order_date);
			$order_date = $order_date[0];
			
			$output .= '
	<dat:dataPackItem id="' . $order['Order']['id'] . '" version="2.0">
		<ord:order version="2.0">
			<ord:orderHeader>
				<ord:orderType><![CDATA[receivedOrder]]></ord:orderType>
				<ord:numberOrder><![CDATA[' . $order['Order']['id'] . ']]></ord:numberOrder>
				<ord:date><![CDATA[' . $order_date . ']]></ord:date>
				<ord:text><![CDATA[' . $order['Order']['comments'] . ']]></ord:text>
				<ord:partnerIdentity>
					<typ:address>
						<typ:company><![CDATA[' . $order['Order']['customer_name'] . ']]></typ:company>
						<typ:name><![CDATA[' . full_name($order['Order']['customer_first_name'], $order['Order']['customer_last_name']) . ']]></typ:name>
						<typ:city><![CDATA['. $order['Order']['customer_city'] . ']]></typ:city>
						<typ:street><![CDATA[' . $order['Order']['customer_street'] . ']]></typ:street>
						<typ:zip><![CDATA[' . $order['Order']['customer_zip'] . ']]></typ:zip>
						<typ:ico><![CDATA[' . $order['Order']['customer_ico'] . ']]></typ:ico>
						<typ:dic><![CDATA['. $order['Order']['customer_dic'] . ']]></typ:dic>
						<typ:phone><![CDATA[' . $order['Order']['customer_phone'] . ']]></typ:phone>
						<typ:email><![CDATA[' . $order['Order']['customer_email'] . ']]></typ:email>
					</typ:address>
					<typ:shipToAddress>
			            <typ:name><![CDATA[' . $order['Order']['delivery_name'] . ']]></typ:name>
			            <typ:city><![CDATA[' . $order['Order']['delivery_city'] . ']]></typ:city>
			            <typ:street><![CDATA[' . $order['Order']['delivery_street'] . ']]></typ:street>
			            <typ:zip><![CDATA[' . $order['Order']['delivery_zip'] . ']]></typ:zip>
					</typ:shipToAddress>
				</ord:partnerIdentity>
				<ord:paymentType>
					<typ:ids>' . $order['PaymentType']['pohoda_name'] . '</typ:ids>
				</ord:paymentType>
			</ord:orderHeader>
			<ord:orderDetail>';
			
			foreach ($order['OrderedProduct'] as $ordered_product) {
				$output .= '
				<ord:orderItem>
					<ord:text><![CDATA[' . $ordered_product['product_name'] . ']]></ord:text> 
					<ord:quantity>' . $ordered_product['product_quantity'] . '</ord:quantity> 
					<ord:payVAT>true</ord:payVAT> 
					<ord:rateVAT>' . ($ordered_product['Product']['tax_class_id'] == 1 ? 'high' : 'low') . '</ord:rateVAT> 
					<ord:homeCurrency>
						<typ:unitPrice>' . round($ordered_product['product_price_with_dph']) . '</typ:unitPrice> 
					</ord:homeCurrency>
					<ord:stockItem>
						<typ:stockItem>
							<typ:ids>' . $ordered_product['Product']['pohoda_id'] . '</typ:ids> 
						</typ:stockItem>
					</ord:stockItem>
				</ord:orderItem>';
			}

			$output .= '
				<ord:orderItem>
					<ord:text><![CDATA[' . $order['Shipping']['name'] . ']]></ord:text> 
					<ord:quantity>1</ord:quantity>
					<ord:payVAT>true</ord:payVAT> 
					<ord:rateVAT>' . $order['Order']['shipping_tax_class'] . '</ord:rateVAT> 
					<ord:homeCurrency>
						<typ:unitPrice>' . $order['Order']['shipping_cost'] . '</typ:unitPrice> 
					</ord:homeCurrency>
					<ord:stockItem>
						<typ:stockItem>
							<typ:ids></typ:ids> 
						</typ:stockItem>
					</ord:stockItem>
				</ord:orderItem>
				<ord:orderItem>
					<ord:text><![CDATA[' . $order['Payment']['name'] . ']]></ord:text> 
					<ord:quantity>1</ord:quantity> 
					<ord:payVAT>true</ord:payVAT> 
					<ord:rateVAT>none</ord:rateVAT> 
					<ord:homeCurrency>
						<typ:unitPrice>0</typ:unitPrice> 
					</ord:homeCurrency>
					<ord:stockItem>
						<typ:stockItem>
							<typ:ids></typ:ids>
						</typ:stockItem>
					</ord:stockItem>
				</ord:orderItem>
			</ord:orderDetail>
			<ord:orderSummary>
				<ord:roundingDocument>math2one</ord:roundingDocument> 
			</ord:orderSummary>
		</ord:order>
	</dat:dataPackItem>';
		}
		$output .= '
</dat:dataPack>';
		
		// zjistim nazev souboru, do ktereho budu export ukladat
		$file_name = $this->get_pohoda_file_name();
		
		// ulozim vystup do souboru
		if (file_put_contents(POHODA_EXPORT_DIR . DS . $file_name, $output)) {
			return $file_name;
		}
		return false;

	}
	
	function get_pohoda_file_name() {
		return 'pohoda-export.xml';
	}
	
	function set_attribute($order_ids, $name, $value) {
		if (is_int($order_ids)) {
			$order_ids = array(0 => $order_ids);
		}
		if (!is_array($order_ids)) {
			return false;
		}

		$success = true;
		foreach ($order_ids as $order_id) {
			$order = array(
				'Order' => array(
					'id' => $order_id,
					$name => $value
				)	
			);
			$success = $success && $this->save($order);
		}
		return $success;
	}

	function do_form_search($conditions, $data) {
		if (isset($data['Order']['from']) && !empty($data['Order']['from'])) {
			$from = cz2db_date($data['Order']['from']);
			$conditions['DATE(Order.created) >='] = $from;
		}

		if (isset($data['Order']['to']) && !empty($data['Order']['to'])) {
			$to = cz2db_date($data['Order']['to']);
			$conditions['DATE(Order.created) <='] = $to;
		}
		$fulltext_fields = array('fulltext1', 'fulltext2');
		foreach ($fulltext_fields as $fulltext_field) {
			if (isset($data['Order'][$fulltext_field]) && !empty($data['Order'][$fulltext_field])) {
				$conditions[] = array(
					'OR' => array(
						array('Order.id' => $data['Order'][$fulltext_field]),
						array('OrderedProduct.product_name LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Order.customer_name LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Order.customer_email LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Order.customer_phone LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Order.customer_street LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Order.customer_city LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Order.customer_zip LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Shipping.name LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
						array('Payment.name LIKE "%%' . $data['Order'][$fulltext_field] . '%%"'),
					)
				);
			}
		}
		return $conditions;
	}
} // konec tridy
?>
