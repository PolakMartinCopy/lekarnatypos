<?php
class ManufacturersController extends AppController {

	var $name = 'Manufacturers';
	var $helpers = array('Html', 'Form', 'Javascript' );


	function admin_index() {
		$count = $this->Manufacturer->find('count');
		$this->paginate = array(
			'conditions' => array('Manufacturer.active' => true),
			'limit' => $count,
			'contain' => array(),
			'order' => array('Manufacturer.name' => 'asc')
		);
		$manufacturers = $this->paginate();
		$this->set('manufacturers', $manufacturers);		
		
		$this->layout = REDESIGN_PATH . 'admin';		
	}
	
	function admin_add() {
		$this->set('tiny_mce_elements', 'ManufacturerText');
		if (!empty($this->data)) {
			$this->Manufacturer->create();
	
			// hledam jestli v databazi uz neni takova hodnota
			if ( $this->Manufacturer->hasAny(array('name' => $this->data['Manufacturer']['name'])) ){
				$this->Session->setFlash('Hodnota "' . $this->data['Manufacturer']['name'] . '" již v databázi figuruje.', REDESIGN_PATH . 'flash_failure');
			} else {
				if ( $this->Manufacturer->save($this->data) ) {
					$this->Session->setFlash('Výrobce byl uložen.', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('action'=>'index'), null, true);
				} else {
					$this->Session->setFlash('Výrobce nemohl být uložen, vyplňte prosím správně všechna pole.', REDESIGN_PATH . 'flash_failure');
				}
			}
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id){
		if (!$id) {
			$this->Session->setFlash('Neznámý výrobce.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$manufacturer = $this->Manufacturer->find('first', array(
			'conditions' => array('Manufacturer.id' => $id),
			'contain' => array()
		));
		
		if (empty($manufacturer)) {
			$this->Session->setFlash('Neznámý výrobce.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$this->set('tiny_mce_elements', 'ManufacturerText');
		
		if (!empty($this->data)) {
			if ($this->Manufacturer->save($this->data)) {
				$this->Session->setFlash('Výrobce byl uložen.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash('Výrobce nemohl být uložen, vyplňte prosím správně všechna pole.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $manufacturer;
		}
		
		$this->set('tinyMceElement', 'ManufacturerContent');
		$this->layout = REDESIGN_PATH . 'admin';
	}

	// soft delete
	function admin_delete($id = null){
		if (!$id) {
			$this->Session->setFlash('Neznámý výrobce.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		if (!$this->Manufacturer->hasAny(array('Manufacturer.id' => $id))) {
			$this->Session->setFlash('Neexistující výrobce.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$manufacturer = array(
			'Manufacturer' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		if ($this->Manufacturer->save($manufacturer)) {
			$this->Session->setFlash('Výrobce byl smazán.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Výrobce se nepodařilo smazat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action'=>'index'));
	}
	
	// vypise rejstrik vyrobcu s poctem produktu
	function index() {
		$customer_type_id = $this->Manufacturer->Product->CustomerTypeProductPrice->CustomerType->get_id($this->Session->read());
		
		$this->Manufacturer->virtualFields['products_count'] = 'COUNT(Product.id)';
		$manufacturers = $this->Manufacturer->find('all', array(
			'conditions' => array(
				'Product.active' => true,
				'Category.active' => true,
				$this->Manufacturer->Product->price . ' > 0',
				'Manufacturer.name !=' => ''
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions' => array('Product.manufacturer_id = Manufacturer.id')
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
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('Product.id = CategoriesProduct.product_id')
				),
				array(
					'table' => 'categories',
					'alias' => 'Category',
					'type' => 'INNER',
					'conditions' => array('Category.id = CategoriesProduct.category_id')
				),
			),
			'fields' => array('Manufacturer.id', 'Manufacturer.name', 'Manufacturer.products_count'),
			'group' => array('Manufacturer.id'),
		));
		
		foreach ($manufacturers as $index => &$manufacturer) {
			$manufacturer['Manufacturer']['url'] = $this->Manufacturer->get_url($manufacturer['Manufacturer']['id']);
			$manufacturer['Manufacturer']['name'] = trim(mb_convert_case(mb_strtolower($manufacturer['Manufacturer']['name']), MB_CASE_TITLE));
		}
		usort($manufacturers, array('Manufacturer', 'sortByName'));

		$this->set('manufacturers', $manufacturers);
		
		$this->layout = REDESIGN_PATH . 'content';
		$this->set('_title', 'Výrobci');
		$this->set('_description', 'LékárnaTypos CZ prodává zboží těchto výrobců');
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Výrobci', 'href' => '/vyrobci')
		);
		$this->set('breadcrumbs', $breadcrumbs);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán výrobce, jehož produkty chcete vypsat', REDESIGN_PATH . 'flash_failure');
			$this->redirect('/');
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
		
		$manufacturer = $this->Manufacturer->find('first', array(
			'conditions' => array(
				'Manufacturer.id' => $id,
				'Manufacturer.active' => true
			),
			'contain' => array(),
		));
		
		if (empty($manufacturer)) {
			$this->cakeError('error404');
		}

		$manufacturer['Manufacturer']['url'] = $this->Manufacturer->get_url($manufacturer['Manufacturer']['id']);
		$this->set('manufacturer', $manufacturer);
		$this->set('opened_manufacturer_id', $id);

		$this->layout = REDESIGN_PATH . 'content';
		$this->set('_title', $manufacturer['Manufacturer']['title']);
		$this->set('_description', $manufacturer['Manufacturer']['description']);
		
		$this->Session->write('categories_bothers_tab', 'manufacturers');
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Výrobci', 'href' => '/vyrobci'),
			array('anchor' => $manufacturer['Manufacturer']['name'], 'href' => '/' . $manufacturer['Manufacturer']['url'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		$conditions = array(
			'Product.manufacturer_id' => $id,
			// chci jen aktivni produkty
			'Product.active' => true,
			'Category.active' => true
		);
				
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
				
		// musim tedy vybrat nejlevnejsi a nejdrazsi produkt v podle dosavadnich podminek, protoze jakmile tam prihodim podminky o cene, zkresli mi to hodnoty pro slider
		// nejlevnejsi a nejdrazsi produkt pro ucely filtru podle ceny
		$cheapest_product = $this->Manufacturer->Product->cheapest($conditions, $customer_type_id);
		$cheapest_product_price = 0;
		if (!empty($cheapest_product)) {
			$cheapest_product_price = $cheapest_product['Product']['price'];
		}
		$this->set('cheapest_price', $cheapest_product_price);
				
		$most_expensive_product = $this->Manufacturer->Product->most_expensive($conditions, $customer_type_id);
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
				'conditions' => array('Product.id = CategoriesProduct.product_id')
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
			'limit' => 15
		);
				
		// sestavim podminku pro razeni podle toho, co je vybrano
		$order = array('Availability.cart_allowed' => 'desc');
		if (isset($_GET['filter']['sorting']) && !empty($_GET['filter']['sorting'])) {
			$order = array_merge($order, $this->Manufacturer->Product->sorting_options[$_GET['filter']['sorting'][0]]['conditions']);
		} else {
			$order = array_merge($order, array('Product.is_akce' => 'desc', 'Product.priority' => 'asc', 'Product.price' => 'asc'));
			$_GET['filter']['sorting'][0] = 0;
		}
				
		$this->paginate['Product']['order'] = $order;
		
		$this->Manufacturer->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
		// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
		// vezmu jako cenu produktu obycejnou cenu
		$this->Manufacturer->Product->virtualFields['price'] = $this->Manufacturer->Product->price;
		// sleva
		$this->Manufacturer->Product->virtualFields['discount'] = $this->Manufacturer->Product->discount;
		
		$products = $this->paginate('Product');
		foreach ($products as &$product) {
			$product['Product']['free_shipping_min_quantity'] = $this->Manufacturer->Product->minQuantityFreeShipping($product['Product']['id']);
		}
		
		// opetovne vypnuti virtualnich poli, nastavenych za behu
		unset($this->Manufacturer->Product->virtualFields['sold']);
		unset($this->Manufacturer->Product->virtualFields['price']);
		unset($this->Manufacturer->Product->virtualFields['discount']);

		$this->set('products', $products);
		
		// nejprodavanejsi produkty
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		$most_sold = $this->Manufacturer->most_sold_products($id, $customer_type_id);
		$this->set('most_sold_products', $most_sold);
		
		$this->set('sorting_options', $this->Manufacturer->Product->sorting_options);
		
		$this->set('listing_style', 'products_listing_grid');
	}
	
	function ajax_get_url() {
		$result = array(
			'success' => false,
			'message' => null	
		);
		
		if (!isset($_POST['id'])) {
			$result['message'] = 'Neznámý výrobce';
		} else {
			$id = $_POST['id'];
			if ($this->Manufacturer->hasAny(array('Manufacturer.id' => $id))) {
				$result['success'] = true;
				$result['message'] = $this->Manufacturer->get_url($id);
			} else {
				$result['message'] = 'Neexistující výrobce';
			}
		}
		
		echo json_encode($result);
		die();
	}

	function admin_repair() {
		// pro kazdy nazev najdu reprezentanta (pokud mozno, aktivniho) - pivota
		$unique_names = $this->Manufacturer->find('list');
		$unique_names = array_unique($unique_names);
		// kazdou tridu vyrobcu, urcenou nazvem, presmeruju na pivota a ostatni smazu
		foreach ($unique_names as $index => $name) {
			$class = $this->Manufacturer->find('all', array(
				'conditions' => array('Manufacturer.name' => $name, 'Manufacturer.id !=' => $index),
				'contain' => array(),
				'fields' => array('Manufacturer.id', 'Manufacturer.name')
			));
			if (!empty($class)) {
				foreach ($class as $duplicity) {
					$products = $this->Manufacturer->Product->find('all', array(
						'conditions' => array('Product.manufacturer_id' => $duplicity['Manufacturer']['id']),
						'contain' => array(),
						'fields' => array('Product.id', 'Product.name')
					));
					
					if (!empty($products)) {
						$save = array();
						// presmeruju produkty na pivota
						foreach ($products as $product) {
							$product['Product']['manufacturer_id'] = $index;
							$save[] = $product['Product'];
						}
						if (!$this->Manufacturer->Product->saveAll($save)) {
							debug($save);
							die();
						}
					}
					
					// duplicitni vyrobce smazu
					$this->Manufacturer->delete($duplicity['Manufacturer']['id']);
				}
			}
		}
		die('here');
	}
	
	// fce, ktera mi vsechny produkty sante nastavi jako skladem
	function sante_in_stock() {
		die('manufacturers/sante_in_stock - odstranit po uprave nacasovani cronu');
		$prev_availability_id = 9;
		$next_availability_id = 1;
		$manufacturer_id = 108;
		
		if (!$this->Manufacturer->changeAvailability($manufacturer_id, $next_availability_id, $prev_availability_id)) {
			die('nepodarilo se nastavit sante produkty jako "skladem"');
		}
		die('odstranit po otestovani - sante_in_stock');
	}
	
	// fce, ktera mi vsechny produkty sante nastavi jako skladem
	function sante_in_2_days() {
		die('manufacturers/sante_in_2_days - odstranit po uprave nacasovani cronu');
		$prev_availability_id = 1;
		$next_availability_id = 9;
		$manufacturer_id = 108;
	
		if (!$this->Manufacturer->changeAvailability($manufacturer_id, $next_availability_id, $prev_availability_id)) {
			die('nepodarilo se nastavit sante produkty jako "do 2 dnu"');
		}
		die('odstranit po otestovani - sante_in_2_days');
	}	
}
?>
