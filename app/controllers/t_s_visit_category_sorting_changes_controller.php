<?php
class TSVisitCategorySortingChangesController extends AppController {
	var $name = 'TSVisitCategorySortingChanges';
	
	function my_create($sortingShowId, $sortingId) {
		$res = array('success' => false);
		$sortById = $this->TSVisitCategorySortingChange->TSVisitCategorySortBy->map[$sortingId];
		$save = array(
			'TSVisitCategorySortingChange' => array(
				't_s_visit_category_sorting_show_id' => $sortingShowId,
				't_s_visit_category_sort_by_id' => $sortById
			)
		);
		$this->TSVisitCategorySortingChange->create();
		if ($this->TSVisitCategorySortingChange->save($save)) {
			$res = array(
				'success' => true,
				'data' => $this->TSVisitCategorySortingChange->id
			);
		}
		echo json_encode($res);
		die();
	}
}