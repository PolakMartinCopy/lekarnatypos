<?php
App::import('Model', 'TSVisitSomething');
class TSVisitProduct extends TSVisitSomething {
	var $name = 'TSVisitProduct';
	
	var $belongsTo = array('Product', 'TSVisit');
	
	var $somethingName = 'product';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
}
?>