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
			'conditions' => array($this->name . '.name' => $name),
			'contain' => array(),
			'fields' => array($this->name . '.value')
		));

		if (empty($setting)) {
			return false;
		}
		return $setting[$this->name]['value'];
	}
	
	function updateValue($name, $value) {
		$setting = $this->find('first', array(
			'conditions' => array($this->name . '.name' => $name),
			'contain' => array(),
			'fields' => array($this->name . '.id')
		));
		
		if (empty($setting)) {
			return false;
		}
		
		$setting[$this->name]['value'] = $value;
		return $this->save($setting);
	}
	
	function init() {
		$settings = $this->find('all', array(
			'contain' => array()
		));
		foreach ($settings as $setting) {
			$constant = $setting['Setting']['name'];
			if (!defined($constant)) {
				$value = $this->findValue($constant);
				// pokud definuju GEIS_POINT_SHIPPING_IDS, je to pole a musim ho pregenerovat a definovat jako json array
				if ($constant == 'GEIS_POINT_SHIPPING_IDS') {
					$value = explode('|', $value);
					$value = json_encode($value);
				}
				define($constant, $value);
			}
		}
	}
}
?>
