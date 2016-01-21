<?php
class TSVisitCategoryElementShow extends AppModel {
	var $name = 'TSVisitCategoryElementShow';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('TSVisitCategory');
}