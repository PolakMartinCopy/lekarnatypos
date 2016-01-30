<?php
class TSVisitsController extends AppController {
	var $name = 'TSVisits';
	
	function recount_duration($id) {
		$this->TSVisit->recountDuration($id);
		die();
	}
	
	function close_expired() {
		if (!$this->TSVisit->closeExpired()) {
			debug('chyba pri uzavirani expirovanych navstev');
		}
		// TODO - spravovat chyby
		die();
	}
	
	function admin_export() {
		if (isset($this->data['TSVisitForm']['TSVisit']['search_form']) && $this->data['TSVisitForm']['TSVisit']['search_form']) {
			$this->Session->write('Search.TSVisitForm', $this->data['TSVisitForm']);
			$from = cz2db_date($this->data['TSVisitForm']['TSVisit']['from']);
			$to = cz2db_date($this->data['TSVisitForm']['TSVisit']['to']);
			
			$url = 'http://' . $_SERVER['HTTP_HOST'] . '/t_s_visits/export/' . $from . '/' . $to;
			$content = download_url($url);
			
			header('Content-Type: text/xml');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="' . basename('visit_export.xml') . '"');
			echo $content;
			
			die();
		} elseif ($this->Session->check('Search.TSVisitForm')) {
			$this->data['TSVisitForm'] = $this->Session->read('Search.TSVisitForm');
		} elseif (!isset($this->data)) {
			$this->data['TSVisitForm']['TSVisit']['from'] = date('d.m.Y', strtotime('-1 week'));
			$this->data['TSVisitForm']['TSVisit']['to'] = date('d.m.Y');
		}

		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function export($from, $to) {
		$visitCategoriesSubquery = $this->TSVisit->getVisitCategoriesSubquery($from, $to);
		$visitProductsSubquery = $this->TSVisit->getVisitProductsSubquery($from, $to);
		
		$query = 'SELECT * FROM (' . $visitCategoriesSubquery . ' UNION ' . $visitProductsSubquery . ') AS Visit ORDER BY visit_created ASC, visit_element_created ASC';
		$visits = $this->TSVisit->query($query);
		foreach ($visits AS &$visit) {
			$visit['Visit']['category_actions'] = $visit['Visit']['product_id'] = $visit['Visit']['product_name'] = $visit['Visit']['product_cart_inserted'] = $visit['Visit']['product_ordered'] = $visit['Visit']['category1_id'] = $visit['Visit']['category1_name'] = $visit['Visit']['category2_id'] = $visit['Visit']['category2_name'] = $visit['Visit']['category3_id'] = $visit['Visit']['category3_name'] = '';
			if ($visit['Visit']['visit_type'] == 'product') {
				$visit['Visit']['product_id'] = $visit['Visit']['element_id'];
				$visit['Visit']['product_name'] = $visit['Visit']['element_name'];
				$category = $this->TSVisit->TSVisitProduct->Product->CategoriesProduct->find('first', array(
					'conditions' => array(
						'CategoriesProduct.product_id' => $visit['Visit']['product_id'],
						'Category.active' => 1
					),
					'contain' => array('Category'),
					'fields' => array('CategoriesProduct.category_id')
				));
				// doplnim implicitni umisteni v kategorii
				if (!empty($category)) {
					$path = $this->TSVisit->TSVisitCategory->Category->getPath($category['CategoriesProduct']['category_id']);
					unset($path[0]);
					unset($path[1]);
					$position = 1;
					foreach ($path as $index => $pathCategory) {
						$visit['Visit']['category' . $position . '_id'] = $pathCategory['Category']['id'];
						$visit['Visit']['category' . $position . '_name'] = $pathCategory['Category']['name'];
						$position++;
					}
				}
				// zjistim, jestli byl produkt v ramci navstevy vlozen do kosiku
				$visit['Visit']['product_cart_inserted'] = $this->TSVisit->productCartInserted($visit['Visit']['visit_id'], $visit['Visit']['product_id']);
				// zjistim, jestli byl produkt v ramci navstevy objednan
				$visit['Visit']['product_ordered'] = $this->TSVisit->productOrdered($visit['Visit']['visit_id'], $visit['Visit']['product_id']);
			} elseif ($visit['Visit']['visit_type'] == 'category') {
				$visit['Visit']['category1_id'] = $visit['Visit']['element_id'];
				$visit['Visit']['category1_name'] = $visit['Visit']['element_name'];
				if ($visit['Visit']['category_parent_id'] != ROOT_CATEGORY_ID) {
					$path = $this->TSVisit->TSVisitCategory->Category->getPath($visit['Visit']['element_id']);
					unset($path[0]);
					unset($path[1]);
					$position = 1;
					foreach ($path as $index => $pathCategory) {
						$visit['Visit']['category' . $position . '_id'] = $pathCategory['Category']['id'];
						$visit['Visit']['category' . $position . '_name'] = $pathCategory['Category']['name'];
						$position++;
					}
				}
				$visit['Visit']['category_actions'] = $this->TSVisit->TSVisitCategory->actions($visit['Visit']['visit_element_id']);
			}
		}
		//debug($visits); die();
		$this->set('visits', $visits);
		$this->layout = 'empty';
	}
}