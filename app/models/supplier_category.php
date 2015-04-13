<?php
class SupplierCategory extends AppModel {
	var $name = 'SupplierCategory';
	
	var $actsAs = array('Containable');

	var $belongsTo = array(
		'Supplier',
		'Category'
	);
	
	var $hasMany = array('Product');
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Zadejte název kategorie'
		)	
	);
	
	function pair_product($supplier_category_id, $product_id) {
		$supplier_category = $this->find('first', array(
			'conditions' => array('SupplierCategory.id' => $supplier_category_id),
			'contain' => array(),
			'fields' => array('SupplierCategory.category_id')	
		));
		
		if (!empty($supplier_category) && $supplier_category['SupplierCategory']['category_id'] != 0) {
			$categories_product = array(
				'CategoriesProduct' => array(
					'product_id' => $product_id,
					'category_id' => $supplier_category['SupplierCategory']['category_id'],
					'is_paired' => true
				)	
			);
			
			return $this->Category->CategoriesProduct->save($categories_product);
		}
		return true;
	}
	
	function isActive($categoryId) {
		$active = false;
		$category = $this->find('first', array(
			'conditions' => array('SupplierCategory.id' => $categoryId),
			'contain' => array(),
			'fields' => array('SupplierCategory.active')
		));
		if (!empty($category)) {
			$active = $category['SupplierCategory']['active'];
		}
		return $active;
	}
}
