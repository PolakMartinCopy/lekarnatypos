<?php 
class ProductType extends AppModel {
	var $name = 'ProductType';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => 'active'
		)
	);
	
	var $hasMany = array('Product');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název typu produktu'
			)
		)
	);
	
	function delete($id) {
		// pred "smazanim" (deaktivaci) musim dopravu presunout na konec seznamu aktivnich doprav
		while (!$this->islast($id)) {
			$this->moveDown($id);
		}
	
		$product_type = array(
			'ProductType' => array(
				'id' => $id,
				'active' => false
			)
		);
	
		return $this->save($product_type);
	}
}
?>