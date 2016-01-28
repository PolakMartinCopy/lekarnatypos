<?php
class TSVisitCategoryPriceFilterChangesController extends AppController {
	var $name = 'TSVisitCategoryPriceFilterChanges';
	
	function my_create($showId, $min, $max) {
		$res = array('success' => false);
		$save = array(
			'TSVisitCategoryPriceFilterChange' => array(
				't_s_visit_category_price_filter_show_id' => $showId,
				'min' => $min,
				'max' => $max
			)
		);
		$this->TSVisitCategoryPriceFilterChange->create();
		if ($this->TSVisitCategoryPriceFilterChange->save($save)) {
			$res = array(
				'success' => true,
				'data' => $this->TSVisitCategoryPriceFilterChange->id
			);
		}
		echo json_encode($res);
		die();
	}
}