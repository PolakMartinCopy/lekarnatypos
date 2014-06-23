<?php
class Content extends AppModel {

	var $name = 'Content';
	
	var $validate = array(
		'path' => array(
        	'rule' => 'isUnique',
        	'message' => 'Stránka s takovouto cestou již existuje, změňte cestu a uložte obsahovou stránku znovu.'
		)
	);
}
?>