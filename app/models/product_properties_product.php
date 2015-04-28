<?php
class ProductPropertiesProduct extends AppModel {
	var $name = 'ProductPropertiesProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Product',
		'ProductProperty'
	);
	
	var $validate = array(
		'update' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Zadejte, jestli chcete vlastnost updatovat daty z feedu nebo ne'
			)
		)
	);
}