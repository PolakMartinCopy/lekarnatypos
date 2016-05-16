<?php mb_internal_encoding('UTF-8')?>
<?php
class AppModel extends Model {
	var $curl = null;
	
	function update_setting($name, $value) {
		return $this->query('
			UPDATE parser_settings
			SET value="' . $value . '"
			WHERE name="' . $name . '"
		');
	}
	
	function truncate() {
		if ($this->useTable) {
			return $this->query('TRUNCATE TABLE ' . $this->useTable);
		}
		return false;
	}
	
	function getFieldValue($id, $field) {
		$item = $this->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array(),
			'fields' => array($field)
		));
	
		if (empty($item)) {
			return false;
		}
		return $item[$this->name][$field];
	}
	
	function getIdByField($value, $field) {
		$item = $this->find('first', array(
			'conditions' => array($field => $value),
			'contain' => array(),
			'fields' => array('id')
		));
		if (empty($item)) {
			return false;
		}
		return $item[$this->name]['id'];
	}
	
	function getItemById($id) {
		$item = $this->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array()
		));
		return $item;
	}
	
	function setAttribute($id, $attName, $attValue) {
		$save = array(
			$this->name => array(
				'id' => $id,
				$attName => $attValue
			)
		);
		return $this->save($save);
	}
	
	function getAllByField($value, $field) {
		$item = $this->find('all', array(
			'conditions' => array($field => $value),
			'contain' => array(),
		));
		return $item;
	}

}
?>
