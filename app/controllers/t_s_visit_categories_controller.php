<?php
class TSVisitCategoriesController extends AppController {
	var $name = 'TSVisitCategories';
	
	function my_create($id) {
		$res = array('success' => false);
		$key = $this->TSVisitCategory->TSVisit->TSCustomerDevice->getKey($this->Cookie, $this->Session);
		$myCreateRes = $this->TSVisitCategory->myCreate($id);
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