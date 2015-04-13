<?php 
class CustomerTypeProductPricesController extends AppController {
	var $name = 'CustomerTypeProductPrices';
	
	function import() {
		$this->CustomerTypeProductPrice->import();
		die('here');
	}

}
?>