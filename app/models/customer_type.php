<?php 
class CustomerType extends AppModel {
	var $name = 'CustomerType';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Customer', 'CustomerTypeProductPrice');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název typu zákazníka'
			)
		)
	);
	
	var $order = array('CustomerType.order' => 'asc');
	
	function update() {
		$snCustomerTypes = $this->findAllSn();
		foreach ($snCustomerTypes as $snCustomerType) {
			if (!$this->hasAny(array('CustomerType.sportnutrition_id' => $snCustomerType['SnCustomerType']['id']))) {
				$customerType = $this->transformSn($snCustomerType);
				$this->create();
				$this->save($customerType);
			}
		}
	}
	
	// zjisti id typu uzivatele
	function get_id($session) {
		// defaultne neni uzivatel zadneho typu
		$customer_type_id = 2;
		// pokud je uzivatel prihlaseny
		if (isset($session['Customer']['customer_type_id'])) {
			// nastavim si id typu ze sesny
			$customer_type_id = $session['Customer']['customer_type_id'];
			// nactu si informace o typu uzivatele
			$customer_type = $this->find('first', array(
				'conditions' => array('CustomerType.id' => $customer_type_id),
				'contain' => array(),
				'fields' => array('CustomerType.id', 'CustomerType.substitute_id')
			));
			// a podivam se, jestli nema dany typ docasne nahrazeni jinym typem a pokud ano
			if (isset($customer_type['CustomerType']['substitute_id'])) {
				// nastavim jako id typu uzivatele to, ktere nahrazuje 
				$customer_type_id = $customer_type['CustomerType']['substitute_id'];
			}
		}
		return $customer_type_id;
	}
	
	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		// vyprazdnim tabulku
		if ($this->truncate()) {
			$snCustomerTypes = $this->findAllSn();
			foreach ($snCustomerTypes as $snCustomerType) {
				$customerType = $this->transformSn($snCustomerType);
				$this->create();
				$this->save($customerType);
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM ciselniky_cenove_kategorie AS SnCustomerType
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$query .= '
			ORDER BY poradi ASC
		';
		$snCustomerTypes = $this->query($query);
		$this->setDataSource('default');
		return $snCustomerTypes;
	}
	
	function findBySnId($snId) {
		$customerType = $this->find('first', array(
			'conditions' => array('CustomerType.sportnutrition_id' => $snId),
			'contain' => array()
		));
		
		return $customerType;
	}
	
	function transformSn($snCustomerType) {
		$customerType = array(
			'CustomerType' => array(
				'id' => $snCustomerType['SnCustomerType']['id'],
				'name' => $snCustomerType['SnCustomerType']['nazev'],
				'order' => $snCustomerType['SnCustomerType']['poradi'],
				'sportnutrition_id' => $snCustomerType['SnCustomerType']['id']
			)
		);
	
		return $customerType;
	}
}
?>