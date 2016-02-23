<?php
class Manufacturer extends AppModel {
	var $name = 'Manufacturer';
	
	var $actsAs = array('Containable');

	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'message' => 'Pole pro název výrobce nesmí zůstat prázdné!'
		),
		'www_address' => array(
			'rule' => array('url'),
			'allowEmpty' => true,
			'message' => 'Uveďte www adresu ve správném formátu, nebo nechte pole prázdné.'
		)
	);
	
	var $hasMany = array('Product');
	
	var $order = array('Manufacturer.name' => 'asc');
	
	var $filter_limit = null;
	
	function beforeValidate() {
		// udelam si kontrolu, jestli je vyplneny titulek
		if (array_key_exists('title', $this->data['Manufacturer']) && empty($this->data['Manufacturer']['title'])){
			$this->data['Manufacturer']['title'] = $this->data['Manufacturer']['name'];
		}
		// udelam si kontrolu, jestli je vyplneny titulek a url
		if (array_key_exists('title', $this->data['Manufacturer']) && empty($this->data['Manufacturer']['heading'])){
			$this->data['Manufacturer']['heading'] = $this->data['Manufacturer']['name'];
		}
		// udelam si kontrolu, jestli je vyplneny titulek a url
		if (array_key_exists('title', $this->data['Manufacturer']) && empty($this->data['Manufacturer']['description'])) {
			$this->data['Manufacturer']['description'] = $this->buildDescription($this->data['Manufacturer']['name']);
		}
	}
	
	function afterFind($results) {
		if (!isset($results['Manufacturer'])) {
			foreach ($results as &$result) {
				if (isset($result['Manufacturer']) && array_key_exists('heading', $result['Manufacturer']) && empty($result['Manufacturer']['heading']) && array_key_exists('name', $result['Manufacturer']) && !empty($result['Manufacturer']['name'])) {
					$result['Manufacturer']['heading'] = $result['Manufacturer']['name'];
				}
				if (isset($result['Manufacturer']) && array_key_exists('title', $result['Manufacturer']) && empty($result['Manufacturer']['title']) && array_key_exists('name', $result['Manufacturer']) && !empty($result['Manufacturer']['name'])) {
					$result['Manufacturer']['title'] = $result['Manufacturer']['name'];
				}
				if (isset($result['Manufacturer']) && array_key_exists('description', $result['Manufacturer']) && empty($result['Manufacturer']['description']) && array_key_exists('name', $result['Manufacturer']) && !empty($result['Manufacturer']['name'])) {
					$result['Manufacturer']['description'] = $this->buildDescription($result['Manufacturer']['name']);
				}
			}
		}
		return $results;
	}
	
	function get_url($id) {
		$url = '';
		$manufacturer = $this->find('first', array(
			'conditions' => array('Manufacturer.id' => $id),
			'contain' => array(),
			'fields' => array('Manufacturer.id', 'Manufacturer.name')
		));
		
		if (!empty($manufacturer)) {
			$url = strip_diacritic($manufacturer['Manufacturer']['name'] . '-v' . $manufacturer['Manufacturer']['id']);
		}
		
		return $url;
	}
	
	function get_description($id) {
		$description = '';
		
		$manufacturer = $this->find('first', array(
			'conditions' => array('Manufacturer.id' => $id),
			'contain' => array(),
			'fields' => array('Manufacturer.id', 'Manufacturer.name')
		));
		
		if (!empty($manufacturer)) {
			$description = 'Sportovní výživa a doplňky stravy pro sportovce ' . $manufacturer['Manufacturer']['name'];
		}
		
		return $description;
	}
	
	function filter_manufacturers($conditions, $order = null) {
		if (!$order) {
			$order = array('Manufacturer.products_count' => 'desc', 'Manufacturer.name' => 'asc');
		}
		$limit = $this->filter_limit;
		$this->virtualFields['products_count'] = 'COUNT(DISTINCT(Product.id))';
		// chci $limit vyrobcu, kteri maji (pro danou kategorii) nejvice produktu (do filtru ve vypisu produktu)
		$filter_manufacturers = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions' => array('Manufacturer.id = Product.manufacturer_id')
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
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
				)
			),
			'fields' => array('Manufacturer.id', 'Manufacturer.name', 'Manufacturer.products_count'),
			'limit' => $limit,
			'group' => array('Manufacturer.id'),
			'order' => $order
		));
		// odnastavim virtualni pole vytvorena za behu
		unset($this->virtualFields['products_count']);
		return $filter_manufacturers;
	}
	
	function most_sold_products($id = null, $customer_type_id = null) {
		if (!$id) {
			return false;
		}
		// chci 2 nejprodavanejsi produkty dane kategorie
		$limit = 8;
		$year_ago = date('Y-m-d', strtotime('-1 year'));

		$conditions = array(
			'Product.manufacturer_id' => $id,
			'Image.is_main' => true,
			'Product.active' => true,
			'DATE(OrderedProduct.created) >' => $year_ago
		);
		
		// idcka kategorii s darky, abych darky nezobrazoval ve vypisu
		if ($this->Product->CategoriesProduct->Category->present_category_id) {
			$present_category_ids = $this->Product->CategoriesProduct->Category->subtree_ids($this->Product->CategoriesProduct->Category->present_category_id);
			$conditions[] = 'CategoriesProduct.category_id NOT IN (' . implode(',', $present_category_ids) . ')';
		}
	
		$this->Product->virtualFields['price'] = $this->Product->price;
	
		$products = $this->Product->find('all', array(
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
				'Product.rate',
					
				'Image.id',
				'Image.name',
	
				'SUM(OrderedProduct.product_quantity) AS total_quantity'
			),
			'joins' => array(
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
					'conditions' => array('Image.product_id = Product.id')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
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
			'limit' => $limit,
			'group' => 'Product.id',
			'order' => array('total_quantity' => 'desc')
		));
	
		return $products;
	}
	
	function redirect_url($url) {
		$redirect_url = '/';
		// zjistim na co chci presmerovat
		// odstranim cast adresy, ktera mi urcuje, ze se jedna o produkt
		$pattern = preg_replace('/^\/manufacturer\//', '', $url);
	
		// vytahnu si id produktu na sportnutritionu
		if (preg_match('/^[^:]+:(\d+)/', $pattern, $matches)) {
			$sn_id = $matches[1];

			// najdu nas kategorii odpovidajici sn adrese
			$manufacturer = $this->find('first', array(
				'conditions' => array('Manufacturer.sportnutrition_id' => $sn_id),
				'contain' => array(),
				'fields' => array('Manufacturer.id', 'Manufacturer.name')
			));

			if (!empty($manufacturer)) {
				// vratim url pro presmerovani
				$redirect_url = strip_diacritic($manufacturer['Manufacturer']['name'] . '-v' . $manufacturer['Manufacturer']['id']);
			}
		}
	
		return $redirect_url;
	}
	
	function getSidebarMenu($opened_manufacturer_id, $limit = 40) {
		$customer_type_id = 2;
		// nejdriv vytahnu ty, ktere mam natvrdo definovane, ze tam chci
		$defManufacturers = $this->find('all', array(
			'conditions' => array(
				'Manufacturer.is_sidebar' => true,
				'Manufacturer.active' => true
			),
			'contain' => array(),
			'fields' => array('Manufacturer.id', 'Manufacturer.name')
		));
		
		$conditions = array(
			'Product.active' => true,
			'Category.active' => true,
			$this->Product->price . ' > 0'
		);
		
		if (!empty($defManufacturers)) {
			$limit -= count($defManufacturers);;
			$defManufacturersIds = Set::extract('/Manufacturer/id', $defManufacturers);
			$conditions[] = 'Manufacturer.id NOT IN (' . implode(',', $defManufacturersIds) . ')';
		}

		$diffManufacturers = $this->find('all', array(
			'conditions' => $conditions,
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
			'fields' => array('Manufacturer.id', 'Manufacturer.name', 'COUNT(Product.id) AS product_count'),
			'order' => array('product_count' => 'desc'),
			'group' => array('Manufacturer.id'),
			'limit' => $limit
		));

		$manufacturers = array_merge($defManufacturers, $diffManufacturers);
		$res = array();

		foreach ($manufacturers as $index => $manufacturer) {
			$url = $this->get_url($manufacturer['Manufacturer']['id']);
			// musim prekladat data, abych mohl pouzit element pro vypis kategorii
			$res[] = array(
				'Category' => array(
					'id' => $manufacturer['Manufacturer']['id'],
					'url' => $url,
					'name' => mb_convert_case(mb_strtolower($manufacturer['Manufacturer']['name']), MB_CASE_TITLE),
					'children' => array()
				)
			);
		}
		usort($res, array('Category', 'sortByName'));

		$res['categories'] = $res;
		$res['path_ids'] = array(0 => $opened_manufacturer_id);

		return $res;
	}
	
	function getTitle($id) {
		$manufacturer = $this->find($first, array(
			'conditions' => array('Manufacturer.id' => $id),
			'contain' => array(),
			'fields' => array('Manufacturer.name', 'Manufacturer.title')
		));
		if (!empty($manufacturer)) {
			$title = $manufacturer['Manufacturer']['title'];
			if (empty($title)) {
				$title = $manufacturer['Manufacturer']['name'];
			}
			return $title;
		}
		return false;
	}
	
	function getHeading($id) {
		$manufacturer = $this->find($first, array(
			'conditions' => array('Manufacturer.id' => $id),
			'contain' => array(),
			'fields' => array('Manufacturer.name', 'Manufacturer.heading')
		));
		if (!empty($manufacturer)) {
			$heading = $manufacturer['Manufacturer']['heading'];
			if (empty($title)) {
				$heading = $manufacturer['Manufacturer']['heading'];
			}
			return $heading;
		}
		return false;
	}
	
	function getDescription($id) {
		$manufacturer = $this->find($first, array(
			'conditions' => array('Manufacturer.id' => $id),
			'contain' => array(),
			'fields' => array('Manufacturer.name', 'Manufacturer.description')
		));
		if (!empty($manufacturer)) {
			$description = $manufacturer['Manufacturer']['description'];
			if (empty($title)) {
				$description = $this->buildDescription($manufacturer['Manufacturer']['name']);
			}
			return $description;
		}
		return false;
	}
	
	// zmeni dostupnost produktu daneho vyrobce
	function changeAvailability($id, $nextAvailabilityId, $prevAvailabilityId = null) {
		$conditions = array(
			'Product.manufacturer_id' => $id,
			'Product.active' => true
		);
		if ($prevAvailabilityId) {
			$conditions['Product.availability_id'] = $prevAvailabilityId;
		}

		$products = $this->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Product.id')
		));

		$save = array();
		foreach ($products as $product) {
			$save[] = array(
				'id' => $product['Product']['id'],
				'availability_id' => $nextAvailabilityId
			);
		}
		return $this->Product->saveAll($save);
	}
	
	private static function buildDescription($name) {
		return 'Produkty výrobce ' . $name . ' najdete v nabídce e-shopu LekarnaTypos CZ';
	}
	

	static function sortByName($a, $b) {
		if ($a['Manufacturer']['name'] == $b['Manufacturer']['name']) {
			return 0;
		}
		return ($a['Manufacturer']['name'] < $b['Manufacturer']['name']) ? -1 : 1;
	}
}
?>
