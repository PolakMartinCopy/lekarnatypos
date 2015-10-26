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
		$limit = 2;
		// idcka kategorii s darky, abych darky nezobrazoval ve vypisu
		$present_category_ids = $this->Product->CategoriesProduct->Category->subtree_ids($this->Product->CategoriesProduct->Category->present_category_id);
	
		$this->Product->virtualFields['price'] = $this->Product->price;
	
		$products = $this->Product->find('all', array(
			'conditions' => array(
				'Product.manufacturer_id' => $id,
				'Image.is_main' => true,
				'Product.active' => true,
				'CategoriesProduct.category_id NOT IN (' . implode(',', $present_category_ids) . ')'
			),
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
}
?>
