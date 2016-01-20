<?php
class TSVisitCategoriesController extends AppController {
	var $name = 'TSVisitCategories';
	
	function my_create($category_id) {
		$res = array('success' => false);
		$key = $this->TSVisitCategory->TSVisit->TSCustomerDevice->getKey($this->Cookie, $this->Session);
		$myCreateRes = $this->TSVisitCategory->myCreate($category_id);
		if ($myCreateRes) {
			$res = array(
				'success' => true,
				'data' => $myCreateRes
			);
		}
		echo json_encode($res);
		die();
	}
}