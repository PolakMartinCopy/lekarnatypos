<?php
class TSVisitCategorySortingChange extends AppModel {
	var $name = 'TSVisitCategorySortingChange';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSVisitCategorySortingShow', 'TSVisitCategorySortBy');
}