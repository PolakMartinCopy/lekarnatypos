<?php 
class DiscountedProduct extends AppModel {
	var $name = 'DiscountedProduct';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => false
		)
	);
	
	var $belongsTo = array('Product');
	
	var $limit = 9;
	
	/**
	 * Test, zda je v systemu vlozen maximalni povoleny pocet zaznamu
	 */
	function isMaxReached() {
		return ($this->find('count') >= $this->limit);
	}
	
	/**
	 * Test, jestli v systemu neexistuje zaznam se zadanymi parametry
	 * @param int $product_id
	 */
	function isIncluded($product_id) {
		return $this->hasAny(array('DiscountedProduct.product_id' => $product_id));
	}
	
	/**
	 * Vrati seznam doporucenych produktu pro zobrazeni na HP
	 */
	function hp_list($customer_type_id = 0) {
		$this->Product->virtualFields['price'] = $this->Product->price;
		$discounted = $this->Product->find('all', array(
			'conditions' => array('Product.active' => true),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.title',
				'Product.short_description',
				'Product.price',
				'Product.retail_price_with_dph',
				'Product.url',
				'Product.rate',
							
				'Image.id',
				'Image.name'
			),
			'joins' => array(
				array(
					'table' => 'discounted_products',
					'alias' => 'DiscountedProduct',
					'type' => 'INNER',
					'conditions' => array('Product.id = DiscountedProduct.product_id')
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
			),
			'order' => array('DiscountedProduct.order' => 'asc')
		));
		unset($this->Product->virtualFields['price']);

		return $discounted;
	}
	
	/*
	 * Natahne sportnutrition data
	 */
	function import() {
		// vyprazdnim tabulku
		$this->truncate();
		$condition = null;
		$snProducts = $this->findAllSn();
		foreach ($snProducts as $snProduct) {
			if ($product = $this->transformSn($snProduct)) {
				$this->create();
				if (!$this->save($product)) {
					debug($product);
					debug($this->validationErrors);
					$this->save($product, false);
				}
			}
		}
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM products AS SnProduct
			WHERE SnProduct.newest = 1
		';
	
		$snProducts = $this->query($query);
		$this->setDataSource('default');
		return $snProducts;
	}
	
	function transformSn($snProduct) {
		$product = array(
			'DiscountedProduct' => array(
				'product_id' => $snProduct['SnProduct']['id'],
			)
		);
	
		return $product;
	}
}
?>
