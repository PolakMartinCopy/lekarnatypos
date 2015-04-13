<?php 
class CustomerLogin extends AppModel {
	var $name = 'CustomerLogin';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Customer');
	
	var $validate = array(
		'login' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte login'	
			),
  			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Uživatel s tímto loginem již existuje. Zvolte prosím jiný login'
			)
		)
	);
}
?>