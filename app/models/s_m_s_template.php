<?php
class SMSTemplate extends AppModel {
	var $name = 'SMSTemplate';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'content' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Obsah SMS nesmí zůstat prázdný.'
			)
		)
	);
}