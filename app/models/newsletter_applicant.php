<?php
class NewsletterApplicant extends AppModel {
	var $name = 'NewsletterApplicant';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Zadejte Vaši emailovou adresu',
				'last' => true
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Tento email v systému existuje, zadejte jiný email.'
			)
		)
	);
}
?>