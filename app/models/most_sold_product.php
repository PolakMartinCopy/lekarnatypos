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
	
	var $limit = 9;
	
	function isMaxReached() {
		return ($this->find('count') >= $this->limit);
	}
	
	function isIncluded($product_id) {
		return $this->hasAny(array('MostSoldProduct.product_id' => $product_id));
	}
	
	/**
	 * Vrati seznam nejprodavanejsich produktu pro zobrazeni na HP
	 */
	function hp_list($customer_type_id) {
		$this->Product->virtualFields['price'] = $this->Product->price;
		$most_sold = $this->Product->find('all', array(
			'conditions' => array('Product.active' => true),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.title',
				'Product.short_description',
				'Product.price',
				'Product.url',
				'Product.rate',
				'Product.retail_price_with_dph',
					
				'Image.id',
				'Image.name'
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
			),
			'order' => array('MostSoldProduct.order' => 'asc')
		));
		unset($this->Product->virtualFields['price']);

		return $most_sold;
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
			WHERE SnProduct.most_sold = 1
		';
	
		$snProducts = $this->query($query);
		$this->setDataSource('default');
		return $snProducts;
	}
	
	function transformSn($snProduct) {
		$product = array(
			'MostSoldProduct' => array(
				'product_id' => $snProduct['SnProduct']['id'],
			)
		);
	
		return $product;
	}
}
?>