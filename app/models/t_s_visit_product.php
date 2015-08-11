<?php
App::import('Model', 'TSVisitSomething');
class TSVisitProduct extends TSVisitSomething {
	var $name = 'TSVisitProduct';
	
	var $belongsTo = array('Product', 'TSVisit');
	
	var $somethingName = 'product';
	
	function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	}
	
	// k zobrazeni produktu behem navstevy pridat, ze byl zobrazen dlouhy popis produktu
	function productDescriptionShow() {
		$last = $this->getLastBySth();
		if (!empty($last)) {
			$last['TSVisitProduct']['description_show'] = true;
			return $this->save($last);
		}
		return false;
	}
	
	// k zobrazeni produktu behem navstevy pridat, ze byly zobrazeny komentare produktu
	function productCommentsShow() {
		$last = $this->getLastBySth();
		if (!empty($last)) {
			$last['TSVisitProduct']['comments_show'] = true;
			return $this->save($last);
		}
		return false;
	}
}
?>