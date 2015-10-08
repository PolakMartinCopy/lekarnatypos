<?php
class PDKManufacturer extends AppModel {
	var $name = 'PDKManufacturer';
	
	var $file_name = 'vyrobce.txt';
	
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
}
?>