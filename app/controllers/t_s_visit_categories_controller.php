<?php
class TSVisitCategoriesController extends AppController {
	var $name = 'TSVisitCategories';
	
	function my_create($id) {
		$key = $this->TSVisitCategory->TSVisit->TSCustomerDevice->getKey($this->Cookie, $this->Session);
		$this->TSVisitCategory->myCreate($id);
		die();
	}
}