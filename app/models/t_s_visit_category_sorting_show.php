<?php
class TSVisitCategorySortingShow extends AppModel {
	var $name = 'TSVisitCategorySortingShow';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSVisitCategory');
}
?>