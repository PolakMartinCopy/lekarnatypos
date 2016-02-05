<?php
class AdMailTemplate extends AppModel {
	var $name = 'AdMailTemplate';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('AbandonedCartAdMail');
	
	function findByType($type) {
		$template = $this->find('first', array(
			'conditions' => array('AdMailTemplate.type' => $type),
			'contain' => array()	
		));
		
		return $template;
	}
}
?>