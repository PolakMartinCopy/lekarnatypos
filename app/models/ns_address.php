<?php 
class NsAddress extends AppModel {
	var $name = 'NsAddress';

	var $useDbConfig = 'ns_live';
	
	var $useTable = 'addresses';
	
	var $actsAs = array('Containable');	

	var $belongsTo = array('NsCustomer');
}
?>