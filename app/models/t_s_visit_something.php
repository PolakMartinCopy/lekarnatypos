<?php
class TSVisitSomething extends AppModel {
	var $name = 'TSVisitSomething';
	
	var $actsAs = array('Containable');
	
	var $sthId = null;
	
	function myCreate($something_id) {
		$last = $this->getLast();
		$something_column_name = $this->somethingName . '_id';
		if ($last[$this->name][$something_column_name] != $something_id) {
			$visit = $this->TSVisit->get();
			$save = array(
				$this->name => array(
					$something_column_name => $something_id,
					't_s_visit_id' => $visit['TSVisit']['id']
				)
			);
			return $this->save($save);
		}
		return true;
	}
	
	function getLast() {
		$visit = $this->TSVisit->get();
		// posledni zobrazeni nejake kategorie / produktu v ramci aktualni navstevy
		$last = $this->find('first', array(
			'conditions' => array($this->name . '.t_s_visit_id' => $visit['TSVisit']['id']),
			'contain' => array(),
			'order' => array($this->name . '.created' => 'DESC')
		));
	
		return $last;
	}
	
	function getLastBySth() {
		if (isset($this->sthId) && $this->sthId) {
			$visit = $this->TSVisit->get();
			$something_column_name = $this->somethingName . '_id';
			// posledni zobrazeni DANE kategorie / produktu v ramci aktualni navstevy
			$last = $this->find('first', array(
				'conditions' => array(
					$this->name . '.t_s_visit_id' => $visit['TSVisit']['id'],
					$this->name . '.' . $something_column_name => $this->sthId
				),
				'contain' => array(),
				'order' => array($this->name . '.created' => 'DESC')
			));
			
			return $last;
		}
		return false;
	}
}