<?php 
class CustomerTypeProductPrice extends AppModel {
	var $name = 'CustomerTypeProductPrice';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product', 'CustomerType');
	
	var $validates = array(
		'price_vat' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu produktu pro skupinu zákazníků'
			)
		)
	);
	
	function beforeSave() {
		// uprava pole s cenou, aby se mohlo vkladat take s desetinnou carkou
		if (array_key_exists('price', $this->data['CustomerTypeProductPrice'])) {
			$this->data['CustomerTypeProductPrice']['price'] = str_replace(',', '.', $this->data['CustomerTypeProductPrice']['price']);
			if ($this->data['CustomerTypeProductPrice']['price'] == 0 || $this->data['CustomerTypeProductPrice']['price'] == '') {
				$this->data['CustomerTypeProductPrice']['price'] = null;
			}
		}
		
		return true;
	}
	
	/*
	 * Natahne sportnutrition data
	*/
	function import() {
		// vyprazdnim tabulku
		if ($this->truncate()) {
			$snCustomerTypeProductPrices = $this->findAllSn();
			$customerTypes = $this->CustomerType->find('list');
			foreach ($snCustomerTypeProductPrices as $snCustomerTypeProductPrice) {
				$customerTypeProductPrice = $this->transformSn($snCustomerTypeProductPrice, $customerTypes);
				$this->saveAll($customerTypeProductPrice['CustomerTypeProductPrice']);
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('sportnutrition');
		$query = '
			SELECT *
			FROM productpricing AS SnCustomerTypeProductPrice
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snCustomerTypeProductPrices = $this->query($query);
		$this->setDataSource('default');
		return $snCustomerTypeProductPrices;
	}
	
	function transformSn($snCustomerTypeProductPrice, $customerTypes) {
		$customerTypeProductPrice = array();
		$product = $this->Product->find('first', array(
			'conditions' => array('Product.sportnutrition_id' => $snCustomerTypeProductPrice['SnCustomerTypeProductPrice']['product_id']),
			'contain' => array(),
			'fields' => array('Product.id')
		));

		foreach ($customerTypes as $customerTypeId => $customerTypeName) {
			$customerTypeProductPrice['CustomerTypeProductPrice'][] = array(
				'product_id' => (!empty($product) ? $product['Product']['id'] : 0),
				'customer_type_id' => $customerTypeId,
				'price' => $snCustomerTypeProductPrice['SnCustomerTypeProductPrice']['cena' . $customerTypeId]
			);
		}

		return $customerTypeProductPrice;
	}
}
?>
