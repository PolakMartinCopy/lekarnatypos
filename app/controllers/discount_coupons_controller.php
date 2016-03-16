<?php
class DiscountCouponsController extends AppController {
	var $name = 'DiscountCoupons';
	
	function admin_index() {
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'discount_coupons') {
			$this->Session->delete('Search.AdminDiscountCouponForm');
			$this->Session->delete('Search.AdminDiscountCouponParams');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}

		$conditions = array('DiscountCoupon.active' => true);
		if (isset($this->data['AdminDiscountCouponForm']['DiscountCoupon']['search_form']) && $this->data['AdminDiscountCouponForm']['DiscountCoupon']['search_form']) {
			$this->Session->write('Search.AdminDiscountCouponForm', $this->data['AdminDiscountCouponForm']);
			$conditions = $this->DiscountCoupon->do_form_search($conditions, $this->data['AdminDiscountCouponForm']);
		} elseif ($this->Session->check('Search.AdminDiscountCouponForm')) {
			$this->data['AdminDiscountCouponForm'] = $this->Session->read('Search.AdminDiscountCouponForm');
			$conditions = $this->DiscountCoupon->do_form_search($conditions, $this->data['AdminDiscountCouponForm']);
		}
		
		$joins = array();
		if (!empty($conditions)) {
			$joins = array(
				array(
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'LEFT',
					'conditions' => array('DiscountCoupon.customer_id = Customer.id')
				),
				array(
					'table' => 'discount_coupons_products',
					'alias' => 'DiscountCouponsProduct',
					'type' => 'LEFT',
					'conditions' => array('DiscountCoupon.id = DiscountCouponsProduct.discount_coupon_id')	
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions' => array('DiscountCouponsProduct.product_id = Product.id')
				),
				array(
					'table' => 'discount_coupons_categories',
					'alias' => 'DiscountCouponsCategory',
					'type' => 'LEFT',
					'conditions' => array('DiscountCoupon.id = DiscountCouponsCategory.discount_coupon_id')	
				),
				array(
					'table' => 'categories',
					'alias' => 'Category',
					'type' => 'LEFT',
					'conditions' => array('Category.id = DiscountCouponsCategory.category_id')
				)
			);
		}
		$this->DiscountCoupon->virtualFields['customer_name'] = $this->DiscountCoupon->Customer->virtualFields['name'];
		$couponIds = $this->DiscountCoupon->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => $joins,
			'fields' => array('DISTINCT DiscountCoupon.id'),
		));
		$couponIds = Set::extract('/DiscountCoupon/id', $couponIds);

		$paginateConditions = array();
		if (!empty($conditions)) {
			$paginateConditions = array('DiscountCoupon.id' => $couponIds);
		}
		$this->paginate = array(
			'conditions' => $paginateConditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'LEFT',
					'conditions' => array('DiscountCoupon.customer_id = Customer.id')
				)
			),
			'fields' => array('*'),
			'limit' => 30
		);
		
		$coupons = $this->paginate();
		$this->set('coupons', $coupons);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán kupón, který chcete zobrazit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}
		
		$coupon = $this->DiscountCoupon->find('first', array(
			'conditions' => array(
				'DiscountCoupon.id' => $id,
				'DiscountCoupon.active' => true
			),
			'contain' => array(
				'Customer',
			)
		));
		
		if (empty($coupon)) {
			$this->Session->setFlash('Kupón, který chcete zobrazit, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}
		
		// produkty na kuponu
		$couponProducts = $this->DiscountCoupon->DiscountCouponsProduct->find('all', array(
			'conditions' => array('DiscountCouponsProduct.discount_coupon_id' => $id),
			'contain' => array(
				'Product' => array(
					'fields' => array('Product.id', 'Product.name', 'Product.url'),
					'Image' => array(
						'conditions' => array('Image.is_main' => true),
						'fields' => array('Image.id', 'Image.name')
					)
				)
			),
			'order' => array('Product.name'),
		));
		// kategorie na kuponu
		$couponCategories = $this->DiscountCoupon->DiscountCouponsCategory->find('all', array(
			'conditions' => array('DiscountCouponsCategory.discount_coupon_id' => $id),
			'contain' => array(
				'Category' => array(
					'fields' => array('Category.id', 'Category.name', 'Category.url', 'Category.image')
				)
			),
			'order' => array('Category.name'),
		));
		
		$this->set(compact('coupon', 'couponProducts', 'couponCategories'));
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if (isset($this->data)) {
			$this->DiscountCoupon->set($this->data);
			// zvaliduju zadani
			if ($this->DiscountCoupon->validates()) {
				// vyfiltruju prazdne radky
				$discountCouponsCustomers = $this->DiscountCoupon->filterCustomersData($this->data['DiscountCouponsCustomer']);
				$discountCouponsProducts = $this->DiscountCoupon->filterProductsData($this->data['DiscountCouponsProduct']);
				$discountCouponsCategories = $this->DiscountCoupon->filterCategoriesData($this->data['DiscountCouponsCategory']);
				$count = 0;
				if (empty($this->data['DiscountCoupon']['count'])) {
					$this->data['DiscountCoupon']['count'] = 1;
				}
				
				// vegeneruju zadany pocet kupony
				while ($count < $this->data['DiscountCoupon']['count']) {
					// pripravim si data
					$save['DiscountCoupon'] = array(
						'value' => $this->data['DiscountCoupon']['value'],
						'min_amount' => $this->data['DiscountCoupon']['min_amount'],
						'active' => true
					);
					if ($this->data['DiscountCoupon']['valid_until']) {
						$save['DiscountCoupon']['valid_until'] = $this->data['DiscountCoupon']['valid_until'];
					}
					if (!empty($discountCouponsProducts)) {
						$save['DiscountCouponsProduct'] = $discountCouponsProducts;
					}
					if (!empty($discountCouponsCategories)) {
						$save['DiscountCouponsCategory'] = $discountCouponsCategories;
					}
					
					$dataSource = $this->DiscountCoupon->getDataSource();
					$dataSource->begin($this->DiscountCoupon);
					$saveSuccess = true;
					// pokud mam zadane zakazniky, vygeneruju kazdemu zadany pocet kuponu
					// tzn pokud chci zadat zakazniky a kazdemu dat 1 kupon, musim v poctu kuponu nechat 1
					if (!empty($discountCouponsCustomers)) {
						foreach ($discountCouponsCustomers as $customer) {
							$save['DiscountCoupon']['customer_id'] = $customer['customer_id'];
							$save['DiscountCoupon']['name'] = $this->DiscountCoupon->generateName();
							
							if (!$this->DiscountCoupon->saveAll($save)) {
								$saveSuccess = false;
								debug($this->DiscountCoupon->validationErrors);
								$dataSource->rollback($this->DiscountCoupon);
								$this->Session->setFlash('Kupóny se nepodařilo vygenerovat, opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
								break;
							}
						}
					// nemam zadaneho zakaznika, kteremu kupon patri
					} else {
						$save['DiscountCoupon']['name'] = $this->DiscountCoupon->generateName();
						
						if (!$this->DiscountCoupon->saveAll($save)) {
							$saveSuccess = false;
							debug($this->DiscountCoupon->validationErrors);
							$dataSource->rollback($this->DiscountCoupon);
							$this->Session->setFlash('Kupóny se nepodařilo vygenerovat, opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
						}
					}
					
					$count++;
				}
				// pokud se ulozilo v poradku, commitnu save
				if ($saveSuccess) {
					$dataSource->commit($this->DiscountCoupon);
					$this->Session->setFlash('Kupóny byly vygenerovány', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
				}
			// neprosla validace
			} else {
				$this->Session->setFlash('Kupóny se nepodařilo vygenerovat, opravte chyby ve formuláři a opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
			}

		} else {
			$this->data['DiscountCoupon']['valid_until'] = date('d.m.Y', strtotime('+1 month'));
			$this->data['DiscountCoupon']['count'] = 1;
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	//upravit muzu jen neuplatneny kupon
	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán kupón, který chcete upravit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}
		
		$coupon = $this->DiscountCoupon->find('first', array(
			'conditions' => array(
				'DiscountCoupon.id' => $id,
				'DiscountCoupon.active' => true
			),
			'contain' => array(
				'Customer',
				'DiscountCouponsProduct',
				'DiscountCouponsCategory'
			)
		));
		
		if (empty($coupon)) {
			$this->Session->setFlash('Kupón, který chcete upravit, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}
		
		if ($coupon['DiscountCoupon']['order_id']) {
			$this->Session->setFlash('Kupón byl již uplatněn a nelze jej upravit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			// vyfiltruju prazdne radky
			$discountCouponsCustomers = $this->DiscountCoupon->filterCustomersData($this->data['DiscountCouponsCustomer']);
			$discountCouponsProducts = $this->DiscountCoupon->filterProductsData($this->data['DiscountCouponsProduct']);
			$discountCouponsCategories = $this->DiscountCoupon->filterCategoriesData($this->data['DiscountCouponsCategory']);
			// pripravim si data
			$save['DiscountCoupon'] = array(
				'id' => $this->data['DiscountCoupon']['id'],
				'value' => $this->data['DiscountCoupon']['value'],
				'valid_until' => $this->data['DiscountCoupon']['valid_until'],
				'min_amount' => $this->data['DiscountCoupon']['min_amount'],
			);
			if (!empty($discountCouponsProducts)) {
				$save['DiscountCouponsProduct'] = $discountCouponsProducts;
			}
			if (!empty($discountCouponsCategories)) {
				$save['DiscountCouponsCategory'] = $discountCouponsCategories;
			}				
			// pokud mam zadane zakazniky, vygeneruju kazdemu zadany pocet kuponu
			// tzn pokud chci zadat zakazniky a kazdemu dat 1 kupon, musim v poctu kuponu nechat 1
			if (!empty($discountCouponsCustomers)) {
				if (empty($discountCouponsCustomers[0]['customer_name'])) {
					$save['DiscountCoupon']['customer_id'] = 0;
				} else {
					$save['DiscountCoupon']['customer_id'] = $discountCouponsCustomers[0]['customer_id'];
				}
			}
			//debug($save); die();
			if ($this->DiscountCoupon->saveAll($save)) {
				$this->Session->setFlash('Kupón byl upraven', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Kupóny se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data['DiscountCoupon'] = $coupon['DiscountCoupon'];
			if (!empty($this->data['DiscountCoupon']['valid_until'])) {
				$this->data['DiscountCoupon']['valid_until'] = cz_date($this->data['DiscountCoupon']['valid_until'], '.');
			}
			if (!empty($coupon['Customer'])) {
				$this->data['DiscountCouponsCustomer'][1]['customer_name'] = $coupon['Customer']['name'];
				$this->data['DiscountCouponsCustomer'][1]['customer_id'] = $coupon['Customer']['id'];
			}
			if (!empty($coupon['DiscountCouponsProduct'])) {
				$discountCouponsProducts = array();
				foreach ($coupon['DiscountCouponsProduct'] as $discountCouponsProduct) {
					$product_name = $this->DiscountCoupon->DiscountCouponsProduct->Product->getFieldValue($discoutCouponsProduct['product_id'], 'name');
					// TADY TO NENI DODELANE, MUSEL BYCH MAZAT ATD. SERU NA TO, DODELAM, AZ TO BUDOU POTREBOVAT
/*					$discountCouponsProducts[] = array(
						'product_id' => $
					)*/
				}
			}
			debug($this->data);
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán kupón, který chcete smazat.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}
		// smazat muzu jen neuplatneny kupon
		$conditions = array(
			'DiscountCoupon.id' => $id,
			'DiscountCoupon.order_id' => 0
		);
		if (!$this->DiscountCoupon->hasAny($conditions)) {
			$this->Session->setFlash('Kupón, který chcete smazat, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
		}
		
		if ($this->DiscountCoupon->delete($id)) {
			$this->Session->setFlash('Kupón byl odstraněn.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Kupón se nepodařilo smazat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'discount_coupons', 'action' => 'index'));
	}
}