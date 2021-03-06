<?php
class ProductsController extends AppController {

	var $name = 'Products';
	var $helpers = array('Html', 'Form', 'Javascript');

	var $paginate = array(
		'limit' => 10,
	);
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->product_types = $this->Product->product_types;
	}
	
	function index($category_id = null) {
		$this->Product->recursive = 0;
		$this->set('products', $this->paginate());
	}

	function view($id = null) {
		// kontrola, zda ctu produkt, ktery vubec existuje
		if (!$this->Product->hasAny(array('Product.id' => $id))) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->cakeError('error404');
		}

		// osetruju pokus o vlozeni do kosiku
		if (isset($this->data['Product'])) {
			// vkladam vyberem z vypisu vsech moznosti
			if (isset($this->data['Subproduct']) && !empty($this->data['Subproduct'])) {
				// zjistim, kterou variantu produktu vlastne do kosiku vkladam
				foreach ($this->data['Subproduct'] as $index => $subproduct) {
					if (isset($subproduct['chosen'])) {
						break;
					}
				}
				
				$new_data['CartsProduct']['quantity'] = $this->data['Subproduct'][$index]['quantity'];
				$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
				$new_data['CartsProduct']['subproduct_id'] = $this->data['Subproduct'][$index]['id'];
			} elseif (isset($this->data['Subproduct']['quantity'])) {
				// vkladam do kosiku produkt bez variant
				$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
				$new_data['CartsProduct']['quantity'] = $this->data['Subproduct']['quantity'];
			} else {
				// vkladam do kosiku produkt z vypisu produktu v kategorii a vyhledavani
				// produkt chci do kosiku vlozit pouze v pripade, ze nema zadne varianty
				if (!$this->Product->Subproduct->hasAny(array('Subproduct.product_id' => $this->data['Product']['id']))) {
					$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
					$new_data['CartsProduct']['quantity'] = $this->data['Product']['quantity'];
					if (isset($_SERVER['HTTP_REFERER'])) {
						$category_url = $_SERVER['HTTP_REFERER'];
					}
				} else {
					$this->Session->setFlash('Produkt se nepodařilo vložit do košíku. Nejprve prosím <a href="' . $_SERVER['REQUEST_URI'] . '#AddProductWithVariantsForm">vyberte variantu produktu</a>.', REDESIGN_PATH . 'flash_failure');
					$this->redirect($_SERVER['REQUEST_URI']);
				}
			}
			
			if (isset($new_data)) {
				$this->data = $new_data;
				
				$result = $this->Product->requestAction('carts_products/add', $this->data);
				// vlozim do kosiku
				if ( $result ){
					$this->Session->setFlash('Produkt byl uložen do nákupního košíku. Obsah Vašeho košíku si můžete zobrazit <a href="/kosik">zde</a>.', REDESIGN_PATH . 'flash_success');
					if (isset($category_url)) {
						$url = $category_url;
					} else {
						$url = '/' . $this->Product->getFieldValue($this->data['CartsProduct']['product_id'], 'url');
					}
					$this->redirect($url);
				} else {
					$this->Session->setFlash('Vložení produktu do košíku se nezdařilo. Zkuste to prosím znovu.', REDESIGN_PATH . 'flash_failure');
				}
			}
		}

		// navolim si layout stranky
		$this->layout = REDESIGN_PATH . 'content';
		
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		$this->Product->virtualFields['price'] = $this->Product->price;
		$this->Product->virtualFields['discount'] = $this->Product->discount;
		// vyhledam si info o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $id,
				'Product.price >' => 0
			),
			'contain' => array(
				'CategoriesProduct' => array(
					'Category' => array(
						'fields' => array('id', 'name', 'url')
					)
				),
				'Image' => array(
					'order' => array(
						'is_main' => 'desc'
					),
					'fields' => array('id', 'name')
				),
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
				'Availability' => array(
					'fields' => array('Availability.id', 'Availability.name', 'Availability.color', 'Availability.cart_allowed')
				),
				'TaxClass' => array(
					'fields' => array('id', 'value')
				),
				'Comment' => array(
					'conditions' => array('Comment.confirmed' => true),
					'fields' => array('Comment.id', 'Comment.subject', 'Comment.body', 'Comment.author', 'Comment.created', 'Comment.reply'),
					'order' => array('Comment.created' => 'desc'),
					'Administrator'
				),
				'ProductType' => array(
					'fields' => array('ProductType.id', 'ProductType.text', 'ProductType.name')
				),
				'Subproduct' => array(
					'fields' => array('id'),
					'AttributesSubproduct' => array(
						'fields' => array('id'),
						'Attribute' => array(
							'fields' => array('id', 'value'),
							'Option' => array(
								'fields' => array('id', 'name')
							)
						)
					)
				)
			),
			'fields' => array(
				'Product.id',
				'Product.title',
				'Product.description',
				'Product.name',
				'Product.breadcrumb',
				'Product.heading',
				'Product.url',
				'Product.short_description',
				'Product.product_type_id',
				'Product.note',
				'Product.retail_price_with_dph',
				'Product.price',
				'Product.discount',
				'Product.rate',
				'Product.video',
				'Product.note',
				'Product.active',
				'Product.code',
				'Product.ean',
				'Product.sukl',
				'Product.group',
				'Product.is_akce',
				'Product.is_novinka',
				'Product.is_doprodej',
				'Product.is_bestseller',
				'Product.is_darek_zdarma',
				'Product.supplier_id',
				'Product.is_alliance_rewritten'
			),
			'joins' => array(
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
				),
			),
			'group' => array('Product.id')
		));
		unset($this->Product->virtualFields['price']);
		unset($this->Product->virtualFields['discount']);

		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->cakeError('error404');
		}
		
		// pokud je produkt z PDK a nema upraveny popis (tzn v dlouhem popisu je adresa do PDK)
		if (in_array($product['Product']['supplier_id'], array(4, 5)) && !$product['Product']['is_alliance_rewritten']) {
			$description_image_content = download_url_like_browser($product['Product']['description']);
			$product['Product']['description'] = '<img src="data:image/jpeg;base64,' . base64_encode($description_image_content) . '" />';
		}
		
		$product['Product']['free_shipping_min_quantity'] = $this->Product->minQuantityFreeShipping($product['Product']['id']);
		$product['Manufacturer']['url'] = $this->Product->Manufacturer->get_url($product['Manufacturer']['id']);
		
		$this->set('product', $product);
		$this->set('opened_manufacturer_id', $product['Manufacturer']['id']);
		
		// SPRAVA VARIANT PRODUKTU
		$subproducts = array();
		$subproduct_conditions = array('Subproduct.product_id' => $id, 'Subproduct.active' => true);
		
		$sorted_attributes = $this->Product->Subproduct->find('all', array(
			'conditions' => $subproduct_conditions,
			'contain' => array(),
			'fields' => array('Attribute.id'),
			'joins' => array(
				array(
					'table' => 'attributes_subproducts',
					'alias' => 'AttributesSubproduct',
					'type' => 'LEFT',
					'conditions' => array('Subproduct.id = AttributesSubproduct.subproduct_id')
				),
				array(
					'table' => 'attributes',
					'alias' => 'Attribute',
					'type' => 'LEFT',
					'conditions' => array('Attribute.id = AttributesSubproduct.attribute_id')
				)
			),
			'order' => array('Attribute.option_id', 'Attribute.value'),
		));

		// vytahnu si serazeny idcka atributu
		$sorted_attribute_ids = Set::extract('/Attribute/id', $sorted_attributes);
		
		// zjistim si idcka subproduktu, serazeny podle jejich prislusnosti k atributum
		$sorted_subproducts = $this->Product->Subproduct->AttributesSubproduct->find('all', array(
			'conditions' => array(
				'AttributesSubproduct.attribute_id' => $sorted_attribute_ids,
				'Subproduct.product_id' => $id
			),
			'fields' => array('AttributesSubproduct.*', "FIELD(attribute_id, '" . implode("', '", $sorted_attribute_ids) . "') AS sort_order"),
			'order' => array('sort_order' => 'asc')
		));
		// vytahnu si idcka subproduktu, ktere jsou serazeny podle vyse uvedenych podminek
		$sorted_subproduct_ids = Set::extract('/AttributesSubproduct/subproduct_id', $sorted_subproducts);
		// odstranim duplicity
		$sorted_subproduct_ids = array_unique($sorted_subproduct_ids);
		// nactu subprodukty, ktery maji serazeny attributes_subprodukty vzdycky ve stejnym poradi
		// zaroven subprodukty jsou serazeny podle option_id a sort_order u atributu, takze subprodukty ve vypisu budou serazeny
		// pokud produkt nema subprodukty, musim zrusit order, protoze pole sorted_subproduct_ids je prazdne
		$find = array(
			'conditions' => $subproduct_conditions,
			'contain' => array(
				'AttributesSubproduct' => array(
					'order' => array('sort_order' => 'asc'),
					'fields' => array('AttributesSubproduct.*', "FIELD(attribute_id, '" . implode("', '", $sorted_attribute_ids) . "') AS sort_order"),
					'Attribute' => array(
						'Option'
					)
				),
				'Availability'
			)
		);
		if (!empty($sorted_subproduct_ids)) {
			$find['order'] = array('FIELD(Subproduct.id, ' . implode(',', $sorted_subproduct_ids) . ')' => 'asc');
		}
		$subproducts = $this->Product->Subproduct->find('all', $find);
		$this->set('subproducts', $subproducts);
	
		$this->set('_title', $product['Product']['title']);
		$this->set('_description', $product['Product']['short_description']);

		// z infa o produktu si vytahnu ID otevrene kategorie
		$opened_category_id = ROOT_CATEGORY_ID;
		if (!empty($product['CategoriesProduct'])) {
			// idcka kategorii, do kterych je produkt zarazen
			$category_ids = Set::extract('/category_id', $product['CategoriesProduct']);
			// breadcrumbs sestavuju v zavislosti na tom, v jakem podstromu se prave nachazim (jestli mam v levem sidebaru otevrene KATEGORIE nebo CO VAS TRAPI)
			$subtree_root_id = $this->Product->CategoriesProduct->Category->category_subtree_root_id;
			$categories_bothers_tab = $this->Session->read('categories_bothers_tab');
			if ($categories_bothers_tab == 'bothers') {
				$subtree_root_id = $this->Product->CategoriesProduct->Category->bothers_subtree_root_id;
			}
			$wanted_category_ids = $this->Product->CategoriesProduct->Category->subtree_ids($subtree_root_id);
			// aktualne otevrenou kategorii chci vybrat pouze z aktivnich, verejnych kategorii, ktere nejsou ve stromu horizontalniho menu
			// zjistim neaktivni kategorie a jejich podstromy
			$not_active_categories = $this->Product->CategoriesProduct->Category->find('all', array(
				'conditions' => array('Category.active' => false),
				'contain' => array(),
				'fields' => array('Category.id')
			));
			$not_active_categories_ids = array();
			foreach ($not_active_categories as $not_active_category) {
				$not_active_categories_ids = array_merge($not_active_categories_ids, $this->Product->CategoriesProduct->Category->subtree_ids($not_active_category['Category']['id']));
			}
			
			$opened_category_id_conditions = array(
				'Category.public' => true,
			);
			if (!empty($not_active_categories_ids)) {
				$opened_category_id_conditions[] = 'Category.id NOT IN (' . implode(',', $not_active_categories_ids) . ')';
			}
			if (!empty($category_ids)) {
				$opened_category_id_conditions[] = 'Category.id IN (' . implode(',', $category_ids) . ')';
			}
			if (!empty($wanted_category_ids)) {
				$opened_category_id_conditions[] = 'Category.id IN (' . implode(',', $wanted_category_ids) . ')';
			}
			
			$opened_category_id = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => $opened_category_id_conditions,
				'contain' => array(),
				'fields' => array('Category.id')
			));
			if (!empty($opened_category_id)) {
				$opened_category_id = $opened_category_id['Category']['id'];
			}
		}
		$this->set('opened_category_id', $opened_category_id);
		
		// sestavim breadcrumbs
		$path = $this->Product->CategoriesProduct->Category->getPath($opened_category_id);
		$breadcrumbs = array();
		if ($path) {
			unset($path[1]);
			foreach ($path as $item) {
				$breadcrumb = array('anchor' => $item['Category']['breadcrumb'], 'href' => '/' . $item['Category']['url']);
				if ($item['Category']['id'] == 5) {
					$breadcrumb = array('anchor' => 'Domů', 'href' => HP_URI);
				}
				$breadcrumbs[] = $breadcrumb;
			}
		}
		$breadcrumbs[] = array('anchor' => $product['Product']['breadcrumb'], 'href' => '/' . $product['Product']['url']);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// zapnu fancybox
		// $this->set('fancybox', true);
		
		// SOUVISEJICI PRODUKTY
		$similar_products = $this->Product->similar_products($id, $customer_type_id);
		$this->set('similar_products', $similar_products);
		
		// naposledy navstivene produkty
		$stack = $this->Session->read('ProductStack');
		$stack_products_ids = Set::extract('/Product/id', $stack);
		// najdu produkt, ktery zakaznik navstivil
		$this->Product->virtualFields['price'] = $this->Product->price;
		$this->Product->virtualFields['discount'] = $this->Product->discount;
		$order = 'FIELD(Product.id, ' . implode(',', $stack_products_ids) . ')';
		if (empty($stack_products_ids)) {
			$order = array();
		}
		$last_visited_products = $this->Product->find('all', array(
			'conditions' => array('Product.id' => $stack_products_ids),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.price',
				'Product.discount',
							
				'Image.id',
				'Image.name'
			),
			'joins' => array(
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id AND Image.is_main = 1')
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
				),
			),
			'order' => $order
		));
		unset($this->Product->virtualFields['price']);
		unset($this->Product->virtualFields['discount']);
		$this->set('last_visited_products', $last_visited_products);
		
		// updatuju zasobnik v sesne, kde mam ulozenych x naposled navstivenych produktu
		$stack = $this->Product->update_stack($stack, $id, $customer_type_id);
		$this->Session->write('ProductStack', $stack);
	}
	
	/**
	 * hodnoceni produktu pomoci hvezdicek
	 */
	function rate() {
		$return = array(
			'success' => false,
			'message' => ''
		);
		if (!isset($_POST['rate']) || !isset($_POST['id'])) {
			$return['message'] = 'Nepodařilo se uložit Vaše hodnocení.';
		} else {
			$product_id = $_POST['id'];
			$rate = $_POST['rate'];
			
			$product = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $product_id),
				'contain' => array(),
				'fields' => array('Product.id', 'Product.overall_rate', 'Product.voted_count')
			));
			
			if (empty($product)) {
				$return['message'] = 'Produkt, který chcete hodnotit, neexistuje.';
			} else {
				$product['Product']['overall_rate'] += $rate;
				$product['Product']['voted_count']++;
				
				if ($this->Product->save($product)) {
					$return['message'] = 'Vaše hodnocení bylo uloženo, děkujeme.';
					$return['success'] = true;
				} else {
					$return['message'] = 'Hodnocení se nepodařilo uložit, zkuste to prosím znovu.';
				}
			}
		}
		
		echo json_encode($return);
		die();
	}
	
	function admin_index() {
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'products') {
			$this->Session->delete('Search.AdminProductForm');
			$this->Session->delete('Search.AdminProductParams');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$products = array();
		$conditions = null;

		// z indexu kategorii se muzu prokliknout pres id kategorie, takze pokud to tak udelam, nastavim do fitru danou
		// kategorii a ostatni hodnoty ve filtru vynuluju
		if (isset($this->params['named']['category_id'])) {
			$this->data['AdminProductForm']['Product']['search_form'] = true;
			$this->data['AdminProductForm']['Category']['id'] = $this->params['named']['category_id'];
			$this->Session->delete('Search.AdminProductForm');
			$this->Session->delete('Search.AdminProductParams');
		}

		if (isset($this->data['AdminProductForm']['Product']['search_form']) && $this->data['AdminProductForm']['Product']['search_form']) {
			$this->Session->write('Search.AdminProductForm', $this->data['AdminProductForm']);
			$conditions = $this->Product->do_form_search($conditions, $this->data['AdminProductForm']);
		} elseif ($this->Session->check('Search.AdminProductForm')) {
			$this->data['AdminProductForm'] = $this->Session->read('Search.AdminProductForm');
			$conditions = $this->Product->do_form_search($conditions, $this->data['AdminProductForm']);
		}

		$page = 1;
		if (isset($this->params['named']['page'])) {
			$page = $this->params['named']['page'];
			$this->Session->write('Search.AdminProductParams.page', $page);
		} else {
			$page = $this->Session->read('Search.AdminProductParams.page');
		}
		
		$sort = false;
		if (isset($this->params['named']['sort'])) {
			$sort = $this->params['named']['sort'];
			$this->Session->write('Search.AdminProductParams.sort', $sort);
		} else {
			$sort = $this->Session->read('Search.AdminProductParams.sort');
		}
		
		$direction = 'asc';
		if ($sort && isset($this->params['named']['direction'])) {
			$direction = $this->params['named']['direction'];
			$this->Session->write('Search.AdminProductParams.direction', $direction);
		} else {
			$direction = $this->Session->read('Search.AdminProductParams.direction');
		}

		$joins = array(
			array(
				'table' => 'categories_products',
				'alias' => 'CategoriesProduct',
				'type' => 'LEFT',
				'conditions' => array('Product.id = CategoriesProduct.product_id')
			),
			array(
				'table' => 'manufacturers',
				'alias' => 'Manufacturer',
				'type' => 'LEFT',
				'conditions' => array('Manufacturer.id = Product.manufacturer_id')
			),
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'LEFT',
				'conditions' => array('Product.availability_id = Availability.id')
			)
		);

		$this->paginate['limit'] = 50;
		$this->paginate['page'] = $page;
		if ($sort && $direction) {
			$this->paginate['order'] = array($sort => $direction);
		}
		if (isset($this->data['AdminProductForm']['Product']['search_property_id']) && $this->data['AdminProductForm']['Product']['search_property_id'] == 6) {
			$this->paginate['order'] = array('Product.ean' => 'asc');
		}
		$this->paginate['conditions'] = $conditions;
		$this->paginate['contain'] = array();
		$this->paginate['joins'] = $joins;
		$this->paginate['fields'] = array(
			'DISTINCT Product.id',
			'Product.name',
			'Product.url',
			'Product.active',
			'Product.priority',
			'Manufacturer.name',
			'Availability.cart_allowed',
		);
		$products = $this->paginate();
		// zjistim, jestli jsou produkty prirazeny do kategorii			
		foreach ($products as &$product) {
			$categories_product = $this->Product->CategoriesProduct->find('first', array(
				'conditions' => array('CategoriesProduct.product_id' => $product['Product']['id']),
				'contain' => array(),
				'fields' => array('CategoriesProduct.id')
			));
			if (!empty($categories_product)) {
				$product['CategoriesProduct'] = $categories_product['CategoriesProduct'];
			}
		}

		$this->set('products', $products);

		$categories = $this->Product->CategoriesProduct->Category->generateAllPaths(true);
		$categories = Set::combine($categories, '{n}.Category.id', '{n}.Category.path');
		$this->set('categories', $categories);
		
		$search_properties = $this->Product->search_properties;
		$search_properties = Set::combine($search_properties, '{n}.id', '{n}.name');
		$this->set('search_properties', $search_properties);
		
		$is_alliance = array(0 => 'nerozhoduje' , 1 => 'není', 2 => 'je');
		$this->set('is_alliance', $is_alliance);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	// zpracovani hromadnych operaci na seznamu produktu
	function admin_bulk_process() {
		if (isset($this->data)) {
			// pro ktere produkty
			$product_ids = array_filter($this->data['Product']['check']);
			if (empty($product_ids)) {
				$this->Session->setFlash('Vyberte produkty pro hromadné zpracování', REDESIGN_PATH . 'flash_failure');
			} else {
				// co chci delat
				$action = $this->data['Product']['BulkProcess']['action'];
				if (!$action) {
					$this->Session->setFlash('Vyberte akci pro hromadné zpracování', REDESIGN_PATH . 'flash_failure');
				} else {
					switch ($action) {
						// aktivovat vybrane produkty
						case 'activate':
							$products = array();
							foreach ($product_ids as $product_id) {
								$products[] = array(
									'id' => $product_id,
									'active' => true
								);
							}
							if ($this->Product->saveAll($products)) {
								$this->Session->setFlash('Produkty byly aktivovány', REDESIGN_PATH . 'flash_success');
							} else {
								$this->Session->setFlash('Produkty se nepodařilo aktivovat', REDESIGN_PATH . 'flash_failure');
							}
							break;
						// deaktivovat vybrane produkty
						case 'deactivate':
							$products = array();
							foreach ($product_ids as $product_id) {
								$products[] = array(
									'id' => $product_id,
									'active' => false
								);
							}
							if ($this->Product->saveAll($products)) {
								$this->Session->setFlash('Produkty byly deaktivovány', REDESIGN_PATH . 'flash_success');
							} else {
								$this->Session->setFlash('Produkty se nepodařilo deaktivovat', REDESIGN_PATH . 'flash_failure');
							}
							break;
						case 'delete':
							$flashes = array();
							foreach ($product_ids as $product_id) {
								$subproducts = $this->Product->Subproduct->find('all', array(
									'conditions' => array('Subproduct.product_id' => $product_id),
									'contain' => array(),
									'fields' => array('Subproduct.id')
								));
								
								// musim vymazat vsechny subprodukty a obrazky
								foreach ($subproducts as $subproduct) {
									$this->Product->Subproduct->AttributesSubproduct->deleteAll(array('subproduct_id' => $subproduct['Subproduct']['id']));
									$this->Product->Subproduct->delete($subproduct['Subproduct']['id']);
								}
								
								$this->Product->Image->deleteAllImages($product_id);
								$this->Product->ProductDocument->deleteAllDocuments($product_id);
								

								if (!$this->Product->delete($product_id)) {
									$flashes[] = 'Produkt ' . $product_id . ' se nepodařilo smazat ze systému';
								}
							}
							$flash_type = 'flash_failure';
							if (empty($flashes)) {
								$flashes[] = 'Produkty byly smazány ze systému';
								$flash_type = 'flash_success';
							}
							$this->Session->setFlash(implode('<br/>', $flashes), REDESIGN_PATH . $flash_type);
							break;
						// presunout vybrane produkty (smaze vsechna puvodni prirazeni do kategorii a vytvori jedno nove do zadane kategorie
						case 'move':
							if (isset($this->data['Product']['BulkProcess']['category_id']) && !empty($this->data['Product']['BulkProcess']['category_id'])) {
								$category_id = $this->data['Product']['BulkProcess']['category_id'];
								$data_source = $this->Product->getDataSource();
								$this->Product->begin($this->Product);
								if ($this->Product->CategoriesProduct->deleteAll(array('product_id' => $product_ids))) {
									foreach ($product_ids as $product_id) {
										$categories_products[] = array(
											'product_id' => $product_id,
											'category_id' => $category_id
										);
									}

									if ($this->Product->CategoriesProduct->saveAll($categories_products)) {
										$this->Product->commit($this->Product);
										$this->Session->setFlash('Produkty byly přesunuty', REDESIGN_PATH . 'flash_success');
									} else {
										$this->Product->rollback($this->Product);
										$this->Session->setFlash('Produkty se nepodařilo přesunout', REDESIGN_PATH . 'flash_failure');
									}
								} else {
									$this->Product->rollback($this->Product);
									$this->Session->setFlash('Produkty se nepodařilo přesunout', REDESIGN_PATH . 'flash_failure');
								}
							} else {
								$this->Session->setFlash('Není zadáno, do které kategorie chcete produkty přesunout', REDESIGN_PATH . 'flash_failure');
							}
							break;
						// kopirovat vybrane produkty
						case 'copy':
							if (isset($this->data['Product']['BulkProcess']['category_id']) && !empty($this->data['Product']['BulkProcess']['category_id'])) {
								$category_id = $this->data['Product']['BulkProcess']['category_id'];
								$categories_products = array();
								foreach ($product_ids as $product_id) {
									$categories_products[] = array(
										'product_id' => $product_id,
										'category_id' => $category_id
									);
								}
								if ($this->Product->CategoriesProduct->saveAll($categories_products)) {
									$this->Session->setFlash('Produkty byly zkopírovány', REDESIGN_PATH . 'flash_success');
								} else {
									$this->Session->setFlash('Produkty se nepodařilo zkopírovat', REDESIGN_PATH . 'flash_failure');
								}
							} else {
								$this->Session->setFlash('Není zadáno, do které kategorie chcete produkty zkopírovat', REDESIGN_PATH . 'flash_failure');
							}
							break;
						// duplikovat vybrane produkty
						case 'clon':
							if (isset($this->data['Product']['BulkProcess']['category_id']) && !empty($this->data['Product']['BulkProcess']['category_id'])) {
								$category_id = $this->data['Product']['BulkProcess']['category_id'];
								$flashes = array();
								foreach ($product_ids as $product_id) {
									// nactu si data produktu
									$product = $this->Product->find('first', array(
										'conditions' => array('Product.id' => $product_id),
										'contain' => array(
											'CustomerTypeProductPrice',
											'Subproduct' => array(
												'AttributesSubproduct'
											),
											'Image'
										)
									));
	
									if (empty($product)) {
										$flashes[] = 'Produkt s ID ' . $product_id . ', který chcete duplikovat, neexistuje';
									} else {
	
										// zalozim produkt
										unset($this->Product->id);
										unset($product['Product']['id']);
										// zalozim ho jako neaktivni, at mi hned neskace v obchode
										$product['Product']['active'] = false;
										
										if ($this->Product->save($product)) {
											// mam ulozeny produkt, musim zmenit URL produktu podle noveho ID
											if ($new_url = $this->Product->buildUrl($product)) {
												// ulozim URL pro duplikovany produkt
												if ($this->Product->save(array('url' => $new_url), false)) {
													// zduplikuju obrazky
													if ($this->Product->copy_images($this->Product->id, $product['Image']) !== true) {
														$flashes[] = $result;
													} else {
														// zaradim produkt do kategorie
														$categories_product = array(
															'CategoriesProduct' => array(
																'product_id' => $this->Product->id,
																'category_id' => $category_id
															)
														);
														$this->Product->CategoriesProduct->create();
														if (!$this->Product->CategoriesProduct->save($categories_product)) {
															$flashes[] = 'Nepodařilo se zařadit produkt ' . $this->Product->id . ' do nové kategorie.';;
														}
														
														// zduplikuju ceny produktu
														foreach ($product['CustomerTypeProductPrice'] as $ctpp) {
															unset($ctpp['id']);
															$ctpp['product_id'] = $this->Product->id;
															$this->Product->CustomerTypeProductPrice->create();
															if (!$this->Product->CustomerTypeProductPrice->save($ctpp)) {
																$flashes[] = 'Nepodařilo se vytvořit ceny u produktu' . $this->Product->id . '.';
															}
														}
									
														// zkopiruju si subprodukty
														if ( !empty($product['Subproduct']) ){
															foreach( $product['Subproduct'] as $sp ){
																$sp_data = array(
																	'product_id' => $this->Product->id,
																	'price_with_dph' => $sp['price_with_dph'],
																	'active' => $sp['active'],
																	'availability_id' => $sp['availability_id']
																);
																unset($this->Product->Subproduct->id);
																if ( !$this->Product->Subproduct->save($sp_data) ){
																	$flashes[] = 'Nepodařilo se duplikovat subproduct ID ' . $sp['id'] . ' produktu ' . $this->Product->id . '.';
																} else {
																	// musim nakopirovat i vztahy mezi subprodukty a atributy
																	foreach ($sp['AttributesSubproduct'] as $att_sp) {
																		$att_sp_data = array(
																			'attribute_id' => $att_sp['attribute_id'],
																			'subproduct_id' => $this->Product->Subproduct->id
																		);
																		unset($this->Product->Subproduct->AttributesSubproduct->id);
																		if (!$this->Product->Subproduct->AttributesSubproduct->save($att_sp_data)) {
																			$flashes[] = 'Nepodařilo se duplikovat vztah mezi atributem a subproduktem ID ' . $att_sp['id'] . ' produktu ' . $this->Product->id;
																		}
																	}
																}
															}
														}
													}
												}
											} else {
												$flashes[] = 'Nepodařilo se vygenerovat URL produktu ' . $this->Product->id;
											}
										} else {
											debug($product);
											$flashes[] = 'Chyba při zakládání produktu.';
										}
									}
								}
							} else {
								$flashes[] = 'Není zadáno, do které kategorie chcete produkty zduplikovat';
							}
							$flash_type = 'flash_failure';
							if (empty($flashes)) {
								$flashes[] = 'Produkty byly úspěšně duplikovány';
								$flash_type = 'flash_success';
							}
							$this->Session->setFlash(implode('<br/>', $flashes), REDESIGN_PATH . $flash_type);
							break;
						default:
							$this->Session->setFlash('Není definována logika pro tuto operaci hromadného zpracování', REDESIGN_PATH . 'flash_failure');
							break;
					}
				}
			}
		} else {
			$this->Session->setFlash('Zadejte vstup pro hromadné zpracování', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'products', 'action' => 'index'));
	}

	function admin_add() {
		
		if (!empty($this->data)) {
			foreach ($this->data['CategoriesProduct'] as $index => $data) {
				if ($data['category_id'] == 0) {
					unset($this->data['CategoriesProduct'][$index]);
				}
			}
			
			$this->data['FreeShippingProduct'] = $this->Product->generateFreeShipping($this->data);
			if (empty($this->data['FreeShippingProduct'])) {
				unset($this->data['FreeShippingProduct']);
			}

			// ukladam produkt
			if ($this->Product->saveAll($this->data)) {
				// k produktu si ulozim id pro export do pohody
				if (empty($this->data['Product']['pohoda_id'])) {
					$save = array(
						'Product' => array(
							'id' => $this->Product->id,
							'pohoda_id' => $this->Product->id
						)
					);
					if (!$this->Product->save($save)) {
						$this->Session->setFlash('Produkt byl uložen, ale nepodařilo se uložit pohoda_id.', REDESIGN_PATH . 'flash_failure');
						$this->redirect(array('controller' => 'products', 'action' => 'index', 'category_id' => $category_id));
					}
				}
				$this->Session->setFlash('Produkt byl uložen.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'products', 'action' => 'index', 'category_id' => $category_id));
			} else {
				$this->Session->setFlash('Produkt nemohl být uložen.', REDESIGN_PATH . 'flash_failure');
			}
		}

		$manufacturers = $this->Product->Manufacturer->find('list', array(
			'conditions' => array('Manufacturer.active' => true),
			'fields' => array('Manufacturer.id', 'Manufacturer.name'),
			'order' => array('Manufacturer.name' => 'asc')
		));
		$taxClasses = $this->Product->TaxClass->find('list');
		$availabilities = $this->Product->Availability->find('list', array(
			'conditions' => array('Availability.active' => true),
			'order' => array('Availability.order' => 'asc')
		));
		
		$customer_types = $this->Product->CustomerTypeProductPrice->CustomerType->find('all', array(
			'contain' => array(),
			'fields' => array('CustomerType.id', 'CustomerType.name')
		));
		$productTypes =  $this->Product->ProductType->find('list');
		$tiny_mce_elements = 'ProductDescription';

		$this->set(compact('category', 'manufacturers', 'taxClasses', 'tinyMce', 'availabilities', 'customer_types', 'productTypes', 'tiny_mce_elements'));
		
		if (!isset($this->data)) {
			$this->data = array(
				'Product' => array(
					'feed' => true,
					'priority' => 0,
					'active' => true
				)
			);
		}
		
		$categories = $this->Product->CategoriesProduct->Category->generateAllPaths(true);
		$categories = Set::combine($categories, '{n}.Category.id', '{n}.Category.path');
		$this->set('categories', $categories);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit_detail($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.heading',
				'Product.breadcrumb',
				'Product.related_name',
				'Product.zbozi_name',
				'Product.heureka_name',
				'Product.heureka_extended_name',
				'Product.heureka_category',
				'Product.short_description',
				'Product.description',
				'Product.active',
				'Product.note',
				'Product.manufacturer_id',
				'Product.availability_id',
				'Product.product_type_id',
				'Product.tax_class_id',
				'Product.wholesale_price',
				'Product.recycle_fees',
				'Product.discount',
				'Product.guarantee',
				'Product.priority',
				'Product.weight',
				'Product.video',
				'Product.is_akce',
				'Product.is_novinka',
				'Product.is_doprodej',
				'Product.is_bestseller',
				'Product.is_darek_zdarma',
				'Product.feed',
				'Product.title',
				'Product.keywords',
				'Product.pohoda_id',
				'Product.ean',
				'Product.sukl',
				'Product.pdk_code',
				'Product.supplier_id',
				'Product.alliance_description',
				'Product.is_alliance_rewritten'
			)	
		));

		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$adminId = $this->Session->read('Administrator.id');
		$product['Product']['show_wholesale_price'] = $this->Product->showWholesalePrice($id, $adminId);
		$this->set('product', $product);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		if (isset($this->data)) {
			// pokud je produkt z alliance a nemam potvrzenou upravu popisu, popis neupravuju
			if (in_array($product['Product']['supplier_id'], array(4, 5)) && !$this->data['Product']['is_alliance_rewritten']) {
				unset($this->data['Product']['description']);
			}

			if ($this->Product->save($this->data)) {
				// k produktu si ulozim id pro export do pohody
				if (empty($this->data['Product']['pohoda_id'])) {
					$save = array(
						'Product' => array(
							'id' => $this->Product->id,
							'pohoda_id' => $this->Product->id
						)
					);
					if (!$this->Product->save($save)) {
						$this->Session->setFlash('Produkt byl uložen, ale nepodařilo se uložit pohoda_id.', REDESIGN_PATH . 'flash_failure');
						$this->redirect($_SERVER['REQUEST_URI']);
					}
				}
				
				// ulozim si data o tom, ktere vlastnosti chci u daneho produktu updatovat feedem
				foreach ($this->data['ProductProperty'] as $product_property) {
					$product_property_id = $product_property['id'];
					$update = $product_property['update'];
					if (!$this->Product->update_product_property($id, $product_property_id, $update)) {
						trigger_error('Nepodarilo se ulozit data o tom, ktere vlastnosti chci u produktu updatovat feedem: product_id - ' . $product_id . ', product_property_id - ' . $product_property_id . ', update - ' . $update, E_USER_NOTICE);
					}
				}
				
				$this->data['FreeShippingProduct'] = $this->Product->generateFreeShipping($this->data);
				if (empty($this->data['FreeShippingProduct'])) {
					unset($this->data['FreeShippingProduct']);
					$this->Product->FreeShippingProduct->deleteAll(array('FreeShippingProduct.product_id' => $id));
				} else {
					foreach ($this->data['FreeShippingProduct'] as &$free_shipping_product) {
						$db_free_shipping_product = $this->Product->FreeShippingProduct->getByProductShipping($product['Product']['id'], $free_shipping_product['shipping_id']);
						if (!empty($db_free_shipping_product)) {
							$free_shipping_product['id'] = $db_free_shipping_product['FreeShippingProduct']['id'];
						}
						$this->Product->FreeShippingProduct->create();
						$this->Product->FreeShippingProduct->save($free_shipping_product);
					}
				}
				
				$this->Session->setFlash('Produkt byl upraven.', REDESIGN_PATH . 'flash_success');
				$this->redirect($_SERVER['REQUEST_URI']);
			} else {
				$this->Session->setFlash('Produkt se nepodařilo upravit. Opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $product;
			
			$product_properties = $this->Product->ProductPropertiesProduct->ProductProperty->find('all', array(
				'contain' => array(),
				'fields' => array('ProductProperty.id')
			));
			
			foreach ($product_properties as $product_property) {
				$product_properties_product = $this->Product->ProductPropertiesProduct->find('first', array(
					'conditions' => array(
						'ProductPropertiesProduct.product_id' => $id,
						'ProductPropertiesProduct.product_property_id' => $product_property['ProductProperty']['id']
					),
					'contain' => array(),
					'fields' => array('ProductPropertiesProduct.update')
				));
				// defaultne nastavim, ze chci updatovat vlastnost produktu
				$update = true;
				if (!empty($product_properties_product)) {
					$update = $product_properties_product['ProductPropertiesProduct']['update'];
				}
				$this->data['ProductProperty'][$product_property['ProductProperty']['id']]['property_id'] = $product_property['ProductProperty']['id'];
				$this->data['ProductProperty'][$product_property['ProductProperty']['id']]['update'] = $update;
			}
			
			if (in_array($product['Product']['supplier_id'], array(4, 5)) && !$product['Product']['is_alliance_rewritten']) {
				$description_image_content = download_url_like_browser($product['Product']['description']);
				$this->data['Product']['description_url'] = '<img src="data:image/jpeg;base64,' . base64_encode($description_image_content) . '" />';
				$this->data['Product']['description'] = str_replace("\n", '<br/>', $product['Product']['alliance_description']);
			}
			
			$free_shipping_product = $this->Product->FreeShippingProduct->getByProductId($product['Product']['id']);
			if (!empty($free_shipping_product)) {
				$this->data['Product']['free_shipping_quantity'] = $free_shipping_product['FreeShippingProduct']['quantity'];
			}
		}
		
		$productTypes = $this->Product->ProductType->find('list');
		$manufacturers = $this->Product->Manufacturer->find('list', array(
			'conditions' => array('Manufacturer.active' => true),
			'fields' => array('Manufacturer.id', 'Manufacturer.name'),
			'order' => array('Manufacturer.name' => 'asc')
		));
		$taxClasses = $this->Product->TaxClass->find('list');
		$availabilities = $this->Product->Availability->find('list', array(
			'conditions' => array('Availability.active' => true),
			'order' => array('Availability.order' => 'asc')
		));
		$tiny_mce_elements = 'ProductDescription';
		$this->set(compact('productTypes', 'manufacturers', 'taxClasses', 'availabilities', 'opened_category_id', 'tiny_mce_elements'));
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit_price_list($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(
				'CustomerTypeProductPrice' => array(
					'fields' => array('CustomerTypeProductPrice.id', 'CustomerTypeProductPrice.price'),
					'CustomerType' => array(
						'fields' => array('CustomerType.id', 'CustomerType.name', 'CustomerType.order')
					)
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.retail_price_with_dph',
				'Product.discount_common'
			)
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$sort_by_customer_type_id = function($a, $b) {
			if (isset($a['CustomerType']['order']) && isset($b['CustomerType']['order'])) {
				return $a['CustomerType']['order'] > $b['CustomerType']['order'];
			}
			return 0;
		};
		usort($product['CustomerTypeProductPrice'], $sort_by_customer_type_id);

		$this->set('product', $product);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		if (isset($this->data)) {
			if ($this->Product->saveAll($this->data)) {
				$this->Session->setFlash('Produkt byl upraven.', REDESIGN_PATH . 'flash_success');
				$this->redirect($_SERVER['REQUEST_URI']);
			} else {
				$this->Session->setFlash('Produkt se nepodařilo upravit. Opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $product;
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_images_list($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		// nacist info o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(
				'Image' => array(
					'order' => array('Image.order' => 'asc')
				)
			),
			'fields' => array('Product.id', 'Product.name')
		));
	
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}

		$this->set('product', $product);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
	
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit_documents($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		// nacist info o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(
				'ProductDocument' => array(
					'order' => $this->Product->ProductDocument->order
				)
			),
			'fields' => array('Product.id', 'Product.name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$this->set('product', $product);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		$this->set('documents_folder', $this->Product->ProductDocument->folder);
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit_related($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		// nacist info o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}

		if ($this->Session->check('RelatedProductSearch.Product.id')) {
			if ($this->Session->read('RelatedProductSearch.Product.id') != $id) {
				$this->Session->delete('RelatedProductsSearch');
			} else {
				if (!isset($this->data)) {
					$this->data['Product']['id'] = $id;
					if ($this->Session->check('RelatedProductSearch.Product.fulltext1')) {
						$this->data['Product']['fulltext1'] = $this->Session->read('RelatedProductSearch.Product.fulltext1');
					}
					if ($this->Session->check('RelatedProductSearch.Category.id')) {
						$this->data['Category']['id'] = $this->Session->read('RelatedProductSearch.Category.id');
					}
				}
			}
		}
		
		// pokud jsem ukladal souvisejici produkt, chci vypsat opet produkty z kategorie, ze ktere jsem souvisejici produkt vybiral
		if (isset($this->params['named']['related_category_id'])) {
			$this->data['Category']['id'] = $this->params['named']['related_category_id'];
		}
		
		$related_products = $this->Product->RelatedProduct->find('all', array(
			'conditions' => array('RelatedProduct.product_id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('RelatedProduct.related_product_id = Product.id')
				),
			),
			'fields' => array(
				'RelatedProduct.id',
				'Product.id',
				'Product.name',
				'Product.url'
			)
		));
		$this->set('related_products', $related_products);
		
		if (isset($this->data))  {
			$products_conditions = array(
				'Product.active' => true,
				'Product.id !=' => $id
			);
			$search_form = array(
				'Product' => array(
					'id' => $id
				)
			);
			
			if (isset($this->data['Category']['id']) && !empty($this->data['Category']['id'])) {
				$products_conditions['CategoriesProduct.category_id'] = $this->Product->CategoriesProduct->Category->subtree_ids($this->data['Category']['id']);
				$search_form['Category']['id'] = $this->data['Category']['id'];
			}
			
			if (isset($this->data['Product']['fulltext1']) && !empty($this->data['Product']['fulltext1'])) {
				$value = $this->data['Product']['fulltext1'];
				$fulltext_condition = array(
					'OR' => array(
						'Product.id' => $value,
						"Product.name LIKE '%%" . $value . "%%'",
						"Product.title LIKE '%%" . $value . "%%'",
						"Product.heading LIKE '%%" . $value . "%%'",
						"Product.related_name LIKE '%%" . $value . "%%'",
						"Product.zbozi_name LIKE '%%" . $value . "%%'",
						"Product.short_description LIKE '%%" . $value . "%%'",
						"Product.description  LIKE '%%" . $value . "%%'",
						"Manufacturer.name  LIKE '%%" . $value . "%%'",
					)
				);
				$products_conditions[] = $fulltext_condition;
				$search_form['Product']['fulltext1'] = $this->data['Product']['fulltext1'];
			}
			
			if (!empty($related_products)) {
				$related_products_ids = Set::extract('/Product/id', $related_products);
				$products_conditions[] = 'CategoriesProduct.product_id NOT IN (' . implode(',', $related_products_ids) . ')';
			}

			$categories_products = $this->Product->CategoriesProduct->find('all', array(
				'conditions' => $products_conditions,
				'contain' => array(),
				'fields' => array(
					'DISTINCT Product.id',
					'Product.name',
					'Product.url'
				),
				'joins' => array(
					array(
						'table' => 'products',
						'alias' => 'Product',
						'type' => 'INNER',
						'conditions' => array('Product.id = CategoriesProduct.product_id')
					),
					array(
						'table' => 'manufacturers',
						'alias' => 'Manufacturer',
						'type' => 'INNER',
						'conditions' => array('Manufacturer.id = Product.manufacturer_id')
					)
				)
			));
			$this->set('categories_products', $categories_products);
			
			$this->Session->write('RelatedProductSearch', $search_form);
		} else {
			$this->data['Product']['id'] = $id;
		}
		
		$this->set('product', $product);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		$categories = $this->Product->CategoriesProduct->Category->generateAllPaths(true);
		$categories = Set::combine($categories, '{n}.Category.id', '{n}.Category.path');
		$this->set('categories', $categories);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit_categories($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		// nacist info o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		if (isset($this->data)) {
			// projdu data a vyfiltruju chyby
			$save = array();
			foreach ($this->data['CategoriesProduct'] as $cp) {
				if ($cp['category_id'] && !$this->Product->CategoriesProduct->hasAny($cp) && !in_array($cp, $save)) {
					$save[] = $cp;
				}
			}
			if ($this->Product->CategoriesProduct->saveAll($save)) {
				$this->Session->setFlash('Produkt byl přiřazen do kategorii.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'products', 'action' => 'edit_categories', $id, (isset($this->params['named']['category_id']) ? $this->params['named']['category_id'] : null)));
			} else {
				$this->Session->setFlash('Produkt nemohl být zkopírován, došlo k chybě.', REDESIGN_PATH . 'flash_failure');
			}
		}
	
		$this->set('product', $product);
		
		$categories_products = $this->Product->CategoriesProduct->find('all', array(
			'conditions' => array('CategoriesProduct.product_id' => $id),
			'fields' => array(
				'CategoriesProduct.id',
				'CategoriesProduct.category_id'
			)
		));
		foreach ($categories_products as &$categories_product) {
			$path = $this->Product->CategoriesProduct->Category->getPath($categories_product['CategoriesProduct']['category_id']);
			$path = Set::extract('/Category/name', $path);
			$categories_product['Category']['name'] = implode(' &times; ', $path);
		}
		
		$this->set('categories_products', $categories_products);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		$categories = $this->Product->CategoriesProduct->Category->generateAllPaths(true);
		$categories = Set::combine($categories, '{n}.Category.id', '{n}.Category.path');
		$this->set('categories', $categories);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_attributes_list($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		// nactu si produkt se zadanym idckem
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.name', 'Product.url')
		));

		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
	
		$this->set('product', $product);

		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
	
		// zjistim si options, ktere jsou zadane v systemu
		$options = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->find('all', array(
			'conditions' => array('Option.active' => true),
			'contain' => array()
		));
		$this->set('options', $options);

		// formular je vyplnen (ne filtrovani)
		if (isset($this->data) && !isset($this->data['Option'])) {
			// musim se podivat, jestli uz tam takovy atributy jsou
			$attributes = array();
			// pro ucely nasledneho mazani nadbytecnych subproduktu si zde iniciuju pole pro zapamatovani suproduktu, ktere odpovidaji
			// datum z formulare
			$subproduct_ids = array();
			foreach ($this->data['Attributes'] as $option_id => $attributes_text) {
				$attributes_text = trim($attributes_text);
				if ($attributes_text != '') {
					$attributes_values = explode(";", $attributes_text);
					foreach ($attributes_values as $value)  {
						$value = trim($value);
						if ($value == '') {
							continue;
						}
						$attribute = array();
						$attribute['Attribute']['value'] = $value;
						$attribute['Attribute']['option_id'] = $option_id;
						// podivam se, jestli tenhle atribut uz nemam v systemu
						$db_attribute = $this->Product->Subproduct->AttributesSubproduct->Attribute->find('first', array(
							'conditions' => $attribute['Attribute'],
							'contain' => array()
						));
						// pokud ne
						if (empty($db_attribute)) {
							// tak ho tam ulozim a zapamatuju si idcko
							$this->Product->Subproduct->AttributesSubproduct->Attribute->create();
							$this->Product->Subproduct->AttributesSubproduct->Attribute->save($attribute);
							$attributes[$option_id][] = $this->Product->Subproduct->AttributesSubproduct->Attribute->id;
						} else {
							// pokud jo, najdu jejich idcko
							$attributes[$option_id][] = $db_attribute['Attribute']['id'];
						}
					}
				}
			}
			// najdu subprodukty daneho produktu
			$subproducts = $this->Product->Subproduct->find('all', array(
				'conditions' => array('Subproduct.product_id' => $this->data['Product']['id']),
				'contain' => array('AttributesSubproduct')
			));
	
			// vygeneruju kombinace atributu
			$generated_subproducts = array();
			if (!empty($attributes)) {
				$generated_subproducts = $this->Product->combine($attributes);
			}
	
			// prochazim vygenerovane subprodukty
			foreach ($generated_subproducts as $generated_subproduct) {
				// musim projit subprodukty produktu a zjistit, jestli uz v db neni subprodukt, ktery chci vkladat
				foreach ($subproducts as $subproduct) {
					// myslim si, ze subprodukt v db je
					$found = true;
					// pokud souhlasi pocet attribute_subproducts u subproduktu z db a vygenerovaneho
					if (sizeof($subproduct['AttributesSubproduct']) == sizeof($generated_subproduct)) {
						// prochazim vztahy mezi atributy a subproduktem z db
						foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
							// jestlize neni attributes_subproduct soucasti vygenerovaneho subproduktu
							if (!in_array($attributes_subproduct['attribute_id'], $generated_subproduct)) {
								// nastavim, ze jsem subprodukt nenasel
								$found = false;
								// a attributes_subprodukty dal neprochazim
								break;
							}
						}
						// jestlize jsem subprodukt nasel v db
						if ($found) {
							// zapamatuju si jeho idcko v db
							$subproduct_ids[] = $subproduct['Subproduct']['id'];
							break;
						}
						// pokud se velikost lisi
					} else {
						// nastavim si, ze jsem subprodukt nenasel
						$found = false;
						break;
					}
				}
				// jestlize jsem subprodukt nenasel
				if (!isset($found) || !$found) {
					// musim vytvorit takovej subprodukt a k nemu napojeni na atributy
					$subproduct_save['Subproduct']['product_id'] = $this->data['Product']['id'];
					$subproduct_save['Subproduct']['active'] = true;
					unset($this->Product->Subproduct->id);
					$this->Product->Subproduct->save($subproduct_save);
					$subproduct_id = $this->Product->Subproduct->id;
					$subproduct_ids[] = $subproduct_id;
					foreach ($generated_subproduct as $attribute_id) {
						unset($this->Product->Subproduct->AttributesSubproduct->id);
						$attributes_subproduct_save['AttributesSubproduct']['subproduct_id'] = $subproduct_id;
						$attributes_subproduct_save['AttributesSubproduct']['attribute_id'] = $attribute_id;
						$this->Product->Subproduct->AttributesSubproduct->save($attributes_subproduct_save);
					}
				}
			}
			// musim najit vsechny subprodukty tohoto produktu a ty, co nejsou podle zadanych hodnot platne, musim odstranit
			// tzn musim porovnat saves oproti obsahu databaze a co je navic, tak smazat
			$db_subproduct_ids = $this->Product->Subproduct->find('all', array(
				'conditions' => array('product_id' => $this->data['Product']['id']),
				'contain' => array(),
				'fields' => array('id')
			));
			foreach ($db_subproduct_ids as $db_subproduct_id) {
				if (!in_array($db_subproduct_id['Subproduct']['id'], $subproduct_ids)) {
					$this->Product->Subproduct->delete($db_subproduct_id['Subproduct']['id']);
				}
			}
			$this->Session->setFlash('Úpravy byly provedeny', REDESIGN_PATH . 'flash_success');
			$this->redirect(array('controller' => 'products', 'action' => 'attributes_list', $this->data['Product']['id']));
		} else {
			// potrebuju vytvorit vstupni data pro formular
			// tzn pro kazdou option vybrat zvolene values k tomuto produktu - ne jen pro ty options, pro ktere ma produkt atributy, ale
			// uplne pro vsechny
			foreach ($options as $option) {
				$order = array('Attribute.option_id ASC');
				if ($option['Option']['id'] == 1) {
					$order[] = 'Attribute.value ASC';
				} else {
					$order[] = 'Attribute.sort_order ASC';
				}
				// vybiram takovy vazby mezi produktem a atributem, ktery patri k zadanymu produktu
				$attributes_subproducts = $this->Product->Subproduct->AttributesSubproduct->find('all', array(
					'conditions' => array_merge(
						array('Subproduct.product_id' => $id, 'Attribute.option_id' => $option['Option']['id'])
					),
					'contain' => array(
						'Subproduct',
							'Attribute' => array(
								'Option'
							)
						),
						'order' => $order,
						// musim se zbavit "duplicit" - atributes_subproduktu, ktery ukazuji na stejny atributy
						'group' => array('Attribute.id')
				));
				// nadefinuju implicitni hodnoty formularovych poli
				$this->data['Attributes'][$option['Option']['id']] = '';
				foreach ($attributes_subproducts as $attributes_subproduct) {
					$this->data['Attributes'][$option['Option']['id']] .= $attributes_subproduct['Attribute']['value'] . ';';
				}
	
				$this->data['Attributes'][$option['Option']['id']] = trim($this->data['Attributes'][$option['Option']['id']]);
			}
		}
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_comparator_click_prices($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		// nactu si produkt se zadanym idckem
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$this->set('product', $product);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id')
			));
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		$comparators = $this->Product->ComparatorProductClickPrice->Comparator->find('all', array(
			'conditions' => array('Comparator.active' => true),
			'contain' => array(),
			'order' => array('Comparator.order' => 'asc'),
			'fields' => array('Comparator.id', 'Comparator.name')
		));
		
		$comparator_product_click_prices = $this->Product->ComparatorProductClickPrice->find('all', array(
			'conditions' => array(
				'ComparatorProductClickPrice.product_id' => $product['Product']['id']
			),
			'contain' => array(),
		));
		
		
		if (isset($this->data)) {
			foreach ($this->data['ComparatorProductClickPrice'] as &$cpcp) {
				if ($cpcp_id = $this->Product->ComparatorProductClickPrice->get_id($cpcp['product_id'], $cpcp['comparator_id'])) {
					$cpcp['id'] = $cpcp_id;
				}
			}

			if ($this->Product->ComparatorProductClickPrice->saveAll($this->data['ComparatorProductClickPrice'])) {
				$this->Session->setFlash('Ceny za proklik byly uloženy.', REDESIGN_PATH . 'flash_success');
				$this->redirect($_SERVER['REQUEST_URI']);
			} else {
				$this->Session->setFlash('Ceny za proklik se nepodařilo uložit.', REDESIGN_PATH . 'flash_failure');
			}
		}	
		
		$this->set('comparators', $comparators);
		$this->set('comparator_product_click_prices', $comparator_product_click_prices);
		$this->set('id', $id);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_duplicate($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		// nacist info o produktu
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.name')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$this->set('product', $product);
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(
				'CategoriesProduct' => array(
					'fields' => array('CategoriesProduct.id', 'CategoriesProduct.category_id')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.heading',
				'Product.breadcrumb',
				'Product.related_name',
				'Product.zbozi_name',
				'Product.manufacturer_id',
				'Product.availability_id',
				'Product.note',
				'Product.short_description',
				'Product.description',
				'Product.product_type',
				'Product.tax_class_id',
				'Product.retail_price_wout_dph',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.title',
				'Product.url',
			)
		));

		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		if (!empty($this->data)) {
			if ($this->Product->saveAll($this->data)) {
				$this->Session->setFlash('Produkt byl uložen.');
				$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $opened_category_id), null, true);
			} else {
				$this->Session->setFlash('Produkt nemohl být uložen, některá pole zůstala nevyplněna.');
			}
		} else {
			$this->data = $product;
			
			$customer_type_product_prices = $this->Product->CustomerTypeProductPrice->find('all', array(
				'conditions' => array('CustomerTypeProductPrice.product_id' => $product['Product']['id']),
				'contain' => array(),
				'fields' => array('CustomerTypeProductPrice.id', 'CustomerTypeProductPrice.customer_type_id', 'CustomerTypeProductPrice.price')	
			));
			
			$customer_type_product_prices = Set::combine($customer_type_product_prices, '{n}.CustomerTypeProductPrice.customer_type_id', '{n}.CustomerTypeProductPrice');
			$this->data['CustomerTypeProductPrice'] = $customer_type_product_prices;
		}
		
		if (empty($opened_category_id)) {
			$opened_category_id = $product['CategoriesProduct'][0]['category_id'];
		}
		$this->set('opened_category_id', $opened_category_id);
		
		$manufacturers = $this->Product->Manufacturer->find('list', array('order' => array('Manufacturer.name' => 'asc')));
		$taxClasses = $this->Product->TaxClass->find('list');
		$availabilities = $this->Product->Availability->find('list');
		$customer_types = $this->Product->CustomerTypeProductPrice->CustomerType->find('all', array(
			'contain' => array(),
			'fields' => array('CustomerType.id', 'CustomerType.name')
		));
		$tinyMce = true;
		$this->set(compact('product', 'manufacturers','taxClasses', 'tinyMce', 'availabilities', 'customer_types'));

		$this->set('product_types', $this->product_types);
	}

	/*
	* @description				Vymaze produkt.
	*/
	function admin_delete($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// nactu si info o produktu, ktery budu mazat
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('Product.id')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}

		$product['Product']['active'] = false;
		if ($this->Product->save($product)) {
			$this->Session->setFlash('Produkt byl vymazán', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo vymazat, opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
		}
		$redirect = array('controller' => 'products', 'action' => 'index');
		if (isset($opened_category_id)) {
			$redirect['category_id'] = $opened_category_id;
		}
		$this->redirect($redirect);
	}
	
	/**
	 * Aktivuje produkt smazany pomoci admin_delete (nastavi active zpet na true)
	 */
	function admin_activate($id = null, $opened_category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}

			// nactu si info o produktu, ktery budu mazat
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(),
			'fields' => array('Product.id')
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}

		$product['Product']['active'] = true;
		if ($this->Product->save($product)) {
			$this->Session->setFlash('Produkt byl aktivován', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo aktivovat, opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
		}
		
		$redirect = array('controller' => 'products', 'action' => 'index');
		if (isset($opened_category_id)) {
			$redirect['category_id'] = $opened_category_id;
		}
		$this->redirect($redirect);
	}

	function admin_delete_from_db($id = null, $opened_category_id = null) {
		$redirect = array('controller' => 'products', 'action' => 'index');
		if (isset($opened_category_id)) {
			$redirect['category_id'] = $opened_category_id;
		}
		
		if (!$id) {
			$this->Session->setFlash('Neznámý produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect($redirect);
		}

		// nactu si info o produktu, ktery budu mazat
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array('CategoriesProduct', 'Subproduct'),
		));
		
		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect($redirect);
		}

		// musim vymazat vsechny subprodukty a obrazky
		foreach ($product['Subproduct'] as $subproduct) {
			$this->Product->Subproduct->AttributesSubproduct->deleteAll(array('subproduct_id' => $subproduct['id']));
			$this->Product->Subproduct->delete($subproduct['id']);
		}

		$this->Product->Image->deleteAllImages($id);
		$this->Product->ProductDocument->deleteAllDocuments($id);

		if ($this->Product->delete($id)) {
			$this->Session->setFlash('Produkt byl vymazán z databáze.', REDESIGN_PATH . 'flash_success');
		}
		
		$this->redirect($redirect);
	}
	
	/*
	 * @description				Vypise seznam smazanych produktu.
	 */
	function admin_deleted(){
		$products = $this->Product->find('all', array(
			'fields' => array('Product.id'),
			'contain' => array()
		));
		
		$product_ids = array();
		// projdu si produkty, zda jsou v nejake kategorii
		foreach ( $products as $product ){
			if ( !$this->Product->CategoriesProduct->hasAny(array('CategoriesProduct.product_id' => $product['Product']['id'])) ){
				$product_ids[] = $product['Product']['id'];
			}
		}

		$products = array();
		if ( !empty($product_ids) ){
			$products = $this->Product->find('all', array(
				'conditions' => array('Product.id' => $product_ids),
				'contain' => array(),
				'fields' => array('id', 'name', 'retail_price_with_dph')
			));
		}
		
		$this->set('products', $products);
	}
	
	/**
	 * Zduplikuje produkt
	 *
	 * @param unsigned int $id
	 */
	function admin_copy($id = null, $opened_category_id = null) {
		// nactu si data produktu
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array(
				'CategoriesProduct',
				'CustomerTypeProductPrice',
				'Subproduct' => array(
					'AttributesSubproduct'
				),
				'Image'
			)
		));
		
		$this->Session->setFlash('Produkt byl zduplikován.', REDESIGN_PATH . 'flash_success');
		
		// zalozim produkt
		unset($this->Product->id);
		unset($product['Product']['id']);
		// zalozim ho jako neaktivni, at mi hned neskace v obchode
		$product['Product']['active'] = false;
		
		if ( $this->Product->save($product) ){
			// mam ulozeny produkt, musim zmenit URL produktu podle noveho ID
			// musim rozlisit jestli se jedna o stary typ URL, nebo o novy typ URL
			// prepoklad ze se jedna o nove URL
			$new_url = str_replace('p' . $id, 'p' . $this->Product->id, $product['Product']['url']);
			if ( eregi('prod' . $id, $product['Product']['url']) ){
				// stary typ URL
				$new_url = str_replace('prod' . $id . '-', '', $product['Product']['url']);
				$new_url = str_replace('.htm', '', $new_url);
				$new_url = $new_url . '-p' . $this->Product->id;
			}
			
			// ulozim URL pro duplikovany produkt
			if ( $this->Product->save(array('url' => $new_url), false) ){
				// zduplikuju obrazky
				$result = $this->Product->copy_images($this->Product->id, $product['Image']);
				if ( $result !== true ){
					$this->Session->setFlash($result, REDESIGN_PATH . 'flash_failure');
				} else {
					// zaradim produkt do kategorii
					foreach ($product['CategoriesProduct'] as $categories_product) {
						unset($categories_product['id']);
						$categories_product['product_id'] = $this->Product->id;
						$this->Product->CategoriesProduct->create();
						if (!$this->Product->CategoriesProduct->save($categories_product)) {
							$this->Session->setFlash('Nepodařilo se zařadit produkt do nové kategorie.', REDESIGN_PATH . 'flash_failure');
						}
					}
					
					// zduplikuju ceny produktu
					foreach ($product['CustomerTypeProductPrice'] as $ctpp) {
						unset($ctpp['id']);
						$ctpp['product_id'] = $this->Product->id;
						$this->Product->CustomerTypeProductPrice->create();
						if (!$this->Product->CustomerTypeProductPrice->save($ctpp)) {
							$this->Session->setFlash('Nepodařilo se vytvořit ceny u produktu.', REDESIGN_PATH . 'flash_failure');
						}
					}

					// zkopiruju si subprodukty
					if ( !empty($product['Subproduct']) ){
						foreach( $product['Subproduct'] as $sp ){
							$sp_data = array(
								'product_id' => $this->Product->id,
								'price_with_dph' => $sp['price_with_dph'],
								'active' => $sp['active'],
								'availability_id' => $sp['availability_id']
							);
							unset($this->Product->Subproduct->id);
							if ( !$this->Product->Subproduct->save($sp_data) ){
								$this->Session->setFlash('Nepodařilo se duplikovat subproduct ID = ' . $sp['id'], REDESIGN_PATH . 'flash_failure');
							} else {
								// musim nakopirovat i vztahy mezi subprodukty a atributy
								foreach ($sp['AttributesSubproduct'] as $att_sp) {
									$att_sp_data = array(
										'attribute_id' => $att_sp['attribute_id'],
										'subproduct_id' => $this->Product->Subproduct->id
									);
									unset($this->Product->Subproduct->AttributesSubproduct->id);
									if (!$this->Product->Subproduct->AttributesSubproduct->save($att_sp_data)) {
										$this->Session->setFlash('Nepodařilo se duplikovat vztah mezi atributem a subproduktem ID = ' . $att_sp['id'], REDESIGN_PATH . 'flash_failure');
									}
								}
							}
						}
					}
				}
			} else {
				$this->Session->setFlash('Chyba při úpravě nového URL produktu.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->Session->setFlash('Chyba při zakládání produktu.', REDESIGN_PATH . 'flash_failure');
		}
		
		$this->redirect(array('controller' => 'products', 'action' => 'duplicate', $id, (isset($opened_category_id) ? $opened_category_id : null)));
	}

	/**
	 * Obsluhuje administraci subproduktu
	 *
	 * @param int $id - product_id
	 */
	function admin_add_subproducts($id) {
		if (isset($this->data)) {

			foreach ($this->data['Product'] as $subproduct_id => $subproduct) {
				$subproduct['Subproduct']['id'] = $subproduct_id;
				$subproduct['Subproduct']['active'] = $subproduct['active'];
				$subproduct['Subproduct']['price_with_dph'] = $subproduct['price_with_dph'];
					
				unset($this->Product->Subproduct->id);
				$this->Product->Subproduct->save($subproduct);
			}
			// zkontroluju nastaveni Product.active
			$product = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $this->data['Product'][$subproduct_id]['product_id']),
				'contain' => array(
					'Subproduct'
				)
			));
			// kontroluju priznak active u produktu a subproduktu
			$active = false;
			$i = 0;
			while (!$active && $i < sizeof($product['Subproduct'])) {
				$active = $product['Subproduct'][$i]['active'];
				$i++;
			}
			$message = '';
			if ($active && !$product['Product']['active']) {
				$product['Product']['active'] = true;
				unset($this->Product->id);
				if ($this->Product->save($product)) {
					$message = ' Produkt byl aktivován';
				}
			} elseif (!$active && $product['Product']['active']) {
				$product['Product']['active'] = false;
				unset($this->Product->id);
				if ($this->Product->save($product)) {
					$message = ' Produkt byl deaktivován';
				}
			}
		}
		$this->Session->setFlash('Úpravy byly provedeny.', REDESIGN_PATH . 'flash_success');
		$this->redirect(array('controller' => 'products', 'action' => 'attributes_list', $id));
	}
	
	function autocomplete_list() {
		$term = '';
		if (isset($_GET['term'])) {
			$term = $_GET['term'];
		}
	
		$active_products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.active' => true,
				'Product.name LIKE \'%%' . $term . '%%\'',
				'Availability.cart_allowed' => true
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id')
				)		
			),
			'fields' => array('Product.id', 'Product.name')
		));
	
		$autocomplete_active_products = array();
		foreach ($active_products as $active_product) {
			$autocomplete_active_products[] = array(
				'label' => $active_product['Product']['name'],
				'value' => $active_product['Product']['id']
			);
		}
	
		if (!function_exists('json_encode')) {
			App::import('Vendor', 'Services_JSON', array('file' => 'JSON.php'));
			$json = &new Services_JSON();
			echo $json->encode($autocomplete_active_products);
		} else {
			echo json_encode($autocomplete_active_products);
		}
		die();
	}
	
	function change_link($id, $sportnutrition_url) {
		$sportnutrition_url = base64_decode($sportnutrition_url);
		
		$product = array(
			'Product' => array(
				'id' => $id,
				'sportnutrition_url' => $sportnutrition_url
			)
		);
		
		$this->Product->save($product, false);
		
		die('zmeneno');
	}
	
	function load_eans() {
		$file_name = 'EanDopl.csv';
		$file_dir = 'files';
		$file_path = $file_dir . DS . $file_name;
		
		$row = 1;
		$errors = 0;
		if (($handle = fopen($file_path, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				if ($row == 1) {
					$row++;
					continue;
				}
				if (isset($data[1]) && !empty($data[1]) && isset($data[2]) && !empty($data[2]) && $data[2] != 'neni' && $data[2] != 'není') {
					if ($this->Product->hasAny(array('id' => $data[1]))) {
						$product = array(
							'Product' => array(
								'id' => $data[1],
								'ean' => $data[2]
							)	
						);

						if (!$this->Product->save($product)) {
							debug($data);
							debug($product);
							$errors++;
						}
					}
				}
			}
			fclose($handle);
		}
		debug($errors);
		die();
	}
	
	function admin_download_pd_descriptions() {
		$folder = 'files/pd_descriptions/';
		$products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.supplier_id' => array(4, 5),
			),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.description')
		));


		foreach ($products as $product) {
			// mam pro dany produkt stazeny popis?
			$image_name = $product['Product']['id'] . '.jpeg';
			if (!file_exists($folder . $image_name)) {
				if ($image = download_url_like_browser($product['Product']['description'])) {
					if (!file_put_contents($folder . $image_name, $image)) {
						$debug($folder . $image_name);
						die('nepodarilo se nahrat obrazek na disk');
					}
				} else {
					debug($product['Product']['description'] . ' - ' . $product['Product']['id'] . ' - NEPODARILO SE STAHNOUT OBRAZEK');
				}
			}
		}

		die('OK');
	}
	
	function admin_upload_transformed_pd_descriptions() {
		$folder = 'files/pd_descriptions_transformed/';
		$folder_contents = scandir($folder);

		foreach ($folder_contents as $file_name) {
			if ($file_name != '.' && $file_name != '..') {
				$product_id = explode('.', $file_name);
				$product_id = $product_id[0];
				
				$description = file_get_contents($folder . $file_name);
				
				$product = array(
					'Product' => array(
						'id' => $product_id,
						'alliance_description' => $description
					)
				);
				
				if (!$this->Product->save($product)) {
					debug($product);
					die('nepodarilo se ulozit produkt');
				}
				
			}
		}
		die('OK');
	}
	
	function admin_deactivate_alliance() {
		$file = 'files/duplicitni-ean-kody.csv';
		$content = file_get_contents($file);
		$content = explode("\n", $content);
		foreach ($content as $line) {
			$line = explode(';', $line);
			foreach ($line as $id) {
				$id = trim($id);
				if ($id) {
					// deaktivovat produkt
					$product = array(
						'Product' => array(
							'id' => $id,
							'active' => false
						)
					);
					$this->Product->create();
					$this->Product->save($product);
					// zakazat update atributu active
					$property = array(
						'ProductPropertiesProduct' => array(
							'product_id' => $id,
							'product_property_id' => 18,
							'update' => false
						)
					);
					$db_property = $this->Product->ProductPropertiesProduct->find('first', array(
						'conditions' => array(
							'ProductPropertiesProduct.product_id' => $id,
							'ProductPropertiesProduct.product_property_id' => 18
						),
						'contain' => array(),
						'fields' => array()
					));
					if (!empty($db_property)) {
						$property['ProductPropertiesProduct']['id'] = $db_property['ProductPropertiesProduct']['id'];
					}
	
					$this->Product->ProductPropertiesProduct->create();
					$this->Product->ProductPropertiesProduct->save($property);
				}
			}
		}
		die('OK');
	}

} // konec tridy
?>
