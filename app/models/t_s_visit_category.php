<?php
App::import('Model', 'TSVisitSomething');
class TSVisitCategory extends TSVisitSomething {
	var $name = 'TSVisitCategory';
	
	var $belongsTo = array('Category', 'TSVisit');
	
	var $hasMany = array('TSVisitCategorySortingShow');
	
	var $somethingName = 'category';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	function actions($id) {
		$sortingsQuery = $this->getSortingsSubquery($id);
		$priceFilterSubquery = $this->getPriceFilterSubquery($id);
		$query = 'SELECT * FROM (' . $sortingsQuery . ' UNION ' . $priceFilterSubquery . ') AS VisitCategoryAction';
		$actions = $this->query($query);
		$actions = $this->actionsToString($actions);
		return $actions;
	}
	
	function getSortingsSubquery($id) {
		$dataSource = $this->getDataSource();
		$sortingSubquery = array(
			'conditions' => array('TSVisitCategory.id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 't_s_visit_category_sorting_shows',
					'alias' => 'TSVisitCategorySortingShow',
					'type' => 'INNER',
					'conditions' => array('TSVisitCategorySortingShow.t_s_visit_category_id = TSVisitCategory.id')
				),
				array(
					'table' => 't_s_visit_category_sorting_changes',
					'alias' => 'TSVisitCategorySortingChange',
					'type' => 'LEFT',
					'conditions' => array('TSVisitCategorySortingChange.t_s_visit_category_sorting_show_id = TSVisitCategorySortingShow.id')
				),
				array(
					'table' => 't_s_visit_category_sort_bys',
					'alias' => 'TSVisitCategorySortBy',
					'type' => 'LEFT',
					'conditions' => array('TSVisitCategorySortingChange.t_s_visit_category_sort_by_id = TSVisitCategorySortBy.id')
				)
			),
			'fields' => array(
				'"sorting" AS type',
				'TSVisitCategorySortBy.name AS value'
			),
			'order' => null,
			'table' => $dataSource->fullTableName($this),
			'alias' => 'TSVisitCategory',
			'limit' => null,
			'offset' => null,
			'group' => null,
		);
		$sortingSubquery = $dataSource->buildStatement($sortingSubquery, $this);
		return $sortingSubquery;
	}
	
	function getPriceFilterSubquery($id) {
		$dataSource = $this->getDataSource();
		$priceFilterSubquery = array(
			'conditions' => array('TSVisitCategory.id' => $id),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 't_s_visit_category_price_filter_shows',
					'alias' => 'TSVisitCategoryPriceFilterShow',
					'type' => 'INNER',
					'conditions' => array('TSVisitCategoryPriceFilterShow.t_s_visit_category_id = TSVisitCategory.id')
				),
				array(
					'table' => 't_s_visit_category_price_filter_changes',
					'alias' => 'TSVisitCategoryPriceFilterChange',
					'type' => 'LEFT',
					'conditions' => array('TSVisitCategoryPriceFilterChange.t_s_visit_category_price_filter_show_id = TSVisitCategoryPriceFilterShow.id')
				),
			),
			'fields' => array(
				'"price_filter" AS type',
				'CONCAT("(", TSVisitCategoryPriceFilterChange.min , ",", TSVisitCategoryPriceFilterChange.max, ")") AS value'
			),
			'order' => null,
			'table' => $dataSource->fullTableName($this),
			'alias' => 'TSVisitCategory',
			'limit' => null,
			'offset' => null,
			'group' => null,
		);
		$priceFilterSubquery = $dataSource->buildStatement($priceFilterSubquery, $this);
		return $priceFilterSubquery;
	}
	
	function actionsToString($actions) {
		$res = array();
		foreach ($actions as $action) {
			$res[] = $action['VisitCategoryAction']['type'] . ':' . $action['VisitCategoryAction']['value'];
		}
		$res = implode('|', $res);
		return $res;
	}
	
	
}
?>