<?php
class PDKNomen extends AppModel {
	var $name = 'PDKNomen';
	
	var $file_name = 'pdknomen.txt';
	
	function dial_2_array($content) {
		$items = explode("\n", $content);
	
		$res = array();
	
		foreach ($items as $item) {
			$line = str_getcsv($item, '|');
			$line = array_filter($line);
			if (count($line) > 1) {
				$shortcut = $line[0];
				array_shift($line);
				$name = implode(' > ', $line);
				$res[$shortcut] = $name;
			}
	
		}
		return $res;
	}
}
?>