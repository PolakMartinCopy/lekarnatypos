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
	
	var $virtualFields = array('shortcut' => 'CONCAT(LEFT(content, 80), "...")');
	
	function process($id, $subject_id = null, $options = array()) {
		$template = $this->find('first', array(
			'conditions' => array('SMSTemplate.id' => $id),
			'contain' => array()
		));
	
		if (empty($template)) {
			return false;
		}
	
		// template musim zpracovat, najdu si promenne
		$matches = '';
		preg_match_all("/%([a-zA-Z_]+\.[a-zA-Z_]+)%/U", $template['SMSTemplate']['content'], $matches, PREG_SET_ORDER);
	
		foreach ($matches as $match) {
			$wildcard = $match[1];
			$replace = $this->getWildcardValue($wildcard, $subject_id, $options);
	
			// nahradim to
			$template['SMSTemplate']['content'] = str_replace($match[0], $replace, $template['SMSTemplate']['content']);
		}
		return $template;
	}
	
	function getWildcardValue($wildcard, $subject_id = null, $options = array()) {
		$res = null;
		// natahnu modely
		App::import('Model', 'Order');
		$this->Order = &new Order;
	
		switch ($wildcard) {
			case 'Order.id':
				if ($subject_id) {
					$res = $subject_id;
				}
				break;
			case 'Order.shipping_number':
			case 'Order.variable_symbol':
				if ($subject_id) {
					$objects = explode('.', $wildcard);
					$field = $objects[1];
					$model = $objects[0];
					$value = $this->{$model}->getFieldValue($subject_id, $field);
					$res = $value;
					if ($field == 'created') {
						$res = cz_date_time($value, '.');
					}
				}
				break;
			case 'Order.total_price':
				if ($subject_id) {
					$order = $this->Order->find('first', array(
						'conditions' => array('Order.id' => $subject_id),
						'contain' => array()
					));
					if (!empty($order)) {
						$res = $order['Order']['orderfinaltotal'];
					}
				}
				break;
		}
		return $res;
	}
}