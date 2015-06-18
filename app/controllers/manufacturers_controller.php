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

	function view($id = null) {
		// navolim si layout, ktery se pouzije
		$this->layout = REDESIGN_PATH . 'category';
		
		// nastaveni formu pro vyber poctu produktu na strance
		define('ALL_STRING', 'vše');
		$paging_options = array(0 => 16, 24, 32, ALL_STRING);
		$this->set('paging_options', $paging_options);
		
		$sorting_options = $this->Manufacturer->Product->sorting_options;
		$this->set('sorting_options', $sorting_options);
		
		$sorting = 0;
		if (isset($_GET['sorting'])) {
			$sorting = $_GET['sorting'];
		}
		$this->data['Manufacturer']['sorting'] = $sorting;
		
		$paging = 0;
		if (isset($_GET['paging'])) {
			$paging = $_GET['paging'];
		}
		$this->data['Manufacturer']['paging'] = $paging;
		
		// nastavim si pro menu IDecko kategorie,
		// kterou momentalne prohlizim
		$this->set('opened_category_id', ROOT_CATEGORY_ID);
		// nactu si info o vyrobci
		$manufacturer = $this->Manufacturer->find('first', array(
			'conditions' => array('Manufacturer.id' => $id, 'Manufacturer.active' => true),
			'contain' => array(),
			'fields' => array('Manufacturer.id', 'Manufacturer.name')
		));
		
		if (empty($manufacturer)) {
			$this->Session->setFlash('Neexistující výrobce.', REDESIGN_PATH . 'flash_failure');
			$this->redirect('/');
		}
		
		$this->set('manufacturer', $manufacturer);
		// nastavim breadcrumbs
		$breadcrumbs[] = array('href' => '/' . $this->Manufacturer->get_url($id), 'anchor' => $manufacturer['Manufacturer']['name']);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// nastavim head tagy
		$_title = $manufacturer['Manufacturer']['name'];
		$this->set('_title', $_title);
		$_description = $this->Manufacturer->get_description($id);
		$this->set('_description', $_description);

		// nastavim zobrazovany banner
		$this->set('category_banner', unserialize(CATEGORY_BANNER));
		
		// nejprodavanejsi produkty
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		$manufacturer_most_sold = $this->Manufacturer->most_sold_products($id, $customer_type_id);
		$this->set('manufacturer_most_sold', $manufacturer_most_sold);
		
		$limit = $paging_options[$this->data['Manufacturer']['paging']];
		// idcka kategorii s darky, abych darky nezobrazoval ve vypisu
		$present_category_ids = $this->Manufacturer->Product->CategoriesProduct->Category->subtree_ids($this->Manufacturer->Product->CategoriesProduct->Category->present_category_id);

		$conditions = array(
			'Product.manufacturer_id' => $id,
			'Product.active' => true,
			'Product.price >' => 0,
			'CategoriesProduct.category_id NOT IN (' . implode(',', $present_category_ids) . ')' 
		);
		
		if (isset($_GET['manufacturer_id']) && !empty($_GET['manufacturer_id'])) {
			$manufacturer_id = $_GET['manufacturer_id'];
			$conditions = array_merge($conditions, array('Product.manufacturer_id' => $manufacturer_id));
			$this->data['Manufacturer']['manufacturer_id'] = $manufacturer_id;
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
				'type' => 'LEFT',
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
			$conditions = array_merge($conditions, array('AttributesSubproduct.attribute_id' => $attribute_id));
			$this->data['Manufacrurer']['attribute_id'] = $attribute_id;
			
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
			'limit' => $limit
		);
		// pokud je vybrano, ze se maji vypsat vsechny produkty
		if ($limit == ALL_STRING) {
			$this->paginate['Product']['show'] = 'all';
		}
		
		// sestavim podminku pro razeni podle toho, co je vybrano
		$order = array('Availability.cart_allowed' => 'desc');
		if (isset($this->data['Manufacturer']['sorting'])) {
			switch ($this->data['Manufacturer']['sorting']) {
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

		$this->Manufacturer->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
		// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
		// vezmu jako cenu produktu obycejnou cenu
		$this->Manufacturer->Product->virtualFields['price'] = $this->Manufacturer->Product->price;
		$products = $this->paginate('Product');
		// opetovne vypnuti virtualnich poli, nastavenych za behu
		unset($this->Manufacturer->Product->virtualFields['sold']);
		unset($this->Manufacturer->Product->virtualFields['price']);

		$this->set('products', $products);

		$listing_style = 'products_listing_grid';
		$this->set('listing_style', $listing_style);
		
		$action_products = $this->Manufacturer->Product->get_action_products($customer_type_id, 4);
		$this->set('action_products', $action_products);
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
	
	function admin_import() {
		$this->Manufacturer->import();
		die('here');
	}
}
?>
