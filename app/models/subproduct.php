<?php
class Subproduct extends AppModel {
	var $name = 'Subproduct';

	var $actsAs = array('Containable');

	
	var $belongsTo = array(
		'Product',
		'Availability'
	);

	var $hasMany = array(
		'AttributesSubproduct' => array(
			'dependent' => true
		),
		'CartsProduct'
	);
	
	function countSortOrder($attribute_id, $product_id){
		$attribute = $this->Attribute->read(null, $attribute_id);
		$bottom_level_left = $attribute['Attribute']['option_id'] * 10;
		$bottom_level_right = ($attribute['Attribute']['option_id'] + 1) * 10;
		$conditions = array(
			'product_id' => $product_id,
			'sort_order' => 'BETWEEN ' . $bottom_level_left . ' AND ' . $bottom_level_right
		);

		$bottom_level = $this->find($conditions, 'MAX(sort_order) as bottom_level');

		// jestlize atribut neni jeste k produktu prirazeny,
		// vraci mi to prazdny zaznam
		if ( empty($bottom_level[0]['bottom_level']) ){
			return $bottom_level_left;
		}
		return $bottom_level[0]['bottom_level'];
	}
	
	function optionFilter($params){
		$filter = '';
		if ( isset($params['option_id']) ){
			$filter = 'option_id:' . $params['option_id'];
		}
		return $filter;
	}
	
	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		$this->truncate();
		$snSubproducts = $this->findAllSn();
		foreach ($snSubproducts as $snSubproduct) {
			$subproduct = $this->transformSn($snSubproduct);
			$this->create();
			if (!$this->save($subproduct)) {
				debug($subproduct);
				debug($this->validationErrors);
				$this->save($subproduct, false);
			}
		}
		
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM subproducts AS SnSubproduct
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snSubproducts = $this->query($query);
		$this->setDataSource('default');
		return $snSubproducts;
	}
	
	function transformSn($snSubproduct) {
		$subproduct = array(
			'Subproduct' => array(
				'id' => $snSubproduct['SnSubproduct']['id'],
				'price_with_dph' => $snSubproduct['SnSubproduct']['price_with_dph'],
				'price_wout_dph' => $snSubproduct['SnSubproduct']['price_wout_dph'],
				'product_id' => $snSubproduct['SnSubproduct']['product_id'],
				'pieces' => $snSubproduct['SnSubproduct']['pieces'],
				'active' => $snSubproduct['SnSubproduct']['active'],
				'availability_id' => $snSubproduct['SnSubproduct']['availability_id'],
			)
		);

		return $subproduct;
	}
}
?>
