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

}
?>
