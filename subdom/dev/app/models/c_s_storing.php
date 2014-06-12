<?php 
class CSStoring extends AppModel {
	var $name = 'CSStoring';

	var $actsAs = array('Containable');
	
	var $belongsTo = array('User');
	
	var $hasMany = array(
		'CSTransactionItem' => array(
			'dependent' => true
		)
	);
	
	var $validate = array(
		'user_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadán uživatel, který vkládá transakci'
			)
		),
		'date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum'
			)
		)
	);
	
	var $export_file = 'files/c_s_storings.csv';
	
	function beforeSave() {
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data['CSStoring']['date']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data['CSStoring']['date'])) {
			$date = explode('.', $this->data['CSStoring']['date']);
	
			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data['CSStoring']['date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}
		
		return true;
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['CSStoring']['date_from'])) {
			$date_from = explode('.', $data['CSStoring']['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions['CSStoring.date >='] = $date_from;
		}
		if (!empty($data['CSStoring']['date_to'])) {
			$date_to = explode('.', $data['CSStoring']['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions['CSStoring.date <='] = $date_to;
		}
		if (!empty($data['CSStoring']['user_id'])) {
			$conditions['CSStoring.user_id'] = $data['CSStoring']['user_id'];
		}
		if (!empty($data['CSProduct']['group_code'])) {
			$conditions[] = 'CSProduct.group_code LIKE \'%%' . $data['CSProduct']['group_code'] . '%%\'';
		}
		if (!empty($data['CSProduct']['vzp_code'])) {
			$conditions[] = 'CSProduct.vzp_code LIKE \'%%' . $data['CSProduct']['vzp_code'] . '%%\'';
		}

		return $conditions;
	}
	
	function export_fields() {
		$export_fields = array(
			array('field' => 'CSTransactionItem.id', 'position' => '["CSTransactionItem"]["id"]', 'alias' => 'CSTransactionItem.id'),
			array('field' => 'CSStoring.date', 'position' => '["CSStoring"]["date"]', 'alias' => 'CSStoring.date'),
			array('field' => 'CSProduct.id', 'position' => '["CSProduct"]["id"]', 'alias' => 'CSProduct.id'),
			array('field' => 'CSTransactionItem.c_s_product_name', 'position' => '["CSTransactionItem"]["c_s_product_name"]', 'alias' => 'CSTransactionItem.c_s_product_name'),
			array('field' => 'CSProduct.vzp_code', 'position' => '["CSProduct"]["vzp_code"]', 'alias' => 'CSProduct.vzp_code'),
			array('field' => 'CSProduct.group_code', 'position' => '["CSProduct"]["group_code"]', 'alias' => 'CSProduct.group_code'),
			array('field' => 'CSTransactionItem.quantity', 'position' => '["CSTransactionItem"]["quantity"]', 'alias' => 'CSTransactionItem.quantity'),
			array('field' => 'CSTransactionItem.price', 'position' => '["CSTransactionItem"]["price"]', 'alias' => 'CSTransactionItem.price'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'User.id', 'position' => '["User"]["id"]', 'alias' => 'User.id'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
	
		return $export_fields;
	}
}
?>