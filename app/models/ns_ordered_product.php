<?php 
class NsOrderedProduct extends AppModel {
	var $name = 'NsOrderedProduct';

	var $useDbConfig = 'ns_live';
	
	var $useTable = 'ordered_products';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('NsOrder');
	
	var $hasMany = array('NsOrderedProductsAttribute');
}
?>