<?
class RelatedProduct extends AppModel{
	var $name = 'RelatedProduct';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => 'product_id'
		)
	);
	
	var $belongsTo = array('Product');
	
	var $order = array('RelatedProduct.order' => 'asc');
	
	function get_list($id){
		$related_products = $this->find('all', array(
			'conditions' => array(
				'product_id' => $id
			),
			'contain' => array()
		));
		
		$related_ids = array();
		foreach ( $related_products as $related_product ){
			$related_ids[] = $related_product['RelatedProduct']['related_product_id'];
		}
		
		return $related_ids;
	}
}
?>