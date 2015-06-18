<?php
class CategoriesProduct extends AppModel {

	var $actsAs = array('Containable');
	
	var $name = 'CategoriesProduct';
	
	var $belongsTo = array('Category', 'Product');
	
	/*
	 * Natahne sportnutrition data
	*/
	function import($truncate = true) {
		// vyprazdnim tabulku
		$condition = null;
		if ($truncate) {
			$this->truncate();
		} else {
			$snCategoriesProducts = $this->find('all', array(
				'contain' => array(),
				'fields' => array('CategoriesProduct.id')	
			));
			$snCategoriesProducts = Set::extract('/CategoriesProduct/id', $snCategoriesProducts);
			
			$condition = 'SnCategoriesProduct.id NOT IN (' . implode(',', $snCategoriesProducts) . ')';
		}
		
		$snCategoriesProducts = $this->findAllSn($condition);
		
		$save['CategoriesProduct'] = array();

		// nachystam si prirazeni k puvodnim kategoriim
		foreach ($snCategoriesProducts as $snCategoriesProduct) {
			$categoriesProduct = $this->transformSn($snCategoriesProduct);
//			$save['CategoriesProduct'][] = $categoriesProduct;
			// doplnim si prirazeni k novym kategoriim
			$newCategoriesProduct = $this->generateNewCategoriesProduct($categoriesProduct);
			if ($newCategoriesProduct) {
				// podivam se, jestli uz takovy vztah jednou nemam
				$found = $this->inCategoriesProductArray($newCategoriesProduct, $save['CategoriesProduct']);

				// prohledam podstrom kategorie, do ktere chci produkt vlozit a pokud narazim na to, ze mam produkt vlozeny nekde
				// v podstromu, do teto nadrazene kategorie uz nevkladam
				if ($found === false) {
					$subtreeFound = $this->subtreeSearch($newCategoriesProduct, $save['CategoriesProduct']);
					
					if ($subtreeFound === false) {
						$save['CategoriesProduct'][] = $newCategoriesProduct;
					}
					
					$pathFound = $this->pathSearch($newCategoriesProduct, $save['CategoriesProduct']);
					if ($pathFound !== false) {
						debug($pathFound); die();
						unset($categoriesProduct['CategoriesProduct'][$pathFound]);
					}
				}
			}
		}
		// vlozeni produktu do kategorii
		$this->saveAll($save['CategoriesProduct']);
		
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM categories_products AS SnCategoriesProduct
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snProducts = $this->query($query);
		$this->setDataSource('default');
		return $snProducts;
	}
	
	function transformSn($snCategoriesProduct) {
		$categoriesProduct = array(
			'CategoriesProduct' => array(
				'category_id' => $snCategoriesProduct['SnCategoriesProduct']['category_id'],
				'product_id' => $snCategoriesProduct['SnCategoriesProduct']['product_id'],
			)
		);
		
		return $categoriesProduct;
	}
	
	function generateNewCategoriesProduct($categoriesProduct) {
		$category = $this->Category->find('first', array(
			'conditions' => array('Category.id' => $categoriesProduct['CategoriesProduct']['category_id']),
			'contain' => array(),
			'fields' => array('Category.new_id')
		));
		
		if (!empty($category['Category']['new_id'])) {
			$categoriesProduct['CategoriesProduct']['category_id'] = $category['Category']['new_id'];
			return $categoriesProduct;
		} else {
			// upraveni naparovani problematickych kategorii
			switch ($categoriesProduct['CategoriesProduct']['category_id']) {
				case 226: $categoriesProduct['CategoriesProduct']['category_id'] = 246; return $categoriesProduct; break;
				case 227: $categoriesProduct['CategoriesProduct']['category_id'] = 248; return $categoriesProduct; break;
				case 228: $categoriesProduct['CategoriesProduct']['category_id'] = 249; return $categoriesProduct; break;
				case 236: $categoriesProduct['CategoriesProduct']['category_id'] = 305; return $categoriesProduct; break;
				case 241: $categoriesProduct['CategoriesProduct']['category_id'] = 327; return $categoriesProduct; break;
				case 242: $categoriesProduct['CategoriesProduct']['category_id'] = 352; return $categoriesProduct; break;
				case 343: $categoriesProduct['CategoriesProduct']['category_id'] = 372; return $categoriesProduct; break;
				case 344: $categoriesProduct['CategoriesProduct']['category_id'] = 373; return $categoriesProduct; break;
			}
		}
		return false;
	}
	
	function inCategoriesProductArray($categoriesProduct, $categoriesProductsArray) {
		// chci vypustit duplicity v prirazeni do kategorii
		foreach ($categoriesProductsArray as $index => $cp) {
			if (
				$cp['CategoriesProduct']['category_id'] == $categoriesProduct['CategoriesProduct']['category_id'] &&
				$cp['CategoriesProduct']['product_id'] == $categoriesProduct['CategoriesProduct']['product_id']
			) {
				return $index;
			}
		}
		return false;
	}
	
	function subtreeSearch($categoriesProduct, $categoriesProductsArray) {
		// zjistim deti kategorie, do ktere chci vlozit produkt
		$children = $this->Category->children($categoriesProduct['CategoriesProduct']['category_id']);
		if (!empty($children)) {
			foreach ($children as $child) {
				$tmpCategoriesProduct = array(
					'CategoriesProduct' => array(
						'category_id' => $child['Category']['id'],
						'product_id' => $categoriesProduct['CategoriesProduct']['product_id']
					)
				);
				$index = $this->inCategoriesProductArray($tmpCategoriesProduct, $categoriesProductsArray);
				if ($index !== false) {
					return $index;
				}
			}
		}
		return false;
	}
	
	function pathSearch($categoriesProduct, $categoriesProductsArray) {
		$path = $this->Category->getPath($categoriesProduct['CategoriesProduct']['category_id']);
		unset($path[0]);
		unset($path[count($path)]);
		if (!empty($path)) {
			foreach ($path as $pathItem) {
				$tmpCategoriesProduct = array(
					'CategoriesProduct' => array(
						'category_id' => $pathItem['Category']['id'],
						'product_id' => $categoriesProduct['CategoriesProduct']['product_id']
					)
				);
				$index = $this->inCategoriesProductArray($tmpCategoriesProduct, $categoriesProductsArray);
				if ($index !== false) {
					return $index;
				}
			}
		}
		return false;
	}
}

?>
