<?php
class TSVisitCategoryPriceFilterShowsController extends AppController {
	var $name = 'TSVisitCategoryPriceFilterShows';
	
	function my_create($visitCategoryId) {
		$res = array('success' => false);
		$save = array(
			'TSVisitCategoryPriceFilterShow' => array(
				't_s_visit_category_id' => $visitCategoryId
			)
		);
		$this->TSVisitCategoryPriceFilterShow->create();
		if ($this->TSVisitCategoryPriceFilterShow->save($save)) {
			$res = array(
				'success' => true,
				'data' => $this->TSVisitCategoryPriceFilterShow->id
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