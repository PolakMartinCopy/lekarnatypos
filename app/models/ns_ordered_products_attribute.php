<?php 
class NsOrderedProductsAttribute extends AppModel {
	var $name = 'NsOrderedProductsAttribute';

	var $useDbConfig = 'ns_live';
	
	var $useTable = 'ordered_products_attributes';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('NsOrderedProduct');
}
?>