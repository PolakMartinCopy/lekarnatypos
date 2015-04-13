<?php
class CategoriesProductsController extends AppController {
	var $name = 'CategoriesProducts';
	
	var $helpers = array('Form');
	
	var $paginate = array(
		'limit' => 50,
		'order' => array(
			'CategoriesProduct.product_id' => 'desc'
		),
	);
		
	function admin_add(){
		if (isset($this->data)){
			if ($this->CategoriesProduct->hasAny($this->data['CategoriesProduct'])) {
				$this->Session->setFlash('Produkt je již přiřazen do kategorie.', REDESIGN_PATH . 'flash_failure');
			} else {
				$this->CategoriesProduct->create();
				if ($this->CategoriesProduct->save($this->data)) {
					$this->Session->setFlash('Produkt byl přiřazen do kategorie.', REDESIGN_PATH . 'flash_success');
				} else {
					$this->Session->setFlash('Produkt nemohl být zkopírován, došlo k chybě.', REDESIGN_PATH . 'flash_failure');
				}
			}
			$this->redirect(array('controller' => 'products', 'action' => 'edit_categories', $this->data['CategoriesProduct']['product_id'], (isset($this->params['named']['category_id']) ? $this->params['named']['category_id'] : null)));
		} else {
			$this->Session->setFlash('Neznámý produkt.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
	}
		
	// presun produktu mezi kategoriemi
	function admin_edit($id){
		// presune produkt do vybrane kategorie
		if ( !isset($this->data) ){ // jeste nebyl odeslan form

			// nactu si data
			$this->data = $this->CategoriesProduct->read(null, $id);

			// nactu si strom kategorii
			$categories = $this->CategoriesProduct->Category->generatetreelist(array('not' => array('id' => array('1'))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories')));
		} else {
			if ($this->CategoriesProduct->save($this->data)) {
				$this->Session->setFlash('Produkt byl přesunut.');
				$this->redirect(array('controller' => 'categories', 'action' => 'list_products', $this->data['CategoriesProduct']['category_id']), null, true);
			} else {
				$this->Session->setFlash('Produkt nemohl být přesunut.');
			}
		}
	}
	
	// smaze prirazeni produktu do kategorie
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámé přiřazení do kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$categories_product = $this->CategoriesProduct->find('first', array(
			'conditions' => array('CategoriesProduct.id' => $id),
			'contain' => array(),
			'fields' => array('CategoriesProduct.id', 'CategoriesProduct.product_id')	
		));
		
		if (empty($categories_product)) {
			$this->Session->setFlash('Neexistující přiřazení do kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		if ($this->CategoriesProduct->delete($id)) {
			$this->Session->setFlash('Produkt byl odstraněn z kategorie.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo odstranit z kategorie.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'products', 'action' => 'edit_categories', $categories_product['CategoriesProduct']['product_id'], (isset($this->params['named']['category_id']) ? $this->params['named']['category_id'] : null)));
	}
	
	function view($id = null) {
		if (!$id) {
			die('neni zvolena kategorie, kterou chcete zobrazit');
		}
		
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'content';
		
		// nastavim si pro menu IDecko kategorie, kterou momentalne prohlizim
		$this->set('opened_category_id', $id);
		
		$path = $this->CategoriesProduct->Category->getPath($id);
		$breadcrumbs = array();
		
		if (!$path) {
			$this->cakeError('error404');
		}
		
		foreach ($path as $item) {
			$breadcrumb = array('anchor' => $item['Category']['breadcrumb'], 'href' => '/' . $item['Category']['url']);
			if ($item['Category']['id'] == 5) {
				$breadcrumb = array('anchor' => 'Domů', 'href' => HP_URI);
			}
			$breadcrumbs[] = $breadcrumb;
		}
		$this->set('breadcrumbs', $breadcrumbs);
		
		// nactu si info o kategorii
		$category = $this->CategoriesProduct->Category->find('first', array(
			'conditions' => array('Category.id' => $id, 'Category.active' => true),
			'contain' => array()	
		));
		
		if (empty($category)) {
			$this->cakeError('error404');
		}

		// k informaci o kategorii pridam text o sleve pro registrovane
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$category_text = $this->Setting->findValue('CATEGORYTEXT');
		
		$category['Category']['content'] = $category['Category']['content'] . ($category_text ? $category_text : '');
		$this->set('category', $category);
		
		// nastavim head tagy
		$_title = $category['Category']['title'];
		$_description = $category['Category']['description'];
		$this->set('_title', $_title);
		$this->set('_description', $_description);
		// nejprodavanejsi produkty
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		$category_most_sold = $this->CategoriesProduct->Category->get_most_sold($id, $customer_type_id);
		$this->set('category_most_sold_products', $category_most_sold);
		
		$category_ids = $this->CategoriesProduct->Category->children($category['Category']['id']);
		$category_ids = Set::extract('/Category/id', $category_ids);
		$category_ids[] = $id;
		
		$conditions = array(
			'CategoriesProduct.category_id' => $category_ids,
			'Product.active' => true,
			'Product.price >' => 0
		);

		if (isset($_GET['manufacturer_id']) && !empty($_GET['manufacturer_id'])) {
			$manufacturer_id = $_GET['manufacturer_id'];
			$manufacturer_id_arr = explode(',', $manufacturer_id);
			if ($this->CategoriesProduct->Product->Manufacturer->filter_limit && count($manufacturer_id_arr) == $this->CategoriesProduct->Product->Manufacturer->filter_limit) {
				$manufacturer_id = '';
			} else {
				$conditions = array_merge($conditions, array('Product.manufacturer_id' => $manufacturer_id_arr));
				$this->data['CategoriesProduct']['manufacturer_id'] = $manufacturer_id;
			}
		}

		$joins = array(
			array(
				'table' => 'ordered_products',
				'alias' => 'OrderedProduct',
				'type' => 'LEFT',
				'conditions' => array('OrderedProduct.product_id = Product.id')
			),
			array(
				'table' => 'categories_products',
				'alias' => 'CategoriesProduct',
				'type' => 'INNER',
				'conditions' => array('CategoriesProduct.product_id = Product.id')
			),
			array(
				'table' => 'images',
				'alias' => 'Image',
				'type' => 'LEFT',
				'conditions' => array('Image.product_id = Product.id AND Image.is_main = "1"')
			),
			array(
				'table' => 'customer_type_product_prices',
				'alias' => 'CustomerTypeProductPrice',
				'type' => 'LEFT',
				'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
			),
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'INNER',
				'conditions' => array('Availability.id = Product.availability_id')
			)
		);

		if (isset($_GET['attribute_id']) && !empty($_GET['attribute_id'])) {
			$attribute_id = $_GET['attribute_id'];
			$attribute_id_arr = explode(',', $attribute_id);
			$conditions = array_merge($conditions, array('AttributesSubproduct.attribute_id' => $attribute_id_arr));
			$this->data['CategoriesProduct']['attribute_id'] = $attribute_id;
			
			$add_joins = array(
				array(
					'table' => 'subproducts',
					'alias' => 'Subproduct',
					'type' => 'LEFT',
					'conditions' => array('Product.id = Subproduct.product_id'),
				),
				array(
					'table' => 'attributes_subproducts',
					'alias' => 'AttributesSubproduct',
					'type' => 'LEFT',
					'conditions' => array('Subproduct.id = AttributesSubproduct.subproduct_id')
				)
			);
			
			$joins = array_merge($joins, $add_joins);
		}

		$this->paginate['Product'] = array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.short_description',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.sold',
				'Product.price',
				'Product.rate',
					
				'Image.id',
				'Image.name',
				
				'Availability.id',
				'Availability.cart_allowed'

			),
			'joins' => $joins,
			'group' => 'Product.id',
		);

		
		$this->paginate['Product']['show'] = 'all';
		
		// sestavim podminku pro razeni podle toho, co je vybrano
		$order = array('Availability.cart_allowed' => 'desc');
		if (isset($this->data['CategoriesProduct']['sorting'])) {
			switch ($this->data['CategoriesProduct']['sorting']) {
				// vychozi razeni podle priority
				case 0: $order = array_merge($order, array('Product.priority' => 'asc')); break;
				// nastavim razeni podle prodejnosti
				case 1: $order = array_merge($order, array('Product.sold' => 'desc')); break;
				// nastavim razeni podle ceny
				case 2: $order = array_merge($order, array('Product.price' => 'asc')); break;
				case 3: $order = array_merge($order, array('Product.price' => 'desc')); break;
				// nastavim razeni podle nazvu
				case 4: $order = array_merge($order, array('Product.name' => 'asc')); break;
				default: $order = array();
			}
		}
		
		$this->paginate['Product']['order'] = $order;

		$this->CategoriesProduct->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
		// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
		// vezmu jako cenu produktu obycejnou cenu
		$this->CategoriesProduct->Product->virtualFields['price'] = $this->CategoriesProduct->Product->price;
		$products = $this->paginate('Product');

		// opetovne vypnuti virtualnich poli, nastavenych za behu
		unset($this->CategoriesProduct->Product->virtualFields['sold']);
		unset($this->CategoriesProduct->Product->virtualFields['price']);

		$this->set('products', $products);

		$listing_style = 'products_listing_grid';
		
		$this->set('listing_style', $listing_style);
		
//		$action_products = $this->CategoriesProduct->Product->get_action_products($customer_type_id, 4);
//		$this->set('action_products', $action_products);
		
		$subcategories_conditions = array(
			'public' => true,
			'active' => true,
			'parent_id' => $id
		);
		if ($this->Session->check('Customer')) {
			unset($subcategories_conditions['public']);
		}
		$subcategories = $this->CategoriesProduct->Category->find('all', array(
			'conditions' => $subcategories_conditions,
			'contain' => array(),
			'fields' => array('Category.id', 'Category.name', 'Category.url', 'Category.image'),
			'order' => array('Category.lft' => 'asc')
		));
		foreach ($subcategories as &$subcategory) {
			if (!empty($subcategory['Category']['image'])) {
				$subcategory['Category']['image'] = $this->CategoriesProduct->Category->image_path . DS . $subcategory['Category']['image'];
				if (!file_exists($subcategory['Category']['image'])) {
					$subcategory['Category']['image'] = 'img' . REDESIGN_PATH . 'category_image_na_150x150.jpg';
				}
			} else {
				$subcategory['Category']['image'] = 'img' . DS . REDESIGN_PATH . 'category_image_na_150x150.jpg';
			}
				
		}

		$this->set('subcategories', $subcategories);
	}
	
	function cancel_filter($id) {
		$this->Session->delete('filter');
		
		$this->CategoriesProduct->Category->contain();
		$category = $this->CategoriesProduct->Category->read(null, $id);
		$url = '/' . $category['Category']['url'];
		if (isset($this->params['named']['ls']) && $this->params['named']['ls'] == 'list') {
			$url .= '/ls:list';
		}
		$this->redirect($url);
	}
	
	function sort_by_availability_and_price($a, $b) {
		if ($a['Product']['Availability']['cart_allowed'] && !$b['Product']['Availability']['cart_allowed']) {
			return -1;
		} elseif (!$a['Product']['Availability']['cart_allowed'] && $b['Product']['Availability']['cart_allowed']) {
			return 1;
		} elseif ($a['Product']['Availability']['cart_allowed'] == $b['Product']['Availability']['cart_allowed']) {
			return $a['Product']['discount_price'] > $b['Product']['discount_price'];
		}
	}
	
	function admin_import($truncate = true) {
		$this->CategoriesProduct->import($truncate);
		die('here');
	}
}
?>