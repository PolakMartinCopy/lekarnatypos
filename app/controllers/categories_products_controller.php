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

		if (isset($_GET['filter']['reset_filter'])) {
			$url = $this->params['url']['url'];
			$params = $this->params['url']['filter'];
			switch ($_GET['filter']['reset_filter']) {
				case 'brand': 
					unset($params['manufacturer_id']);
					break;
				case 'price':
					unset($params['price']);
					break;
				case 'sorting':
					unset($params['sorting']);
					break;
			}
			unset($params['reset_filter']);
			$params = array('filter' => $params);
			$params = http_build_query($params);
			$url = '/' . $url . '?' . $params;
			$this->redirect($url);
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

		// hned za korenem mam kategorie "NOVE KATEGORIE" a "PRIZNAKY", ktere ale nechci zobrazovat
		if ($path[1]['Category']['id'] == $this->CategoriesProduct->Category->category_subtree_root_id) {
			$this->Session->write('categories_bothers_tab', 'categories');		
		} else {
			$this->Session->write('categories_bothers_tab', 'bothers');
		}
		unset($path[1]);

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

		// k informaci o kategorii pridam univerzalni text
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
		
		// podminky pro vypis produktu
		$category_ids = $this->CategoriesProduct->Category->children($category['Category']['id']);
		$category_ids = Set::extract('/Category/id', $category_ids);
		$category_ids[] = $id;
		$conditions = array(
			'CategoriesProduct.category_id' => $category_ids,
			'Product.active' => true,
			'Category.active' => true
		);
		
		// DO TETO CHVILE MAM PODMINKY PRO VYBRANI PRODUKTU NEOMEZUJICI PODLE FILTRU
		$filter_manufacturers_order = array('Manufacturer.name' => 'asc');
		$filter_manufacturers_conditions = array_merge($conditions, array('Manufacturer.active' => true));
		$filter_manufacturers = $this->CategoriesProduct->Product->Manufacturer->filter_manufacturers($filter_manufacturers_conditions, $filter_manufacturers_order);
		$this->set('filter_manufacturers', $filter_manufacturers);
		
		// musim tedy vybrat nejlevnejsi a nejdrazsi produkt v podle dosavadnich podminek, protoze jakmile tam prihodim podminky o cene, zkresli mi to hodnoty pro slider
		// nejlevnejsi a nejdrazsi produkt pro ucely filtru podle ceny
		$cheapest_product = $this->CategoriesProduct->Product->cheapest($conditions, $customer_type_id);
		$cheapest_product_price = 0;
		if (!empty($cheapest_product)) {
			$cheapest_product_price = $cheapest_product['Product']['price'];
		}
		$this->set('cheapest_price', $cheapest_product_price);
		
		$most_expensive_product = $this->CategoriesProduct->Product->most_expensive($conditions, $customer_type_id);
		$most_expensive_product_price = 1000;
		if (!empty($most_expensive_product)) {
			$most_expensive_product_price = $most_expensive_product['Product']['price'];
		}
		$this->set('most_expensive_price', $most_expensive_product_price);
		
		if (isset($_GET['filter']['price']['min']) && !empty($_GET['filter']['price']['min'])) {
			$conditions['Product.price >='] = $_GET['filter']['price']['min'];
		}
		if (isset($_GET['filter']['price']['max']) && !empty($_GET['filter']['price']['max'])) {
			$conditions['Product.price <='] = $_GET['filter']['price']['max'];
		}
		
		// filtr podle vyrobcu
		if (isset($_GET['filter']['manufacturer_id']) && !empty($_GET['filter']['manufacturer_id'])) {
			$manufacturer_id_arr = $_GET['filter']['manufacturer_id'];
			$conditions = array_merge($conditions, array('Product.manufacturer_id' => $manufacturer_id_arr));
//			$this->data['CategoriesProduct']['manufacturer_id'] = $manufacturer_id;
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
				'table' => 'categories',
				'alias' => 'Category',
				'type' => 'INNER',
				'conditions' => array('Category.id = CategoriesProduct.category_id')
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
				'table' => 'customer_type_product_prices',
				'alias' => 'CustomerTypeProductPriceCommon',
				'type' => 'LEFT',
				'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
			),
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'INNER',
				'conditions' => array('Availability.id = Product.availability_id')
			),
			array(
				'table' => 'manufacturers',
				'alias' => 'Manufacturer',
				'type' => 'LEFT',
				'conditions' => array('Manufacturer.id = Product.manufacturer_id')
			)
		);

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
				'Product.discount',
				'Product.rate',
				'Product.is_akce',
				'Product.is_novinka',
				'Product.is_doprodej',
				'Product.is_bestseller',
				'Product.is_darek_zdarma',
							
				'Image.id',
				'Image.name',
		
				'Availability.id',
				'Availability.cart_allowed'
		
			),
			'joins' => $joins,
			'group' => 'Product.id',
//			'show' => 'all',
			'limit' => 15
		);

		// sestavim podminku pro razeni podle toho, co je vybrano
		$order = array('Availability.cart_allowed' => 'desc');
		if (isset($_GET['filter']['sorting']) && !empty($_GET['filter']['sorting'])) {
			$order = array_merge($order, $this->CategoriesProduct->Product->sorting_options[$_GET['filter']['sorting'][0]]['conditions']);
		} else {
			$order = array_merge($order, array('Product.is_akce' => 'desc', 'Product.priority' => 'asc'));
			$_GET['filter']['sorting'][0] = 0;
		}

		$this->paginate['Product']['order'] = $order;

		$this->CategoriesProduct->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
		// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
		// vezmu jako cenu produktu obycejnou cenu
		$this->CategoriesProduct->Product->virtualFields['price'] = $this->CategoriesProduct->Product->price;
		// sleva
		$this->CategoriesProduct->Product->virtualFields['discount'] = $this->CategoriesProduct->Product->discount;

		$products = $this->paginate('Product');
		foreach ($products as &$product) {
			$product['Product']['free_shipping_min_quantity'] = $this->CategoriesProduct->Product->minQuantityFreeShipping($product['Product']['id']);
		}

		// opetovne vypnuti virtualnich poli, nastavenych za behu
		unset($this->CategoriesProduct->Product->virtualFields['sold']);
		unset($this->CategoriesProduct->Product->virtualFields['price']);
		unset($this->CategoriesProduct->Product->virtualFields['discount']);
		$this->set('products', $products);
		
		// je zvoleny nejaky tab filtru, ktery ma byt zobrazeny?
		$filter_tab = false;
		if (isset($_GET['filter']['default_tab'])) {
			$filter_tab = $_GET['filter']['default_tab'];
		}
		$this->set('filter_tab', $filter_tab);
		
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
		// vyfiltruju prazdne kategorie
		$subcategories = $this->CategoriesProduct->Category->filterEmpty($subcategories);
		
		foreach ($subcategories as &$subcategory) {
			if (!empty($subcategory['Category']['image'])) {
				$subcategory['Category']['image'] = $this->CategoriesProduct->Category->image_path . DS . $subcategory['Category']['image'];
				if (!file_exists($subcategory['Category']['image'])) {
					$subcategory['Category']['image'] = 'img' . REDESIGN_PATH . 'category_image_na.jpg';
				}
			} else {
				$subcategory['Category']['image'] = 'img' . DS . REDESIGN_PATH . 'category_image_na.jpg';
			}
				
		}

		$this->set('subcategories', $subcategories);
		
		$this->set('sorting_options', $this->CategoriesProduct->Product->sorting_options);
		// trackovani prohlednutych kategorii
//		$this->CategoriesProduct->Category->TSVisitCategory->myCreate($id);
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
