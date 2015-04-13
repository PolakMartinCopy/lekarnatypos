<?
class Availability extends AppModel{
	var $name = 'Availability';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => false
		)
	);

	var $hasMany = array('Product');
	
	var $order = array('Availability.order' => 'asc');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název dostupnosti.'
			)
		)
	);
	
	function delete($id) {
		// pred "smazanim" (deaktivaci) musim dopravu presunout na konec seznamu aktivnich doprav
		while (!$this->islast($id)) {
			$this->moveDown($id);
		}
	
		$availability = array(
			'Availability' => array(
				'id' => $id,
				'active' => false
			)
		);
	
		return $this->save($availability);
	}
	
	function update() {
		$snAvailabilities = $this->findAllSn();
		foreach ($snAvailabilities as $snAvailability) {
			if (!$this->hasAny(array('Availability.sportnutrition_id' => $snAvailability['SnAvailability']['id']))) {
				$availability = $this->transformSn($snAvailability);
				$this->create();
				$this->save($availability);
			}
		}
	}

	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		// vyprazdnim tabulku
		if ($this->truncate()) {
			$snAvailabilities = $this->findAllSn();
			foreach ($snAvailabilities as $snAvailability) {
				$availability = $this->transformSn($snAvailability);
				$this->create();
				$this->save($availability);
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM availabilities AS SnAvailability
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$query .= '
			ORDER BY id ASC
		';
		$snAvailabilities = $this->query($query);
		$this->setDataSource('default');
		return $snAvailabilities;
	}
	
	function findBySnId($snId) {
		$availability = $this->find('first', array(
			'conditions' => array('Availability.sportnutrition_id' => $snId),
			'contain' => array()
		));
	
		return $availability;
	}
	
	function transformSn($snAvailability) {
		// id dostupnosti z puvodniho sportnutritionu, u kterych vim, ze muzu povolit vkladani produktu do kosiku
		$sportnutrition_cart_allowed = array(1, 2, 3, 6, 7, 9);
		$availability = array(
			'Availability' => array(
				'id' => $snAvailability['SnAvailability']['id'],
				'name' => $snAvailability['SnAvailability']['nazev_cz'],
				'cart_allowed' => in_array($snAvailability['SnAvailability']['id'], $sportnutrition_cart_allowed),
				'order' => $snAvailability['SnAvailability']['poradi'],
				'sportnutrition_id' => $snAvailability['SnAvailability']['id']
			)
		);
		return $availability;
	}
}
?>