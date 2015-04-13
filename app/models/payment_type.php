<?php 
class PaymentType extends AppModel {
	var $name = 'PaymentType';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Payment');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název typu platby'
			)
		)
	);
}
?>