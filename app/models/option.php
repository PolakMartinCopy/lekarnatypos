<?php
class Option extends AppModel {
	var $name = 'Option';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název třídy atributů'
			)
		)
	);
	
	var $hasMany = array('Attribute');
	
	function delete($id) {
		if ($this->hasAny(array('Option.id' => $id))) {
			$save = array(
				'Option' => array(
					'id' => $id,
					'active' => false
				)
			);
			return $this->save($save);
		}
		return false;
	}
	
	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		$this->truncate();
		$snOptions = $this->findAllSn();
		foreach ($snOptions as $snOption) {
			$option = $this->transformSn($snOption);
			if (!$this->hasAny(array('Option.name' => $option['Option']['name']))) {
				$this->create();
				if (!$this->save($option)) {
					debug($option);
					debug($this->validationErrors);
					$this->save($option, false);
				}
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT DISTINCT nazev_CZ name
			FROM products_povinne_select AS SnOption
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snOptions = $this->query($query);
		$this->setDataSource('default');
		return $snOptions;
	}
	
	function transformSn($snOption) {
		$option = array(
			'Option' => array(
				'name' => $snOption['SnOption']['name']
			)
		);
	
		return $option;
	}
	
	function findByName($name) {
		return $this->find('first', array(
			'conditions' => array('Option.name' => $name),
			'contain' => array(),
		));
	}
}
?>
