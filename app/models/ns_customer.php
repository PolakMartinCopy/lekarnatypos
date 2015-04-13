<?php 
class NsCustomer extends AppModel {
	var $name = 'NsCustomer';
	
	var $useDbConfig = 'ns_live';
	
	var $useTable = 'customers';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('NsAddress', 'NsOrder');
}
?>