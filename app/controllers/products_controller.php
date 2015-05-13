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
			if (isset($this->data['Subproduct']['id'])) {
				$subproduct = $this->Product->Subproduct->find('first', array(
					'conditions' => array('Subproduct.id' => $this->data['Subproduct']['id']),
					'contain' => array(
						'AttributesSubproduct' => array(
							'Attribute'
						)
					)
				));
				$new_data['CartsProduct']['quantity'] = $this->data['Subproduct']['quantity'];
				$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
				$new_data['CartsProduct']['subproduct_id'] = $this->data['Subproduct']['id'];
			} elseif (isset($this->data['Subproduct']['quantity'])) {
				// vkladam do kosiku produkt bez variant
				$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
				$new_data['CartsProduct']['quantity'] = $this->data['Subproduct']['quantity'];
			} else {
				// vkladam do kosiku produkt z vypisu produktu v kategorii
				// produkt chci do kosiku vlozit pouze v pripade, ze nema zadne varianty
				if (!$this->Product->Subproduct->hasAny(array('Subproduct.product_id' => $this->data['Product']['id']))) {
					$new_data['CartsProduct']['product_id'] = $this->data['Product']['id'];
					$new_data['CartsProduct']['quantity'] = $this->data['Product']['quantity'];
				} else {
					$this->Session->setFlash('Produkt se nepodařilo vložit do košíku. Nejprve prosím <a href="' . $this->here . '#AddProductWithVariantsForm">vyberte variantu produktu</a>.', REDESIGN_PATH . 'flash_failure');
					$this->redirect($this->here);
				}
			}
			
			if (isset($new_data)) {
				$this->data = $new_data;
				
				$result = $this->Product->requestAction('carts_products/add', $this->data);
				// vlozim do kosiku
				if ( $result ){
					$this->Session->setFlash('Produkt byl uložen do nákupního košíku. Obsah Vašeho košíku si můžete zobrazit <a href="/kosik">zde</a>.', REDESIGN_PATH . 'flash_success');
					$product = $this->Product->read(array('Product.url'), $this->data['CartsProduct']['product_id']);
					$this->redirect('/' . $product['Product']['url'], null, true);
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
				'Product.price',
				'Product.rate',
				'Product.video',
				'Product.note',
				'Product.active',
				'Product.code',
				'Product.ean',
				'Product.sukl',
				'Product.group'
				
			),
			'joins' => array(
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				)
			),
			'group' => array('Product.id')
		));
		unset($this->Product->virtualFields['price']);

		if (empty($product)) {
			$this->Session->setFlash('Neexistující produkt.');
			$this->cakeError('error404');
		}
		$this->set('product', $product);

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
			'order' => array('Attribute.option_id', 'Attribute.sort_order'),
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
		foreach ($path as $item) {
			$breadcrumb = array('anchor' => $item['Category']['breadcrumb'], 'href' => '/' . $item['Category']['url']);
			if ($item['Category']['id'] == 5) {
				$breadcrumb = array('anchor' => 'Domů', 'href' => HP_URI);
			}
			$breadcrumbs[] = $breadcrumb;
		}
		$breadcrumbs[] = array('anchor' => $product['Product']['breadcrumb'], 'href' => '/' . $product['Product']['url']);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// zapnu fancybox
		$this->set('fancybox', true);
		
		// PRODUKTY, KTERE NEJCASTEJI KUPUJI LIDE S TIMTO PRODUKTEM
		$similar_products = $this->Product->similar_products($id, $customer_type_id);
		$this->set('similar_products', $similar_products);
		
		// updatuju zasobnik v sesne, kde mam ulozenych x naposled navstivenych produktu
		$stack = $this->Session->read('ProductStack');
		$stack = $this->Product->update_stack($stack, $id);
		$this->Session->write('ProductStack', $stack);
		
		if (!empty($product['CategoriesProduct'])) {
			$right_sidebar_products = $this->Product->right_sidebar_products($id, $customer_type_id);
			$this->set('right_sidebar_products', $right_sidebar_products);
		}
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
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$products = array();
		$conditions = null;

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
			$this->Session->write('Search.AdminProductForm.page', $page);
		} else {
			$page = $this->Session->read('Search.AdminProductForm.page');
		}
		
		$sort = false;
		if (isset($this->params['named']['sort'])) {
			$sort = $this->params['named']['sort'];
			$this->Session->write('Search.AdminProductForm.sort', $sort);
		} else {
			$sort = $this->Session->read('Search.AdminProductForm.sort');
		}
		
		$direction = 'asc';
		if ($sort && isset($this->params['named']['direction'])) {
			$direction = $this->params['named']['direction'];
			$this->Session->write('Search.AdminProductForm.direction', $direction);
		} else {
			$direction = $this->Session->read('Search.AdminProductForm.direction');
		}

		if (isset($conditions)) {
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
					'type' => 'INNER',
					'conditions' => array('Product.availability_id = Availability.id')
				)
			);

			$this->paginate['show'] = 'all';
			$this->pagiante['page'] = $page;
			if ($sort && $direction) {
				$this->paginate['order'] = array($sort => $direction);
			}
			$this->paginate['conditions'] = $conditions;
			$this->paginate['contain'] = array();
			$this->paginate['joins'] = $joins;
			$this->paginate['fields'] = array(
				'DISTINCT Product.id',
				'Product.name',
				'Product.active',
				'Product.priority',
				'Manufacturer.name',
				'Availability.cart_allowed',
				'CategoriesProduct.id'
			);

			$products = $this->paginate();
		}
		$this->set('products', $products);

		$categories = $this->Product->CategoriesProduct->Category->generateAllPaths(true);
		$categories = Set::combine($categories, '{n}.Category.id', '{n}.Category.path');
		$this->set('categories', $categories);
		
		
		$search_properties = $this->Product->search_properties;
		$search_properties = Set::combine($search_properties, '{n}.id', '{n}.name');
		$this->set('search_properties', $search_properties);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_add($category_id = null) {
		if (!isset($category_id)) {
			$this->Session->setFlash('Není určena kategorie, do které chcete produkt vložit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		if (!empty($this->data)) {
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

		$this->set('opened_category_id', $category_id);

		$manufacturers = $this->Product->Manufacturer->find('list', array('order' => array('Manufacturer.name' => 'asc')));
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
				'Product.short_description',
				'Product.description',
				'Product.active',
				'Product.note',
				'Product.manufacturer_id',
				'Product.availability_id',
				'Product.product_type_id',
				'Product.tax_class_id',
				'Product.recycle_fees',
				'Product.discount',
				'Product.guarantee',
				'Product.priority',
				'Product.weight',
				'Product.video',
				'Product.is_top_produkt',
				'Product.is_akce',
				'Product.is_doprava_zdarma',
				'Product.is_novinka',
				'Product.is_sleva',
				'Product.is_doprodej',
				'Product.is_montaz',
				'Product.is_firmy_cz',
				'Product.is_slide_akce',
				'Product.feed',
				'Product.title',
				'Product.keywords',
				'Product.pohoda_id',
				'Product.ean'
			)	
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
		
		if (isset($this->data)) {
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
				$this->Session->setFlash('Produkt byl upraven.', REDESIGN_PATH . 'flash_success');
				$this->redirect($_SERVER['REQUEST_URI']);
			} else {
				$this->Session->setFlash('Produkt se nepodařilo upravit. Opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $product;
		}
		
		$productTypes = $this->Product->ProductType->find('list');
		$manufacturers = $this->Product->Manufacturer->find('list');
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
		
		// pokud jsem ukladal souvisejici produkt, chci vypsat opet produkty z kategorie, ze ktere jsem souvisejici produkt vybiral
		if (isset($this->params['named']['related_category_id'])) {
			$this->data['Category']['id'] = $this->params['named']['related_category_id'];
		}
		
		if (isset($this->data))  {
			$categories_products = $this->Product->CategoriesProduct->find('all', array(
				'conditions' => array(
					'CategoriesProduct.category_id' => $this->data['Category']['id'],
					'Product.active' => true
				),
				'contain' => array('Product'),
				'fields' => array(
					'Product.id',
					'Product.name',
					'Product.url'
				)
			));

			$this->set('categories_products', $categories_products);
		} else {
			$this->data['Product']['id'] = $id;
		}
		
		$this->set('product', $product);
		
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
		
		if (isset($opened_category_id)) {
			$category = $this->Product->CategoriesProduct->Category->find('first', array(
				'conditions' => array('Category.id' => $opened_category_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name')
			));
			$this->set('category', $category);
			$this->set('opened_category_id', $category['Category']['id']);
		}
		
		$categories = $this->Product->CategoriesProduct->Category->generateTreeList(null, null, '{n}.Category.name', ' - ', -1);
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
		
		$categories = $this->Product->CategoriesProduct->Category->generateAllPaths(false);
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
		$options = $this->Product->Subproduct->AttributesSubproduct->Attribute->Option->find('all');
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
			$this->Session->setFlash('Úpravy byly provedeny');
			$this->redirect(array('controller' => 'products', 'action' => 'attributes_list', $this->data['Product']['id']));
		} else {
			// potrebuju vytvorit vstupni data pro formular
			// tzn pro kazdou option vybrat zvolene values k tomuto produktu - ne jen pro ty options, pro ktere ma produkt atributy, ale
			// uplne pro vsechny
			foreach ($options as $option) {
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
						'order' => array('Attribute.option_id ASC', 'Attribute.sort_order ASC'),
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
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// nactu si info o produktu, ktery budu mazat
		$this->Product->contain('CategoriesProduct');
		$product = $this->Product->read(null, $id);

		$product['Product']['active'] = false;
		if ($this->Product->save($product)) {
			$this->Session->setFlash('Produkt byl vymazán');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo vymazat, opakujte prosím akci');
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
			$this->Session->setFlash('Neexistující produkt.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// nactu si info o produktu, ktery budu mazat
		$this->Product->contain('CategoriesProduct');
		$product = $this->Product->read(null, $id);

		$product['Product']['active'] = true;
		if ($this->Product->save($product)) {
			$this->Session->setFlash('Produkt byl aktivován');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo aktivovat, opakujte prosím akci');
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
			$this->Session->setFlash('Produkt byl vymazán z databáze.');
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
		$this->Session->setFlash('Úpravy byly provedeny.');
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
	
	function admin_import() {
		$this->Product->import();
		die('here');
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
	
	function sold_out_urls() {
		$removes = array(
//			array('-docasne-vyprodano', 'dočasně vyprodáno'),
//			array('docasne-vyprodano', 'dočasně vyprodáno'),
//			array('-vyprodano', 'vyprodáno'),
//			array('vyprodano', 'vyprodáno'),
//			array('-doprodano', 'doprodáno'),
//			array('doprodano', 'doprodáno'),
//			array('-novinka', 'novinka'),
			array('novinka', 'novinka')
		);
		
		$remove_strings = array(
			0 => 'dočasně vyprodáno!',
			'dočasně vyprodáno',
			'- vyprodáno!',
			'- VYPRODÁNO!',
			'- vyprodáno',
			'- VYPRODÁNO',
			'vyprodáno!',
			'VYPRODÁNO!',
			'vyprodáno',
			'VYPRODÁNO',
			'doprodáno!',
			'doprodáno'
		);
		
		App::import('Model', 'Redirect');
		$this->Redirect = &new Redirect;
		
		$data_source = $this->Product->getDataSource();
				
		// pro kazdy retezec, ktereho se chci zbavit
		foreach ($removes as $remove) {
			// najdu produkty, ktere ho maji v url
			$products = $this->Product->find('all', array(
				'conditions' => array(
					'Product.url LIKE "%%' . $remove[0] . '%%"',
					'Product.active' => true,
					'Availability.cart_allowed' => true
				),
				'contain' => array('Availability'),
				'fields' => array(
					'Product.id',
					'Product.name',
					'Product.breadcrumb',
					'Product.related_name',
					'Product.heading',
					'Product.title',
					'Product.zbozi_name',
					'Product.heureka_name',
					'Product.url',
					'Availability.cart_allowed'
				),
				'order' => array('Product.active' => 'desc', 'Availability.cart_allowed' => 'desc'),
				'limit' => 5
			));
			
			foreach ($products as $product) {
				$data_source->begin($this->Product);
				$old_url = '/' . $product['Product']['url'];
				$new_url = '/' . str_replace($remove[0], '', $product['Product']['url']);
				// nachystam si presmerovani
				$new_redirect = array(
					'Redirect' => array(
						'request_uri' => $old_url,
						'target_uri' => $new_url
					)
				);
				$this->Redirect->create();
debug($new_redirect);
				if (!$this->Redirect->save($new_redirect)) {
					$data_source->rollback($this->Product);
					debug($new_redirect);
					debug($this->Redirect->validationErrors);
					die('nepodarilo se ulozit NOVY redirect');
				}
				
				// existuje presmerovani s upravovanou adresu?
				$old_redirects = $this->Redirect->find('all', array(
					'conditions' => array('Redirect.target_uri' => $old_url),
					'contain' => array(),
					'fields' => array('Redirect.id')
				));
debug($old_redirects);
				// upravim stavajici presmerovani, aby smerovala na novou adresu
				foreach ($old_redirects as $old_redirect) {
					$old_redirect['Redirect']['target_uri'] = $new_url;
					if (!$this->Redirect->save($old_redirect)) {
						$data_source->rollback($this->Product);
						debug($old_redirect);
						debug($this->Redirect->validationErrors);
						die('nepodarilo se ulozit UPRAVENY redirect');
					}
				}
				// nahradim retezce i v textovych polich
debug($product);
				foreach ($remove_strings as $remove_string) {
					$product['Product']['name'] = trim(preg_replace('/' . $remove_string . '/i', '', $product['Product']['name']));
					$product['Product']['breadcrumb'] = trim(preg_replace('/' . $remove_string . '/i', '', $product['Product']['breadcrumb']));
					$product['Product']['related_name'] = trim(preg_replace('/' . $remove_string . '/i', '', $product['Product']['related_name']));
					$product['Product']['heading'] = trim(preg_replace('/' . $remove_string . '/i', '', $product['Product']['heading']));
					$product['Product']['title'] = trim(preg_replace('/' . $remove_string . '/i', '', $product['Product']['title']));
					$product['Product']['zbozi_name'] = trim(preg_replace('/' . $remove_string . '/i', '', $product['Product']['zbozi_name']));
					$product['Product']['heureka_name'] = trim(preg_replace('/' . $remove_string . '/i', '', $product['Product']['heureka_name']));
				}
				
				$product['Product']['url'] = $new_url;
				$product['Product']['url'] = preg_replace('/^\//', '', $product['Product']['url']);
debug($product);
				if (!$this->Product->save($product)) {
					$data_source->rollback($this->Product);
					debug($product);
					debug($this->Product->validationErrors);
					die('nepodarilo se ulozit upraveny produkt');
				}
				$data_source->commit($this->Product);
//die();
			}
			die('jeden remove');
		}
		die('asd');
	}
} // konec tridy
?>
