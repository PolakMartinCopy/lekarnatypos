<?
class RelatedProductsController extends AppController {

	var $name = 'RelatedProducts';
	
	function admin_add() {
		if (isset($this->params['named']['product_id'])) {
			$product_id = $this->params['named']['product_id'];
		} else {
			$this->Session->setFlash('Není zadán produkt, ke kterému chcete uložit související produkty.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}

		if (isset($this->params['named']['category_id'])) {
			$category_id = $this->params['named']['category_id'];
		}
		
		if (isset($this->params['named']['related_product_id'])) {
			$save = array(
				'RelatedProduct' => array(
					'product_id' => $product_id,
					'related_product_id' => $this->params['named']['related_product_id']
				)
			);
			
			if ($this->RelatedProduct->hasAny($save['RelatedProduct'])) {
				$this->Session->setFlash('Vybraný produkt je již přiřazen k tomuto produktu.', REDESIGN_PATH . 'flash_failure');
			} else {
				$this->RelatedProduct->create();
				if ($this->RelatedProduct->save($save)) {
					$this->Session->setFlash('Související produkt byl uložen.', REDESIGN_PATH . 'flash_success');
				} else {
					$this->Session->setFlash('Související produkt se nepodařilo uložit.', REDESIGN_PATH . 'flash_failure');
				}
			}
		} else {
			$this->Session->setFlash('Není zadán související produkt.', REDESIGN_PATH . 'flash_failure');
		}
		$redirect = array('controller' => 'products', 'action' => 'edit_related', $product_id, (isset($category_id) ? $category_id : null));
		
		// pokud vim id kategorie, ze ktere jsem vybiral souvisejici produkt, vratim si jej zpet, abych obsah kategorie opet vypsal
		if (isset($this->params['named']['related_category_id'])) {
			$redirect['related_category_id'] = $this->params['named']['related_category_id'];
		}
		$this->redirect($redirect);
	}
	
	function admin_move_up($id = null, $category_id = null, $related_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán související produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$related_product = $this->RelatedProduct->find('first', array(
			'conditions' => array('RelatedProduct.id' => $id),
			'contain' => array(),
			'fields' => array('RelatedProduct.product_id')
		));
		
		if (empty($related_product)) {
			$this->Session->setFlash('Neexistující související produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		if ($this->RelatedProduct->moveUp($id)) {
			$this->Session->setFlash('Související produkt byl přesunut.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Související produkt se nepodařilo přesunout.', REDESIGN_PATH . 'flash_failure');
		}
		$redirect = array('controller' => 'products', 'action' => 'edit_related', $related_product['RelatedProduct']['product_id'], (isset($category_id) ? $category_id : null));
		if (isset($related_category_id)) {
			$redirect['related_category_id'] = $related_category_id;
		}
		$this->redirect($redirect);
	}
	
	function admin_move_down($id = null, $category_id = null, $related_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán související produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$related_product = $this->RelatedProduct->find('first', array(
			'conditions' => array('RelatedProduct.id' => $id),
			'contain' => array(),
			'fields' => array('RelatedProduct.product_id')
		));
		
		if (empty($related_product)) {
			$this->Session->setFlash('Neexistující související produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		if ($this->RelatedProduct->moveDown($id)) {
			$this->Session->setFlash('Související produkt byl přesunut.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Související produkt se nepodařilo přesunout.', REDESIGN_PATH . 'flash_failure');
		}
		$redirect = array('controller' => 'products', 'action' => 'edit_related', $related_product['RelatedProduct']['product_id'], (isset($category_id) ? $category_id : null));
		if (isset($related_category_id)) {
			$redirect['related_category_id'] = $related_category_id;
		}
		$this->redirect($redirect);
	}
	
	function admin_delete($id = null, $category_id = null, $related_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán související produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$related_product = $this->RelatedProduct->find('first', array(
			'conditions' => array('RelatedProduct.id' => $id),
			'contain' => array(),
			'fields' => array('RelatedProduct.product_id')
		));
		
		if (empty($related_product)) {
			$this->Session->setFlash('Neexistující související produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		if ($this->RelatedProduct->delete($id)) {
			$this->Session->setFlash('Související produkt byl smazán.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Související produkt se nepodařilo smazat.', REDESIGN_PATH . 'flash_failure');
		}
		$redirect = array('controller' => 'products', 'action' => 'edit_related', $related_product['RelatedProduct']['product_id'], (isset($category_id) ? $category_id : null));
		if (isset($related_category_id)) {
			$redirect['related_category_id'] = $related_category_id;
		}
		$this->redirect($redirect);
	}
	
	function admin_generate() {
		$truncate = 'TRUNCATE TABLE related_products';
		$this->RelatedProduct->query($truncate);
		// vytahnu si kategorie, kde jsou umisteny produkty urcene na prodej
		$unactive_categories_ids = $this->RelatedProduct->Product->CategoriesProduct->Category->unactive_categories_ids;
		
		// pro kazdou z techto kategorii vytahnu vsechny produkty v ni
		$categories = $this->RelatedProduct->Product->CategoriesProduct->Category->find('all', array(
			'conditions' => array(
				'Category.id NOT IN (' . implode(',', $unactive_categories_ids) . ')'
			),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
		
		$set_price = function($p) {
			$p['Product']['price'] = $p['Product']['retail_price_with_dph'];
			if (isset($p['Product']['discount_common']) && $p['Product']['discount_common'] > 0 && $p['Product']['retail_price_with_dph'] > $p['Product']['discount_common']) {
				$p['Product']['price'] = $p['Product']['discount_common'];
			}
			return $p;
		};
		
		foreach ($categories as $category) {
			$this->RelatedProduct->Product->virtualFields['price'] = $this->RelatedProduct->Product->price;
			$products = $this->RelatedProduct->Product->CategoriesProduct->find('all', array(
				'conditions' => array(
					'CategoriesProduct.category_id' => $category['Category']['id'],
					'Product.active' => true,
				),
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'INNER',
						'conditions' => array('CategoriesProduct.product_id = Product.id')
					)
				),
				'fields' => array('Product.id', 'Product.name', 'Product.retail_price_with_dph', 'Product.discount_common')
			));
			
			if (!empty($products)) {
				$products = array_map($set_price, $products);
				usort($products, array('RelatedProductsController', 'sort_by_final_price_asc'));
				
				$available_products = $this->RelatedProduct->Product->CategoriesProduct->find('all', array(
					'conditions' => array(
						'CategoriesProduct.category_id' => $category['Category']['id'],
						'Product.active' => true,
						'Availability.cart_allowed' => true
					),
					'contain' => array(),
					'joins' => array(
						array(
							'table' => 'products',
							'alias' => 'Product',
							'type' => 'INNER',
							'conditions' => array('CategoriesProduct.product_id = Product.id')
						),
						array(
							'table' => 'availabilities',
							'alias' => 'Availability',
							'type' => 'INNER',
							'conditions' => array('Product.availability_id = Availability.id')
						)
					),
					'fields' => array('Product.id', 'Product.retail_price_with_dph', 'Product.discount_common')
				));
				
				$available_products = array_map($set_price, $available_products);
				usort($available_products, array('RelatedProductsController', 'sort_by_final_price_asc'));


				// projdu produkty a pokud nema produkt pribuzne, budu ukladat pribuzne produkty podle kontextu v poli
				foreach ($products as $index => $product) {
					$related_count = $this->RelatedProduct->find('count', array(
						'conditions' => array('RelatedProduct.product_id' => $product['Product']['id']),
						'contain' => array()
					));
					
					if ($related_count == 0) {
						// zjistim pozici produktu v poli dostupnych produktu podle ceny
						$ap_index = 0;
						while (isset($available_products[$ap_index]) && $available_products[$ap_index]['Product']['price'] < $product['Product']['price']) {
							$ap_index++;
						}

						// musim kontrolovat, jestli jsem nenarazil na hranice pole!!!
						$related_products = array();
						// pokud je produkt na zacatku pole, vezmu 4 produkty za nim
						if ($ap_index == 0) {
							$rest = 4;
						// pokud je produkt na 2. pozici, vlozim jediny pred nim a dalsi 3 za nim
						} elseif ($ap_index == 1) {
							$related_products[] = $available_products[0];
							$rest = 3;
						// pokud je produkt treti od konce, vlozim do pribuznych produktu 2 produkty bezprostredne pred nim
						} elseif ($ap_index == count($available_products)-3 ) {
							if (isset($available_products[$ap_index-2])) {
								$related_products[] = $available_products[$ap_index-2];
							}
							if (isset($available_products[$ap_index-1])) {
								$related_products[] = $available_products[$ap_index-1];
							}
							$rest = 2;
						// pokud je produkt druhy od konce, vlozim do pribuznych produktu 3 produkty bezprostredne pred nim
						} elseif ($ap_index == count($available_products)-2 ) {
							if (isset($available_products[$ap_index-3])) {
								$related_products[] = $available_products[$ap_index-3];
							}
							if (isset($available_products[$ap_index-2])) {
								$related_products[] = $available_products[$ap_index-2];
							}
							if (isset($available_products[$ap_index-1])) {
								$related_products[] = $available_products[$ap_index-1];
							}
							$rest = 1;
						// pokud je produkt posledni v poli, vlozim do pribuznych produktu 4 produkty bezprostredne pred nim
						} elseif ($ap_index == count($available_products)-1 ) {
							if (isset($available_products[$ap_index-4])) {
								$related_products[] = $available_products[$ap_index-4];
							}
							if (isset($available_products[$ap_index-3])) {
								$related_products[] = $available_products[$ap_index-3];
							}
							if (isset($available_products[$ap_index-2])) {
								$related_products[] = $available_products[$ap_index-2];
							}
							if (isset($available_products[$ap_index-1])) {
								$related_products[] = $available_products[$ap_index-1];
							}
							$rest = 0;
							// jinak vlozim 2 pred produktem a 2 za nim
						} else {
							if (isset($available_products[$ap_index-2])) {
								$related_products[] = $available_products[$ap_index-2];
							}
							if (isset($available_products[$ap_index-1])) {
								$related_products[] = $available_products[$ap_index-1];
							}
							$rest = 2;
						}
						
						$j = $ap_index+1;
						$end = $j+$rest-1;
						while ($j<=(count($available_products)-1) && $j<=$end) {
							if ($available_products[$j]['Product']['id'] != $product['Product']['id']) {
								$related_products[] = $available_products[$j];
							}
							$j++;
						}
/*						debug($product);
						debug($available_products);
						debug($related_products);
						die();*/
						foreach ($related_products as $related_product) {
							$this->RelatedProduct->create();
							$save = array(
								'RelatedProduct' => array(
									'product_id' => $product['Product']['id'],
									'related_product_id' => $related_product['Product']['id']
								)
							);
							$this->RelatedProduct->save($save);
						}
					}
				}
			}
		}
		die('hotovo');
	}
	
	function sort_by_final_price_asc($a, $b){
		$a_final_price = $a['Product']['price'];
		$b_final_price = $b['Product']['price'];
		return $b_final_price < $a_final_price;
	}
}
?>