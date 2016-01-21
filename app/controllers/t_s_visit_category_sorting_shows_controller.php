<?php
class TSVisitCategorySortingShowsController extends AppController {
	var $name = 'TSVisitCategorySortingShows';
	
	function my_create($visitCategoryId) {
		$res = array('success' => false);
		$save = array(
			'TSVisitCategorySortingShow' => array(
				't_s_visit_category_id' => $visitCategoryId
			)
		);
		$this->TSVisitCategorySortingShow->create();
		if ($this->TSVisitCategorySortingShow->save($save)) {
			$res = array(
				'success' => true,
				'data' => $this->TSVisitCategorySortingShow->id
			);
		} else {
			die('nepodarilo se ulozit ShortingShow');
		}
		echo json_encode($res);
		die();
	}
	
	function find_last($visitCategoryId) {
		$res = array('success' => false);
		$sortingShow = $this->TSVisitCategorySortingShow->find('first', array(
			'conditions' => array('TSVisitCategorySortingShow.t_s_visit_category_id' => $visitCategoryId),
			'contain' => array(),
			'order' => array('TSVisitCategorySortingShow.id' => 'desc')
		));
		if (!empty($sortingShow)) {
			$res = array(
				'success' => true,
				'data' => $sortingShow['TSVisitCategorySortingShow']['id']
			);
		}
		echo json_encode($res);
		die();
	}
}