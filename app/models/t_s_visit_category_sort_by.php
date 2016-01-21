<?php
class TSVisitCategorySortBy extends AppModel {
	var $name = 'TSVisitCategorySortBy';
	
	var $useTable = 't_s_visit_category_sort_bys';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('TSVisitCategorySortingChange');
	
	// napamovani idcek polozek pro razeni mezi daty z HTML a daty v DB
	var $map = array(
		0 => 1,
		1 => 2,
		2 => 3,
		3 => 4,
		4 => 5,
		5 => 6,
		6 => 7,
		7 => 8
	);
}
?>