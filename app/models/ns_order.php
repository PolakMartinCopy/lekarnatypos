<?php 
class NsOrder extends AppModel {
	var $name = 'NsOrder';
	
	var $useDbConfig = 'ns_live';
	
	var $useTable = 'orders';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('NsCustomer');
	
	var $hasMany = array('NsOrderedProduct');
}
?>