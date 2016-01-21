<?php
class TSVisitCategoryElementShowsController extends AppController {
	var $name = 'TSVisitCategoryElementShowsController';
	
	function my_create($visitCategoryId) {
		$res = array('success' => false);
		$save = array(
			$this->modelClass => array(
				't_s_visit_category_id' => $visitCategoryId
			)
		);
		$this->{$this->modelClass}->create();
		if ($this->{$this->modelClass}->save($save)) {
			$res = array(
				'success' => true,
				'data' => $this->{$this->modelClass}->id
			);
		} else {
			die('nepodarilo se ulozit ' . $this->modelClass);
		}
		echo json_encode($res);
		die();
	}
	
	function find_last($visitCategoryId) {
		$res = array('success' => false);
		$elementShow = $this->{$this->modelClass}->find('first', array(
				'conditions' => array($this->modelClass . '.t_s_visit_category_id' => $visitCategoryId),
				'contain' => array(),
				'order' => array($this->modelClass . '.id' => 'desc')
		));
		if (!empty($elementShow)) {
			$res = array(
				'success' => true,
				'data' => $elementShow[$this->modelClass]['id']
			);
		}
		echo json_encode($res);
		die();
	}
	
	function test() {
		debug($this->modelClass); die();
	}
}