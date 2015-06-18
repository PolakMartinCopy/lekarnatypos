<?
class AttributesSubproduct extends AppModel {
	var $name = 'AttributesSubproduct';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Subproduct', 'Attribute');
	
	/*
	 * Natahne sportnutrition data
	 */
	function import() {
		$this->truncate();
		$snAttributesSubproducts = $this->findAllSn();
		foreach ($snAttributesSubproducts as $snAttributesSubproduct) {
			$attributesSubproduct = $this->transformSn($snAttributesSubproduct);
			$this->create();
			if (!$this->save($attributesSubproduct)) {
				debug($attributesSubproduct);
				debug($this->validationErrors);
			}
		}
	
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM attributes_subproducts AS SnAttributesSubproduct
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snAttributesSubproducts = $this->query($query);
		$this->setDataSource('default');
		return $snAttributesSubproducts;
	}
	
	function transformSn($snAttributesSubproduct) {
		$attributesSubproduct = array(
			'AttributesSubproduct' => array(
				'id' => $snAttributesSubproduct['SnAttributesSubproduct']['id'],
				'attribute_id' => $snAttributesSubproduct['SnAttributesSubproduct']['attribute_id'],
				'subproduct_id' => $snAttributesSubproduct['SnAttributesSubproduct']['subproduct_id']
			)
		);
	
		return $attributesSubproduct;
	}
}
?>
