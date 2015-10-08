<?php
class PDKRelationsController extends AppController {
	var $name = 'PDKRelations';
	
	function import() {
		$relations_file_name = 'http://' . $_SERVER['HTTP_HOST'] .  '/' . PDK_DIAL_DIR . '/' . $this->PDKRelation->relations_file_name;
		
		$relations_file = download_url($relations_file_name);
		$relations_file = iconv('cp852', 'UTF-8', $relations_file);
		
		$this->PDKRelation->truncate();

		$relations = explode("\n", $relations_file);
		$save = array();
		foreach ($relations as $relation) {
			$line = str_getcsv($relation, '|');
			if (isset($line[0])) {
				$save[] = array(
					'pdk' => $line[0],
					'manufacturer_name' => $line[10],
					'nomen' => $line[158],
					'ean' => $line[51]
				);
			}
		}
		$this->PDKRelation->saveAll($save);
		
		die();
		
	}
}