<?php
App::import('Model', 'TSVisitCategoryElementShow');
class TSVisitCategorySortingShow extends TSVisitCategoryElementShow {
	var $name = 'TSVisitCategorySortingShow';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
}
?>