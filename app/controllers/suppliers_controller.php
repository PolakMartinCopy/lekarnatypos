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
			if ($this->Supplier->SupplierCategory->saveAll($this->data['SupplierCategory'])) {
				$this->Session->setFlash('Kategorie byly spárovány');
				$this->redirect(array('controller' => 'suppliers', 'action' => 'index'));
			} else {
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
			// prochazim produkty - poskladam si save produktu
			foreach ($products->SHOPITEM as $feed_product) {
				$product = $this->Supplier->product($feed_product, $supplier['Supplier']['discount'], $supplier['Supplier']['price_field']);
				// produkt jsem v poradku vyparsovat
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
					if (!empty($db_product)) {
						$product['Product']['id'] = $db_product['Product']['id'];
					} else {
						$this->Supplier->Product->create();
					}
					
				} else {
					debug($feed_product);
					trigger_error('Nepodarilo se vyparsovat informace o produktu', E_USER_NOTICE);
					continue;
				}

				$data_source = $this->Supplier->Product->getDataSource();
				$data_source->begin($this->Supplier->Product);
				
				if (!$this->Supplier->Product->save($product)) {
					debug($product);
					trigger_error('Nepodarilo se ulozit produkt', E_USER_NOTICE);
					$data_source->rollback($this->Supplier->Product);
					continue;
				}
				$product_id = $this->Supplier->Product->id;
				
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
				
				// kategorie
				// zjistim naparovanou kategorii pro produkt z feedu
				$category_id = $this->Supplier->category_id($feed_product, $id);

				$categories_product = array(
					'CategoriesProduct' => array(
						'category_id' => $category_id,
						'product_id' => $product_id
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
						// smazu vsechny ostatni prirazeni produktu do kategorii
						// TODO - produkt 
						$this->Supplier->Product->CategoriesProduct->deleteAll(array(
							'CategoriesProduct.product_id' => $product_id,
							'CategoriesProduct.category_id !=' => $category_id	
						));
					} else {
						debug($categories_product);
						trigger_error('Nepodarilo se ulozit prirazeni produktu do kategorie: ' . $product_id . ' - ' . $category_id, E_USER_NOTICE);						
					}
				}
				
				$data_source->commit($this->Supplier->Product);
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
		
		die('Upload produktů ukončen.');
	}
}