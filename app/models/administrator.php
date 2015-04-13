<?
class Administrator extends AppModel{
	var $name = 'Administrator';

	var $hasMany = array('Ordernote');
	
	/*
	 * Natahne sportnutrition data
	 */
	function import() {
		$this->truncate();
		$snAdministrators = $this->findAllSn();
		foreach ($snAdministrators as $snAdministrator) {
			$administrator = $this->transformSn($snAdministrator);
			$this->create();
			if (!$this->save($administrator)) {
				debug($administrator);
				debug($this->validationErrors);
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM administrators AS SnAdministrator
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snAdministrators = $this->query($query);
		$this->setDataSource('default');
		return $snAdministrators;
	}
	
	function transformSn($snAdministrator) {
		$administrator = array(
			'Administrator' => array(
				'id' => $snAdministrator['SnAdministrator']['id'],
				'first_name' => $snAdministrator['SnAdministrator']['first_name'],
				'last_name' => $snAdministrator['SnAdministrator']['last_name'],
				'login' => $snAdministrator['SnAdministrator']['login'],
				'password' => $snAdministrator['SnAdministrator']['password'],
			)
		);
	
		return $administrator;
	}
}
?>