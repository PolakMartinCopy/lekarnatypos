<?php 
class MostSoldProduct extends AppModel {
	var $name = 'MostSoldProduct';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => false
		)
	);
	
	var $belongsTo = array('Product');
	
	var $limit = 15;
	
	var $image_path = 'images/most_sold_products';
	
	function isMaxReached($gender) {
		$count = $this->find('count', array(
			'conditions' => array('MostSoldProduct.gender' => $gender)
		));
		return ($count >= $this->limit);
	}
	
	function isIncluded($product_id) {
		return $this->hasAny(array('MostSoldProduct.product_id' => $product_id));
	}
	
	/**
	 * Vrati seznam nejprodavanejsich produktu pro zobrazeni na HP
	 */
	function hp_list($customer_type_i, $gender = null) {
		$this->Product->virtualFields['price'] = $this->Product->price;
		$this->Product->virtualFields['discount'] = $this->Product->discount;
		$order = array('MostSoldProduct.gender' => 'ASC');
		if (!$gender) {
			$order = array('MostSoldProduct.gender' => 'DESC');
		}
		$order = array_merge($order, array('MostSoldProduct.order' => 'asc'));
		$most_sold = $this->Product->find('all', array(
			'conditions' => array('Product.active' => true),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.title',
				'Product.short_description',
				'Product.price',
				'Product.discount',
				'Product.url',
				'Product.rate',
				'Product.retail_price_with_dph',
					
				'Image.id',
				'Image.name',
				
				'MostSoldProduct.id'
			),
			'joins' => array(
				array(
					'table' => 'most_sold_products',
					'alias' => 'MostSoldProduct',
					'type' => 'INNER',
					'conditions' => array('Product.id = MostSoldProduct.product_id')	
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
			),
			'order' => $order,
			'limit' => 3
		));
		unset($this->Product->virtualFields['price']);
		unset($this->Product->virtualFields['discount']);
		
		foreach ($most_sold as &$product) {
			$product['MostSoldProduct']['image'] = $this->getImage($product['MostSoldProduct']['id']);
		}

		return $most_sold;
	}
	
	function mostSoldProductIds($customerId, $customerTypeId, $productIds, $limit = 3) {
		$this->virtualFields['ordered_quantity'] = 'SUM(OrderedProduct.product_quantity)';
		
		$conditions = array(
			'Availability.cart_allowed' => true,
			'Product.active' => true,
			$this->Product->price . ' >' => 0
		);
		
		if (!empty($productIds)) {
			$conditions[] = 'MostSoldProduct.product_id NOT IN (' . implode(',', $productIds) . ')';
		}
		
		App::import('Model', 'Customer');
		$this->Customer = &new Customer;
		$gender = $this->Customer->getFieldValue($customerId, 'gender');
		if (isset($gender)) {
			$conditions['MostSoldProduct.gender'] = $gender;
		}

		$productIds = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('MostSoldProduct.product_id = Product.id')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Product.availability_id = Availability.id')
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customerTypeId)
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
				),
			),
			'group' => 'MostSoldProduct.product_id',
			'fields' => array('MostSoldProduct.product_id'),
			'order' => array('MostSoldProduct.order' => 'ASC'),
			'limit' => $limit
		));

		$productIds = Set::extract('/MostSoldProduct/product_id', $productIds);
		return $productIds;
	}
	
	function getImage($id = null) {
		if (!$id) {
			return false;
		}
		$item = $this->find('first', array(
			'conditions' => array('MostSoldProduct.id' => $id),
			'contain' => array(),
			'fields' => array('MostSoldProduct.id', 'MostSoldProduct.product_id', 'MostSoldProduct.image')
		));

		if (empty($item)) {
			debug('nemam item s id ' . $id);
			return false;
		}
		
		$res_image = false;

		if (!$item['MostSoldProduct']['image']) {
			$image = $this->Product->Image->find('first', array(
				'conditions' => array(
					'Image.product_id' => $item['MostSoldProduct']['product_id'],
					'Image.is_main' => true
				),
				'contain' => array()
			));

			if (empty($image)) {
				return false;
			}
			
			$res_image = 'product-images/small/' . $image['Image']['name'];
		} else {
			$res_image = $this->image_path . '/' . $item['MostSoldProduct']['image'];
		}
		return $res_image;
	}
	
	function loadImage($image_data) {
		// pokud neni zadan obrazek, nahraje se bez nej
		if (empty($image_data['name']) && empty($image_data['tmp_name'])) {
			return false;
		}

		$tmp_name = $image_data['tmp_name'];
		
		// vychozi obrazek musi byt 118 x 118
		$default_weight = 118;
		$default_height = 118;
		if (!$imagesize = getimagesize($tmp_name)) {
			debug('"' . $tmp_name . '"');
			return false;
		}
		
		if ($imagesize[0] != $default_weight || $imagesize[1] != $default_height) {
			debug('Rozmery obrazku pro doporuceny produkt musi byt 118 x 118');
			return false;
		}
		
		$file_name = $this->image_path . DS . $image_data['name'];
		$file_name_arr = explode('.', $file_name);
		$file_name_ext = $file_name_arr[count($file_name_arr)-1];
		unset($file_name_arr[count($file_name_arr)-1]);
		$file_name_prefix = implode('.' , $file_name_arr);
		$counter = '';
		$file_name = $file_name_prefix . $counter . '.' . $file_name_ext;
		$i = 1;
		while (file_exists($file_name)) {
			$counter = '_' . $i;
			$file_name = $file_name_prefix . $counter . '.' . $file_name_ext;
			$i++;
		}

		// musim nakopirovat soubor z tmp do slozky
		if (move_uploaded_file($tmp_name, $file_name)) {
			$file_name = str_replace($this->image_path . DS, '', $file_name);
			return $file_name;
		}
		return false;
	}
}
?>
