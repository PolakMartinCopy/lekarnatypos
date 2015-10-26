<?php
class SupplierCategoriesController extends AppController {
	var $name = 'SupplierCategories';
	
	function admin_repair() {
		$categories = $this->SupplierCategory->find('all', array(
			'conditions' => array(
				'SupplierCategory.supplier_id' => 5,
				'SupplierCategory.category_id' => 0,
				'SupplierCategory.active' => true
			),
			'contain' => array(),
			'fields' => array('SupplierCategory.id', 'SupplierCategory.name')
		));
		
		$save = array();
		foreach ($categories as $category) {
			$category1 = $this->SupplierCategory->find('first', array(
				'conditions' => array(
					'SupplierCategory.name' => $category['SupplierCategory']['name'],
					'SupplierCategory.category_id !=' => 0
				),
				'contain' => array(),
				'fields' => array('SupplierCategory.category_id')
			));
			
			if (!empty($category1)) {
				$category['SupplierCategory']['category_id'] = $category1['SupplierCategory']['category_id'];
				$save[] = $category['SupplierCategory'];
			}
		}

		$this->SupplierCategory->saveAll($save);
		die('here');
	}
}