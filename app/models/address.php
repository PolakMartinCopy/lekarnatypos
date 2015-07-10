<?php
class Address extends AppModel {
	var $name = 'Address';
	
	var $actsAs = array('Containable');

	var $belongsTo = array('Customer');

	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím jméno a příjmení, nebo název společnosti.'
			)
		),
		'street' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím název ulice.'
			)
		),
		'zip' => array(
			'rule' => array('between', 5, 6),
			'message' => 'Vyplňte prosím správné PSČ.'
		),
		'city' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím název města.'
			)
		)
	);
	
	function import() {
		$this->truncate();
	
		$snAddresses = $this->findAllSn();
		// pro kazdou tridu adres
		foreach ($snAddresses as $snAddress) {
			$address = $this->transformSn($snAddress);
			$this->create();
			if (!$this->saveAll($address)) {
				debug($address);
				debug($this->validationErrors);
			}
		}
	
	
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM addresses AS SnAddress
		';
	
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
	
		$snAddresses = $this->query($query);
		$this->setDataSource('default');
		return $snAddresses;
	}
	
	function transformSn($snAddress) {
		$address = array(
			'Address' => array(
				'id' => $snAddress['SnAddress']['id'],
				'customer_id' => $snAddress['SnAddress']['customer_id'],
				'name' => $snAddress['SnAddress']['name'],
				'street' => $snAddress['SnAddress']['street'],
				'street_no' => $snAddress['SnAddress']['street_no'],
				'zip' => $snAddress['SnAddress']['zip'],
				'city' => $snAddress['SnAddress']['city'],
				'state' => $snAddress['SnAddress']['state'],
				'is_main' => $snAddress['SnAddress']['is_main'],
				'type' => $snAddress['SnAddress']['type'],
			),
		);
	
		return $address;
	}
}
?>
