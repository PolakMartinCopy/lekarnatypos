<?php
App::import('Model', 'TSVisitSomething');
class TSVisitCategory extends TSVisitSomething {
	var $name = 'TSVisitCategory';
	
	var $belongsTo = array('Category', 'TSVisit');
	
	var $somethingName = 'category';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
}
?>