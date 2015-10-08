<?php
class PDKRelation extends AppModel {
	var $name = 'PDKRelation';
	
	var $actsAs = array('Containable');
	
	var $relations_file_name = 'PDK066.TXT';
	
	function dial_2_array($content, $shortcut_index, $name_index) {
		$items = explode("\n", $content);

		$res = array();
		foreach ($items as $item) {
			$line = str_getcsv($item, '|');
			if (isset($line[$shortcut_index]) && isset($line[$name_index])) {
				$res[$line[$shortcut_index]] = $line[$name_index];
			}
			
		}
		return $res;
	}
	
	function getManufacturer($pdk) {
		$manufacturer = $this->find('first', array(
			'conditions' => array('PDKRelation.pdk' => $pdk),
			'contian' => array(),
			'joins' => array(
				array(
					'table' => 'p_d_k_manufacturers',
					'alias' => 'PDKManufacturer',
					'type' => 'inner',
					'conditions' => array('PDKRelation.manufacturer_name = PDKManufacturer.shortcut')
				)
			)
		));
		
		if (isset($manufacturer['PDKManufacturer']['name'])) {
			return $manufacturer['PDKManufacturer']['name'];
		}
		return '';
	}
	
	function getAtc($pdk) {
		$atc = $this->find('first', array(
			'conditions' => array('PDKRelation.pdk' => $pdk),
			'contian' => array(),
			'joins' => array(
				array(
					'table' => 'p_d_k_atcs',
					'alias' => 'PDKAtc',
					'type' => 'inner',
					'conditions' => array('PDKRelation.atc_name = PDKAtc.shortcut')
				)
			)
		));
		
		if (isset($atc['PDKAtc']['name'])) {
			return $atc['PDKAtc']['name'];
		}
		return '';
	}
}
?>