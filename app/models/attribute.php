<?php
class Attribute extends AppModel {
	var $name = 'Attribute';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'sort_order',
			'foreign_key' => 'option_id'
		)
	);

	var $validate = array(
		'option_id' => array(
			'rule' => array('numeric'),
			'allowEmpty' => false,
			'message' => 'Pole nesmí zůstat prázdné!'
		),
		'value' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole hodnota atributu musí být vyplněno'
			)
		)
	); // definuje pole, ktera musi byt validni

	var $belongsTo = array('Option');
	
	var $hasMany = array('AttributesSubproduct', 'OrderedProductsAttribute'); 
	
	var $filter_limit = 7;
	
	function filter_attributes($opened_category_id, $option_ids = null) {
		// defaultne chci vypisovat prichute, ale muze se v budoucnu pouzit i pro vypis ostatnich trid atributu
		if (!$option_ids) {
			$option_ids = array(1, 7, 9, 11, 13, 15, 16);
		}
		
		$conditions = array('Product.active' => true, 'Attribute.option_id' => $option_ids);
		if (isset($opened_category_id)) {
			// zjistim idcka kategorii v podstromu
			$category_ids = $this->AttributesSubproduct->Subproduct->Product->CategoriesProduct->Category->subtree_ids($opened_category_id);
			$conditions = array_merge($conditions, array('CategoriesProduct.category_id' => $category_ids));
		}
		$limit = $this->filter_limit;
		
		$this->virtualFields['products_count'] = 'COUNT(DISTINCT(Product.id))';
		
		// chci $limit atributu, kteri maji (pro danou kategorii a danou tridu atributu) nejvice produktu
		// (do filtru ve vypisu produktu)
		$filter_attributes = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'attributes_subproducts',
					'alias' => 'AttributesSubproduct',
					'type' => 'INNER',
					'conditions' => array('Attribute.id = AttributesSubproduct.attribute_id')
				),
				array(
					'table' => 'subproducts',
					'alias' => 'Subproduct',
					'type' => 'INNER',
					'conditions' => array('Subproduct.id = AttributesSubproduct.subproduct_id')						
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = Subproduct.product_id')
				),
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('Product.id = CategoriesProduct.product_id')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
				)
			),
			'fields' => array('Attribute.id', 'Attribute.value', 'Attribute.products_count'),
			'limit' => $limit,
			'group' => array('Attribute.id'),
			'order' => array('Attribute.products_count' => 'desc', 'Attribute.value' => 'asc')
		));
		// odnastavim virtualni pole zalozena za behu
		unset($this->virtualFields['products_count']);
		return $filter_attributes;
	}
	
	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		$this->truncate();
		$snAttributes = $this->findAllSn();
		foreach ($snAttributes as $snAttribute) {
			$attribute = $this->transformSn($snAttribute);
			$this->create();
			if (!$this->save($attribute)) {
				debug($attribute);
				debug($this->validationErrors);
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM attributes AS SnAttribute
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snAttributes = $this->query($query);
		$this->setDataSource('default');
		return $snAttributes;
	}
	
	function transformSn($snAttribute) {
		$attribute = array(
			'Attribute' => array(
				'id' => $snAttribute['SnAttribute']['id'],
				'option_id' => $snAttribute['SnAttribute']['option_id'],
				'value' => $snAttribute['SnAttribute']['value'],
				'sort_order' => $snAttribute['SnAttribute']['sort_order']
			)
		);
		
		return $attribute;
	}
	
	function findByValue($optionId, $value) {
		return $this->find('first', array(
			'conditions' => array(
				'Attribute.option_id' => $optionId,
				'Attribute.value' => $value
			),
			'contain' => array(),
		));
	}
}
?>