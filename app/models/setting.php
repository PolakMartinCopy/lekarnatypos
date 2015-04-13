<?php 
class Setting extends AppModel {
	var $name = 'Setting';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název nastavení.'
			)
		),
		'value' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte hodnotu nastavení'
			)
		)
	);
	
	var $shop_keys = array(0 => 'CUST_ROOT', 'CUST_NAME', 'CUST_COMPANY', 'CUST_ICO', 'CUST_DIC', 'CUST_PHONE', 'CUST_MAIL', 'CUST_STREET', 'CUST_CITY', 'CUST_ZIP');
	
	function findId($name) {
		$setting = $this->find('first', array(
			'conditions' => array('Setting.name' => $name),
			'contain' => array(),
			'fields' => array('Setting.id')
		));
		
		if (empty($setting)) {
			return false;
		}
		return $setting['Setting']['id'];
	}
	
	function findValue($name) {
		$setting = $this->find('first', array(
			'conditions' => array('Setting.name' => $name),
			'contain' => array(),
			'fields' => array('Setting.value')
		));
		
		if (empty($setting)) {
			return false;
		}
		return $setting['Setting']['value'];
	}
	
	function updateValue($name, $value) {
		$setting = $this->find('first', array(
			'conditions' => array('Setting.name' => $name),
			'contain' => array(),
			'fields' => array('Setting.id')
		));
		
		if (empty($setting)) {
			return false;
		}
		
		$setting['Setting']['value'] = $value;
		return $this->save($setting);
	}
	
	function init() {
		foreach ($this->shop_keys as $constant) {
			if (!defined($constant)) {
				define($constant, $this->findValue($constant));
			}
		}
	}
}
?>