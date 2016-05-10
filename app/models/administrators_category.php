<?php
// nekteri admini zodpovidaji jen za nekterou cast eshopu, danou kategoriemi
class AdministratorsCategory extends AppModel {
	var $name = 'AdministratorsCategory';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Administrator', 'Category');
}
?>