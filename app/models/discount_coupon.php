<?php 
class DiscountCoupon extends AppModel {
	var $name = 'DiscountCoupon';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Customer',
		'Order'
	);
	
	var $hasMany = array(
		'DiscountCouponsProduct' => array(
			'dependent' => true
		),
		'DiscountCouponsCategory' => array(
			'dependent' => true
		)
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název kupónu'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Název kupónu existuje, zadejte jiný'
			)
		),
		'value' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte hodnotu kupónu'
			)
		)
	);
}
?>