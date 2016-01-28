<?php
class TSVisitCategoryPriceFilterChange extends AppModel {
	var $name = 'TSVisitCategoryPriceFilterChange';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSVisitCategoryPriceFilterShow');
}