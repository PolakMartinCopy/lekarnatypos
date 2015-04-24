<?php
class SuppliersController extends AppController {
	var $name = 'Suppliers';
	
	function admin_index() {
		$this->paginate = array(
			'conditions' => array('Supplier.active' => true),
			'contain' => array(),
			'limit' => 30	
		);
		
		$suppliers = $this->paginate();
		$this->set('suppliers', $suppliers);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if (isset($this->data)) {
			if ($this->Supplier->save($this->data)) {
				$this->Session->setFlash('Dodavatel byl uložen', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Dodavatele se nepodařilo uložit. Opravte chyby ve formuláři a uložte jej prosím znovu', REDESIGN_PATH . 'flash_failure');
			}
		}
		$categories = $this->Supplier->SupplierCategory->Category->generatetreelist(array('id !=' => 5), null, null, '_');
		$this->set('categories', $categories);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dodavatel, kterého chcete upravovat', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		$supplier = $this->Supplier->find('first', array(
			'conditions' => array('Supplier.id' => $id),
			'contain' => array(),
		));
		
		if (empty($supplier)) {
			$this->Session->setFlash('Neznámý dodavatel', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->Supplier->save($this->data)) {
				$this->Session->setFlash('Dodavatel byl uložen', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Dodavatele se nepodařilo uložit. Opravte chyby ve formuláři a uložte jej prosím znovu', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $supplier;
		}
		$categories = $this->Supplier->SupplierCategory->Category->generatetreelist(array('id !=' => 5), null, null, '_');
		$this->set('categories', $categories);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_pair_categories($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dodavatel, kterého chcete upravovat', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		$supplier = $this->Supplier->find('first', array(
			'conditions' => array('Supplier.id' => $id),
			'contain' => array(
				'SupplierCategory' => array(
					'order' => array('name' => 'asc')
				)
			)
		));
		
		if (empty($supplier)) {
			$this->Session->setFlash('Neznámý dodavatel', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		$this->set('supplier', $supplier);
		
		if (isset($this->data)) {

			$data_source = $this->Supplier->getDataSource();
			$data_source->begin($this->Supplier->SupplierCategory);

			foreach ($this->data['SupplierCategory'] as $supplier_category) {
				$old_supplier_category = $this->Supplier->SupplierCategory->find('first', array(
					'conditions' => array('SupplierCategory.id' => $supplier_category['id']),
					'contain' => array(),
					'fields' => array('SupplierCategory.category_id', 'SupplierCategory.active')	
				));

				// pokud jsem zmenil active
				if ($old_supplier_category['SupplierCategory']['active'] != $supplier_category['active']) {
					$products = $this->Supplier->Product->find('all', array(
						'conditions' => array(
							'Product.supplier_category_id' => $supplier_category['id']
						),
						'contain' => array(),
						'fields' => array('Product.id')
					));

					// pokud jsem kategorii zakazal, deaktivuju vsechny produkty, ktere jsou z teto kategorie
					// pokud jsem kategorii povolil, vsechny jeji produkty aktivuju
					foreach ($products as $product) {
						$product['Product']['active'] = $supplier_category['active'];
						if (!$this->Supplier->Product->save($product)) {
							debug($product);
							trigger_error('Produktu se nepodarilo upravit active', E_USER_NOTICE);
							die('here');
						}
					}
				}

				// pri uploadu se podivam, jestli je kategorie aktivni a pokud ne, produkt nenatahuju

				// pokud jsem zmenil naparovani
				if (empty($old_supplier_category) || ($supplier_category['category_id'] != $old_supplier_category['SupplierCategory']['category_id'])) {
					// pokud byla puvodni kategorie nenulova
					if ($old_supplier_category['SupplierCategory']['category_id'] != 0) {
						// smazu vsechny prirazeni podle stare naparovane kategorie
						$this->Supplier->Product->CategoriesProduct->deleteAll(array(
							'Product.supplier_id' => $id,
							'Product.supplier_category_id' => $supplier_category['id'],
							'CategoriesProduct.paired' => true
						));
					}
					
					if ($supplier_category['category_id'] != 0) {
						// vlozim prirazeni podle nove naparovane kategori
						$products = $this->Supplier->Product->find('all', array(
							'conditions' => array(
								'Product.supplier_id' => $id,
								'Product.supplier_category_id' => $supplier_category['id']
							),
							'contain' => array(),
							'fields' => array('Product.id')
						));
						// ke vsem produktum vlozim prirazeni do naparovane kategorie
						foreach ($products as $product) {
							$categories_product = array(
								'CategoriesProduct' => array(
									'product_id' => $product['Product']['id'],
									'category_id' => $supplier_category['category_id'],
									'paired' => true
								)
							);
							$this->Supplier->Product->CategoriesProduct->create();
							$this->Supplier->Product->CategoriesProduct->save($categories_product);
						}
					}
				}
			}
			
			// ulozim nove naparovani kategorii
			if ($this->Supplier->SupplierCategory->saveAll($this->data['SupplierCategory'])) {
				$data_source->commit($this->Supplier->SupplierCategory);
				$this->Session->setFlash('Kategorie byly spárovány', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
			} else {
				$data_source->rollback($this->Supplier->SupplierCategory);
				$this->Session->setFlash('Kategorie se nepodařilo spárovat', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data['SupplierCategory'] = $supplier['SupplierCategory'];
		}
		$old_categories_ids = $this->Supplier->SupplierCategory->Category->subtree_ids(304);
		// chci vynechat stare kategorie a korenovou kategorii
		$skipped_categories_ids = $old_categories_ids;
		$skipped_categories_ids[] = 5;
		$categories_conditions = array('Category.id NOT IN (' . implode(',', $skipped_categories_ids) . ')');
		$categories = $this->Supplier->SupplierCategory->Category->generatetreelist($categories_conditions, null, null, '_');
		$this->set('categories', $categories);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dodavatel, kterého chcete smazat', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		if ($this->Supplier->delete($id)) {
			$this->Session->setFlash('Dodavatel byl odstraněn', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Dodavatele se nepodařilo odstranit, opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
	}
	
	function upload($id = null) {
		if (!$id) {
			trigger_error('Není zadáno id uživatele', E_USER_ERROR);
			die();
		}
		
		$supplier = $this->Supplier->find('first', array(
			'conditions' => array('Supplier.id' => $id, 'Supplier.active' => true),
			'contain' => array()	
		));
		if (empty($supplier)) {
			trigger_error('Pro id ' . $id . ' neexistuje dodavatel', E_USER_ERROR);
			die();
		}
		// stahnu feed (je tam vubec)
		if (!$xml = download_url($supplier['Supplier']['url'])) {
			trigger_error('Chyba při stahování URL ' . $supplier['Supplier']['url'], E_USER_ERROR);
			die();
		}

		// ulozim si stazene xml pro pozdejsi kontrolu
		$xml_file_suffix = date('Y-m-d-H-i-s');
		$xml_file = 'files/uploads/xml-' . $id . '-' . $xml_file_suffix . '.xml';
//		file_put_contents($xml_file, $xml);
		
		$products = new SimpleXMLElement($xml);

		// otestuju, jestli ma feed spravnou strukturu
		if (!$this->Supplier->validate_feed($products, $supplier['Supplier']['price_field'])) {
			trigger_error('Feed ' . $supplier['Supplier']['url'] . ' není validní', E_USER_ERROR);
			die();
		}
		
		// otestuju, jestli se lisi od posledniho (porovnam otisky)
		$hash = md5($xml);
		// podivam se, jestli se jedna o vynuceny upload
		$force = false;
		if (isset($this->params['named']['force']) && $this->params['named']['force'] == true) {
			$force = true;
		}
		if ($supplier['Supplier']['hash'] != $hash || $force) {
			// chci si pamatovat produkty, ktere jsou v aktualnim feedu, abych pak mohl ostatni produkty vyrobce deaktivovat
			$supplier_product_ids = array();
			// prochazim produkty - poskladam si save produktu
			foreach ($products->SHOPITEM as $feed_product) {
				$product = $this->Supplier->product($feed_product, $supplier['Supplier']['discount'], $supplier['Supplier']['price_field'], $id);

				// produkt jsem v poradku vyparsoval
				if ($product) {
					$product['Product']['supplier_id'] = $id;
					// pokud mam v systemu produkt s danym id produktu ve feedu poskytovatele, budu hodnoty updatovat
					$db_product = $this->Supplier->Product->find('first', array(
						'conditions' => array(
							'Product.supplier_id' => $product['Product']['supplier_id'],
							'Product.supplier_product_id' => $product['Product']['supplier_product_id']
						),
						'contain' => array(),
					));

					$data_source = $this->Supplier->Product->getDataSource();
					$data_source->begin($this->Supplier->Product);
					// produkt uz mam v databazi
					if (!empty($db_product)) {
						$product['Product']['id'] = $db_product['Product']['id'];
						// nastavim active produktu podle active u supplier_category (nastavene v parovani kategorii)
						$product_active = false;
						$supplier_category = $this->Supplier->SupplierCategory->find('first', array(
							'conditions' => array('SupplierCategory.id' => $product['Product']['supplier_category_id']),
							'contain' => array(),
						));
						if (!empty($supplier_category)) {
							$product_active = $supplier_category['SupplierCategory']['active'];
						}
						$product['Product']['active'] = $product_active;
/* 						// musim zkontrolovat, ze vyrobce nepresunul produkt do jine kategorie
						if ($product['Product']['supplier_category_id'] != $db_product['Product']['supplier_category_id']) {
							// smazu prirazeni do kategorii (ve stromu vyrobce i naparovane)
							if (!$this->Supplier->Product->CategoriesProduct->deleteAll(array(
								'product_id' => $db_product['Product']['id'],
								'paired' => true	
							))) {
								trigger_error('Nepodarilo se odstranit stare prirazeni do naparovane kategorie', E_USER_NOTICE);
								$data_source->rollback($this->Supplier->Product);
								continue;
							}
							// ulozim prirazeni do kategorie podle naparovani kategorii (pokud to jde)
							if ($product['Product']['supplier_category_id'] != $db_product['Product']['supplier_category_id']) {
								if (!$this->Supplier->SupplierCategory->pair_product($product['Product']['supplier_category_id'], $product['Product']['id'])) {
									trigger_error('nepodarilo se ulozit nove prirazeni produktu do kategorie podle naparovani kategorii', E_USER_NOTICE);
									$data_source->rollback($this->Supplier->Product);
									continue;
								}
							}
						} */
					} else {
						$this->Supplier->Product->create();
					}

					// ulozim produkt
					if (!$this->Supplier->Product->save($product)) {
						debug($product);
						trigger_error('Nepodarilo se ulozit produkt', E_USER_NOTICE);
						$data_source->rollback($this->Supplier->Product);
						continue;
					}
					$product_id = $this->Supplier->Product->id;
					
					// ulozim url produktu
					$product_url_update = array(
						'Product' => array(
							'id' => $product_id,
							'url' => strip_diacritic($product['Product']['name']) . '-p' . $product_id
						)
					);
					if (!$this->Supplier->Product->save($product_url_update)) {
						debug($product_url_update);
						trigger_error('Nepodarilo se ulozit URL produktu', E_USER_NOTICE);
						$data_source->rollback($this->Supplier->Product);
					}
					
					// OBRAZKY
					// zjistim url obrazku
					$image_url = $this->Supplier->image_url($feed_product);

					// stahnu a ulozim obrazek, pokud je treba
					$save_image_end = $this->Supplier->image_save($product_id, $image_url);

					// pokud nenastala chyba pri ukladani obrazku, smazu vsechny obrazky u produktu, ktere jiz nejsou aktualni
					if ($save_image_end) {
						$del_images_conditions = array(
							'Image.product_id' => $product_id,
							'Image.supplier_url !=' => $image_url
						);

						$this->Supplier->Product->Image->deleteAllImages($del_images_conditions);
					}

					// KATEGORIE
					// zjistim kategorii pro produkt z feedu
					$category_id = $this->Supplier->category_id($feed_product, $id);
					if ($category_id) {
						$categories_product = array(
							'CategoriesProduct' => array(
								'category_id' => $category_id,
								'product_id' => $product_id,
								'paired' => true
							)
						);
						// podivam se, jestli mam dany produkt pridelen do dane naparovane kategorie
						$db_categories_product = $this->Supplier->Product->CategoriesProduct->find('first', array(
							'conditions' => $categories_product['CategoriesProduct'],
							'contain' => array()
						));
						// pokud nemam produkt v te kategorii
						if (empty($db_categories_product)) {
							// vlozim ho tam
							$this->Supplier->Product->CategoriesProduct->create();
							if ($this->Supplier->Product->CategoriesProduct->save($categories_product)) {
								// smazu vsechny ostatni prirazeni produktu do kategorii vznikle naparovanim produktu
								$this->Supplier->Product->CategoriesProduct->deleteAll(array(
									'CategoriesProduct.product_id' => $product_id,
									'CategoriesProduct.paired' => true,
									'CategoriesProduct.category_id !=' => $category_id
								));
							} else {
								debug($categories_product);
								trigger_error('Nepodarilo se ulozit prirazeni produktu do kategorie: ' . $product_id . ' - ' . $category_id, E_USER_NOTICE);
							}
						}
					}
					
					// CENY V CENOVYCH SKUPINACH (naplnim jen cenu pro neprihlaseneho zakaznika, zbytek pouze zalozim)
					$product_prices = array();
					$customer_types = $this->Supplier->Product->CustomerTypeProductPrice->CustomerType->find('all', array(
						'contain' => array()	
					));
					
					foreach ($customer_types as $customer_type) {
						$customer_type_product_price = array(
							'customer_type_id' => $customer_type['CustomerType']['id'],
							'product_id' => $product_id
						);
						
						$db_unlogged_customer_price = $this->Supplier->Product->CustomerTypeProductPrice->find('first', array(
							'conditions' => array($customer_type_product_price),
							'contain' => array(),	
							'fields' => array('CustomerTypeProductPrice.id')
						));

						if (!empty($db_unlogged_customer_price)) {
							$customer_type_product_price['id'] = $db_unlogged_customer_price['CustomerTypeProductPrice']['id'];
						}
						
						$customer_type_product_price['price'] = null;
						if ($customer_type['CustomerType']['id'] == 2) {
							$customer_type_product_price['price'] = $product['Product']['discount_common'];
						}
						$product_prices[] = $customer_type_product_price;
					}

					if (!$this->Supplier->Product->CustomerTypeProductPrice->saveAll($product_prices)) {
						debug($product_prices);
						trigger_error('Nepodarilo se ulozit ceny produktu', E_USER_NOTICE);
					}
					
					$data_source->commit($this->Supplier->Product);
					
					$supplier_product_ids[] = $product_id;
				} else {
					debug($feed_product);
					trigger_error('Nepodarilo se vyparsovat informace o produktu', E_USER_NOTICE);
					continue;
				}
	
			}

			// zjistim produkty dodavatele, ktere nejsou v danem feedu aktivni a deaktivuju je, aby mi v shopu nezustavaly produkty, ktere uz dodavatel nema v nabidce
			if (!empty($supplier_product_ids)) {
				$unactive_products = $this->Supplier->Product->find('all', array(
					'conditions' => array(
						'Product.supplier_id' => $id,
						'Product.active' => true,
						'Product.id NOT IN (' . implode(',', $supplier_product_ids) . ')'
					),
					'contain' => array(),
					'fields' => array('Product.id')
				));
				
				foreach ($unactive_products as $unactive_product) {
					$unactive_product['Product']['active'] = false;
					if (!$this->Supplier->Product->save($unactive_product)) {
						debug($unactive_product);
						trigger_error('Nepodarilo se deaktivovat produkty', E_USER_NOTICE);
					}
				}
			}
			
			// updatuju hash feedu poskytovatele
			$supplier_save = array(
				'Supplier' => array(
					'id' => $id,
					'hash' => md5($xml)
				)	
			);
			if (!$this->Supplier->save($supplier_save)) {
				trigger_error('Nepodařilo se uložit nový hash feedu', E_USER_NOTICE);
			}
		} else {
			trigger_error('Feed ' . $supplier['Supplier']['url'] . ' se od posledně nezměnil', E_USER_NOTICE);
			die();
		}

		// pokud se jedna o vynuceny upload z administace
		if (isset($this->params['named']['force'])) {
			// presmeruju na index dodavatelu
			$this->Session->setFlash('Upload produktů byl úspěšně dokončen', REDESIGN_PATH . 'flash_success');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index', 'admin' => 'true'));
		}
		die();
	}
	
	function admin_pair($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán výrobce, jehož produkty chcete párovat', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		$supplier = $this->Supplier->find('first', array(
			'conditions' => array('Supplier.id' => $id),
			'contain' => array()
		));
		
		if (empty($supplier)) {
			$this->Session->setFlash('Nepodařilo se najít výrobce, jehož produkty chcete párovat', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		$this->set('supplier', $supplier);

		// url feedu
		$url = $supplier['Supplier']['url'];
		// soubor s ulozenym feedem
		if (!$file = $this->Supplier->get_local_xml_path($id)) {
			trigger_error('Nepodarilo se sestavit URL lokalne ulozeneho XML vyrobce');
		}
		
		// pokud nemam stazeny feed, stahnu si ho
		if (!file_exists($file) || (isset($this->params['named']['force']) && $this->params['named']['force'] == true)) {
			$content = download_url_like_browser($url);
			if (!$content) {
				trigger_error('Nepodarilo se stahnout feed na adrese ' . $url, E_USER_ERROR);
			}
			if (!file_put_contents($file, $content)) {
				trigger_error('Nepodarilo se ulozit feed do souboru ' . $file, E_USER_ERROR);
			}
		}
		
		if (isset($this->data)) {
			foreach ($this->data['Product'] as $product) {
				if (!empty($product['supplier_product_id']) && !empty($product['supplier_product_name'])) {
					$this->Supplier->Product->save($product);
				} elseif (empty($product['supplier_product_name']) && !empty($product['supplier_product_id'])) {
					$product['supplier_product_name'] = null;
					$product['supplier_product_id'] = null;
					$this->Supplier->Product->save($product);
				}
			}
			$this->Session->setFlash('Přiřazení byla uložena.', REDESIGN_PATH . 'flash_success');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		} else {
			$product_conditions = $this->Supplier->get_product_conditions($id);
			// nactu produkty syncare, ktere mame v shopu
			$db_products = $this->Supplier->Product->find('all', array(
				'conditions' => $product_conditions,
				'contain' => array(),
				'fields' => array('Product.id', 'Product.name', 'Product.url', 'Product.supplier_product_id')
			));
			
			$xml_products_list = $this->Supplier->get_xml_products_list($id);
			foreach ($db_products as &$db_product) {
				$this->data['Product'][$db_product['Product']['id']]['id'] = $db_product['Product']['id'];
				$this->data['Product'][$db_product['Product']['id']]['name'] = $db_product['Product']['name'];
				$this->data['Product'][$db_product['Product']['id']]['supplier_product_id'] = $db_product['Product']['supplier_product_id'];
				$this->data['Product'][$db_product['Product']['id']]['supplier_product_name'] = null;
				if (isset($db_product['Product']['supplier_product_id']) && isset($xml_products_list[$db_product['Product']['supplier_product_id']])) {
					$this->data['Product'][$db_product['Product']['id']]['supplier_product_name'] = $xml_products_list[$db_product['Product']['supplier_product_id']];
				}
				$this->data['Product'][$db_product['Product']['id']]['url'] = $db_product['Product']['url'];
				// dodavatel je syncare
				$this->data['Product'][$db_product['Product']['id']]['supplier_id'] = $id;
			}
 		}

		// layout
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function xml_autocomplete_list($id) {
		$return = array(
			'success' => false,
			'message' => null,
			'data' => array()
		);
		
		$xml_products_list = $this->Supplier->get_xml_products_list($id);
		foreach ($xml_products_list as $index => $name) {
			$return['data'][] = array(
				'label' => $name,
				'value' => $index,
				'name' => $name
			);
		}
		$return['success'] = true;
		
		if (!function_exists('json_encode')) {
			App::import('Vendor', 'Services_JSON', array('file' => 'JSON.php'));
			$json = &new Services_JSON();
			echo $json->encode($return);
		} else {
			echo json_encode($return);
		}
		die();
	}
}
