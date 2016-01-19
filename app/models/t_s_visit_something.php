<?php
class TSVisitSomething extends AppModel {
	var $name = 'TSVisitSomething';
	
	var $actsAs = array('Containable');
	
	var $sthId = null;
	
	function myCreate($something_id) {
		// zjistim posledni zobrazeni produktu / kategorie v ramci aktualni navstevy
		$last = $this->getLast();
		// nastavim nazev sloupce podle toho, co chci ukladat (produkt / kategorie)
		$something_column_name = $this->somethingName . '_id';
		// pokud je posledni zobrazeny produkt / kategorie jina, nez aktualni
		if ($last[$this->name][$something_column_name] != $something_id) {
			// zjistim id aktualni navstevy
			$visit = $this->TSVisit->get();
			// ulozim nove navstiveny produkt / kategorii
			$save = array(
				$this->name => array(
					$something_column_name => $something_id,
					't_s_visit_id' => $visit['TSVisit']['id']
				)
			);
			$this->create();
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