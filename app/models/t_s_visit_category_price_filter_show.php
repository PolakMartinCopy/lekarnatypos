<?php
App::import('Model', 'TSVisitCategoryElementShow');
class TSVisitCategoryPriceFilterShow extends TSVisitCategoryElementShow {
	var $name = 'TSVisitCategoryPriceFilterShow';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
}
?>