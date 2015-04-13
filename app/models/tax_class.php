<?php
class TaxClass extends AppModel {
	var $name = 'TaxClass';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => 'active'
		)
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Musíte vyplnit název daňové třídy.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Hodnota již v systému existuje, zvolte jinou'
			)
		),
		'value' => array(
			'rule' => 'numeric',
			'required' => true,
			'message' => 'Musíte vyplnit hodnotu daně.'
		)
	);
	
	var $hasMany = array('Product', 'Shipping');
	
	function delete($id) {
		// pred "smazanim" (deaktivaci) musim dopravu presunout na konec seznamu aktivnich doprav
		while (!$this->islast($id)) {
			$this->moveDown($id);
		}
	
		$tax_class = array(
			'TaxClass' => array(
				'id' => $id,
				'active' => false
			)
		);
	
		return $this->save($tax_class);
	}

	//The Associations below have been created with all possible keys, those that are not needed can be removed
}
?>