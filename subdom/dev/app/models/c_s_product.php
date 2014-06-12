<?php 
class CSProduct extends AppModel {
	var $name = 'CSProduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Unit', 'TaxClass');
	
	var $hasMany = array('CSTransactionItem');
	
	var $virtualFields = array(
		'info' => 'CONCAT(CSProduct.vzp_code, " ", CSProduct.name, " - ", ROUND(CSProduct.store_price, 2), " Kč")'
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název zboží'
			)
		),
		'store_price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu zboží'
			)
		)
	);
	
	var $export_file = 'files/c_s_products.csv';
	
	function beforeSave() {
		if (isset($this->data['CSProduct']['store_price'])) {
			$this->data['CSProduct']['store_price'] = str_replace(',', '.', $this->data['CSProduct']['store_price']);
		}

		return true;
	}
	
	// metoda pro smazani produktu - NEMAZE ale DEAKTIVUJE
	function delete($id = null) {
		if (!$id) {
			return false;
		}
	
		if ($this->hasAny(array('CSProduct.id' => $id))) {
			$product = array(
				'CSProduct' => array(
					'id' => $id,
					'active' => false
				)
			);
			return $this->save($product);
		} else {
			return false;
		}
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['CSProduct']['vzp_code'])) {
			$conditions[] = 'CSProduct.vzp_code LIKE \'%%' . $data['CSProduct']['vzp_code'] . '%%\'';
		}
		if (!empty($data['CSProduct']['group_code'])) {
			$conditions[] = 'CSProduct.group_code LIKE \'%%' . $data['CSProduct']['group_code'] . '%%\'';
		}
		if (!empty($data['CSProduct']['name'])) {
			$conditions[] = 'CSProduct.name LIKE \'%%' . $data['CSProduct']['name'] . '%%\'';
		}
	
		return $conditions;
	}
	
	function autocomplete_list($term = null) {
		$conditions = array('CSProduct.active' => true);
		if ($term) {
			$conditions['CONCAT(CSProduct.vzp_code, " ", CSProduct.name) LIKE'] = '%' . $term . '%';
		}
	
		$products = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('CSProduct.id', 'CSProduct.info', 'CSProduct.name')
		));
	
		$autocomplete_list = array();
		foreach ($products as $product) {
			$autocomplete_list[] = array(
				'label' => $product['CSProduct']['info'],
				'name' => $product['CSProduct']['name'],
				'value' => $product['CSProduct']['id']
			);
		}
		return json_encode($autocomplete_list);
	}
}
?>