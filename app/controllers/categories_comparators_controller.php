<?php
class CategoriesComparatorsController extends AppController {
	var $name = 'CategoriesComparators';
	
	function init_heureka() {
		$comparator_id = 1;
		
		$source_file = 'files/TyposURL.csv';
		$content = file_get_contents($source_file);
		$content = explode("\n", $content);
		$save = array();
		foreach ($content as $line) {
			$line = trim($line);
			if ($line != ';') {
				$line = str_getcsv($line, ';');
				$category_id = false;
				if (preg_match('/-c(\d+)"/', $line[0], $matches)) {
					$category_id = $matches[1];
				}
				
				$path = false;
				if (isset($line[1])) {
					$path = trim($line[1]);
				}
				
				if ($category_id && !empty($path) && preg_match('/^Heureka.cz/' , $path)) {
					$save_item = array(
						'comparator_id' => $comparator_id,
						'category_id' => $category_id,
						'path' => $path
					);
					
					$db_categories_comparator = $this->CategoriesComparator->find('first', array(
						'conditions' => array(
							'CategoriesComparator.comparator_id' => $comparator_id,
							'CategoriesComparator.category_id' => $category_id
						),
						'contain' => array(),
						'fields' => array('CategoriesComparator.id')
					));
					if (!empty($db_categories_comparator)) {
						$save_item['id'] = $db_categories_comparator['CategoriesComparator']['id'];
					}
					$save[] = $save_item;
				}
			}
		}
		
		if (!$this->CategoriesComparator->saveAll($save)) {
			debug($save);
		}
		die('OK');
	}
	
	function admin_init_gmc() {
		$comparator_id = 3;
		
		$source_file = 'files/shop_2_taxonomy.csv';
		$content = file_get_contents($source_file);
		$content = explode("\n", $content);
		$save = array();
		foreach ($content as $line) {
			$line = trim($line);
			$line = str_getcsv($line, ';');
			$category_id = false;
			if (preg_match('/-c(\d+)"/', $line[0], $matches)) {
				$category_id = $matches[1];
				
				$path = array($line[1], $line[2], $line[3], $line[4]);
				$path = array_map('trim', $path);
				$path = array_filter($path);
				$path = implode(' > ' , $path);

				if (!empty($path)) {
					$save_item = array(
						'comparator_id' => $comparator_id,
						'category_id' => $category_id,
						'path' => $path
					);
					
					$db_categories_comparator = $this->CategoriesComparator->find('first', array(
						'conditions' => array(
							'CategoriesComparator.comparator_id' => $comparator_id,
							'CategoriesComparator.category_id' => $category_id
						),
						'contain' => array(),
						'fields' => array('CategoriesComparator.id')
					));
					if (!empty($db_categories_comparator)) {
						$save_item['id'] = $db_categories_comparator['CategoriesComparator']['id'];
					}
					$save[] = $save_item;
				}
			}
		}
		
		if (!$this->CategoriesComparator->saveAll($save)) {
			debug($save);
		}
		die('OK');
	}
}