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
	}
	
	function admin_add() {
		if (isset($this->data)) {
			if ($this->Supplier->save($this->data)) {
				$this->Session->setFlash('Dodavatel byl uložen');
				$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Dodavatele se nepodařilo uložit. Opravte chyby ve formuláři a uložte jej prosím znovu');
			}
		}
		$categories = $this->Supplier->SupplierCategory->Category->generatetreelist(array('id !=' => 5), null, null, '_');
		$this->set('categories', $categories);
	}
	
	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dodavatel, kterého chcete upravovat');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		$supplier = $this->Supplier->find('first', array(
			'conditions' => array('Supplier.id' => $id),
			'contain' => array(),
		));
		
		if (empty($supplier)) {
			$this->Session->setFlash('Neznámý dodavatel');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->Supplier->save($this->data)) {
				$this->Session->setFlash('Dodavatel byl uložen');
				$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Dodavatele se nepodařilo uložit. Opravte chyby ve formuláři a uložte jej prosím znovu');
			}
		} else {
			$this->data = $supplier;
		}
		$categories = $this->Supplier->SupplierCategory->Category->generatetreelist(array('id !=' => 5), null, null, '_');
		$this->set('categories', $categories);
	}
	
	function admin_pair_categories($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dodavatel, kterého chcete upravovat');
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
			$this->Session->setFlash('Neznámý dodavatel');
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
						$this->Supplier->Product->save($product);
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
							'CategoriesProduct.is_paired' => true
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
									'is_paired' => true
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
				$this->Session->setFlash('Kategorie byly spárovány');
				$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
			} else {
				$data_source->rollback($this->Supplier->SupplierCategory);
				$this->Session->setFlash('Kategorie se nepodařilo spárovat');
			}
		} else {
			$this->data['SupplierCategory'] = $supplier['SupplierCategory'];
		}
		$categories = $this->Supplier->SupplierCategory->Category->generatetreelist(array('id !=' => 5), null, null, '_');
		$this->set('categories', $categories);
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dodavatel, kterého chcete smazat');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
		}
		
		if ($this->Supplier->delete($id)) {
			$this->Session->setFlash('Dodavatel byl odstraněn');
		} else {
			$this->Session->setFlash('Dodavatele se nepodařilo odstranit, opakujte prosím akci');
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
					
					if (!empty($db_product)) {
						$product['Product']['id'] = $db_product['Product']['id'];
						// aktivuju produkt, kdyby byl nahodou predtim deaktivovany
						$product['Product']['active'] = true;
						// musim zkontrolovat, ze vyrobce nepresunul produkt do jine kategorie
						if ($product['Product']['supplier_category_id'] != $db_product['Product']['supplier_category_id']) {
//							debug($db_product);
//							debug($product); die();
							// smazu prirazeni do kategorii (ve stromu vyrobce i naparovane)
							if (!$this->Supplier->Product->CategoriesProduct->deleteAll(array(
								'product_id' => $db_product['Product']['id'],
								'is_paired' => true	
							))) {
								trigger_error('Nepodarilo se odstranit stare prirazeni do naparovane kategorie', E_USER_NOTICE);
								$data_source->rollback($this->Supplier->Product);
								continue;
							}
							if (!$this->Supplier->Product->CategoriesProduct->deleteAll(array(
									'product_id' => $db_product['Product']['id'],
									'is_supplier' => true
							))) {
								trigger_error('Nepodarilo se odstranit stare prirazeni do kategorie ve stromu vyrobce', E_USER_NOTICE);
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
						}
					} else {
						$this->Supplier->Product->create();
					}
					
				} else {
					debug($feed_product);
					trigger_error('Nepodarilo se vyparsovat informace o produktu', E_USER_NOTICE);
					continue;
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

				$categories_product = array(
					'CategoriesProduct' => array(
						'category_id' => $category_id,
						'product_id' => $product_id,
						'is_supplier' => true
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
						// smazu vsechny ostatni prirazeni produktu do kategorii vznikle importem do korenove kategorie
						$this->Supplier->Product->CategoriesProduct->deleteAll(array(
							'CategoriesProduct.product_id' => $product_id,
							'CategoriesProduct.is_supplier' => true,
							'CategoriesProduct.category_id !=' => $category_id
						));
					} else {
						debug($categories_product);
						trigger_error('Nepodarilo se ulozit prirazeni produktu do kategorie: ' . $product_id . ' - ' . $category_id, E_USER_NOTICE);						
					}
				}
				$data_source->commit($this->Supplier->Product);

				$supplier_product_ids[] = $product['Product']['id'];
			}

			// zjistim produkty dodavatele, ktere nejsou v danem feedu aktivni a deaktivuju je, aby mi v shopu nezustavaly produkty, ktere uz dodavatel
			// nema v nabidce
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
			$this->Session->setFlash('Upload produktů byl úspěšně dokončen');
			$this->redirect(array('controller' => 'suppliers', 'action' => 'index', 'admin' => 'true'));
		}
		die();
	}
	
	function admin_repair_categories_products($id) {
		$categories_products = $this->Supplier->Product->CategoriesProduct->find('all', array(
			'conditions' => array(
				'Product.supplier_id' => $id,
			),
			'contain' => array('Product'),
			'fields' => array('CategoriesProduct.id')
		));
		foreach ($categories_products as $categories_product) {
			$categories_product['CategoriesProduct']['is_supplier'] = true;
			$this->Supplier->Product->CategoriesProduct->save($categories_product);
		}
		die('konec');
	}
}