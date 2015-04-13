<?php 
class CategoriesMostSoldProduct extends AppModel {
	var $name = 'CategoriesMostSoldProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Category', 'Product');
	
	var $count = 6;
	
	// vyprazdni tabulku
	function truncate() {
		// prepnu na admin db config
		$query = 'TRUNCATE TABLE ' . $this->useTable;
		$result = $this->query($query);
		return $result;
	}
}
?>
