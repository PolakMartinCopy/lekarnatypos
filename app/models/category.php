<?php
class Category extends AppModel {

	var $name = 'Category';
	
	var $actsAs = array('Tree', 'Containable');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název kategorie'
			)
		),
	);

	var $hasMany = array(
		'CategoriesProduct' => array('dependent' => true),
		'CategoriesMostSoldProduct' => array(
			'dependent' => true
		)
	);
	
	// id kategorii, ktere se nebudou brat v potaz pri generovani souvisejicich produktu
	var $unactive_categories_ids = array();
	
	// id kategorie s darky, jejiz produkty nechci vypisovat v seznamu produktu podle vyrobce
	var $present_category_id = null;

	var $image_path = 'images/categories';
	
	function afterSave($created) {
		if ($created) {
			if ($url = $this->buildUrl($this->data)) {
				$category = array(
					'Category' => array(
						'id' => $this->id,
						'url' => $url
					)
				);
				return $this->save($category);
			} else {
				return false;
			}
		}
		return true;
	}
	
	function buildUrl($category) {
		if (isset($category['Category']['name']) && isset($this->id)) {
			return strip_diacritic($category['Category']['name']) . '-c' . $this->id;
		}
		trigger_error('Nejsou potrebna data k vytvoreni url kategorie', E_USER_ERROR);
		return false;
	}
	
	function countProducts($categories) {
		foreach ($categories as &$category) {
			if (!empty($category['children'])) {
				$category['children'] = $this->countProducts($category['children']);
			}
			$category['Category']['productCount'] = $this->countAllProducts($category['Category']['id']);
			$category['Category']['activeProductCount'] = $this->countActiveProducts($category['Category']['id']);
		}
		
		return $categories;
	}
	
	function countAllProducts($id){
		// spocita mi kolik aktivnich produktu obsahuje dana kategorie
		$result = $this->CategoriesProduct->find('count', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $id
			),
			'contain' => array()
		));

		return $result;
	}

	function countActiveProducts($id){
		// spocita mi kolik aktivnich produktu obsahuje dana kategorie
		$result = $this->CategoriesProduct->find('count', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $id,
				'Product.active' => true
			),
			'contain' => array('Product')
		));

		return $result;
	}
	
	function getSidebarMenu($opened_category_id, $logged = false, $order_by_opened = true, $show_all = false) {
		$horizontal_categories_tree_ids = $this->get_horizontal_categories_tree_ids();
		if (in_array($opened_category_id, $horizontal_categories_tree_ids)) {
			$opened_category_id = ROOT_CATEGORY_ID;
		}
		
		$fields = array('id', 'name'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		// zjistim cestu k otevrene kategorii
		$path = $this->getPath($opened_category_id, $fields, -1);
		// zjistim idcka kategorii v ceste
		$path_ids = Set::extract('/Category/id', $path);
		$order = array();
		
		if ($order_by_opened) {
			// aktualne otevrenou kategorii chci vypsat ve strome na prvnim miste
			if ($opened_category_id) {
				if (isset($path_ids[1])) {
					$lead_id = $path_ids[1];
					$order[] = 'FIELD (Category.id, ' . $lead_id . ') DESC';
				}
				// pokud nemam nastavenou aktualne otevrenou kategorii, chci mit rozbalenou kategorii "sportovni vyziva" s id 9
/*			} else {
				$path_ids[] = 9; */
			}
		}

		$order['Category.lft'] = 'asc';
		$path_ids[] = ROOT_CATEGORY_ID;
		
		// mozne duplicity smazu
		$path_ids = array_unique($path_ids);

		$conditions = array();
		
		// idcka kategorii, ktere nechci ve vertikalnim menu zobrazit
		$unwanted_category_ids = array();
		if (!empty($unwanted_category_ids)) {
			$conditions[] = 'Category.id NOT IN (' . implode(',', $unwanted_category_ids) . ')';
		}
		
		if (!$show_all) {
			$conditions['active'] = true;
			$conditions['public'] = true;
		}
		
		// pokud je uzivatel prihlaseny, vypisu i kategorie, ktere jsou urceny pouze prihlasenym
		if ($logged) {
			unset($conditions['public']);
		}
		
		$full_tree_conditions = $conditions;
		$full_tree_conditions[] = 'parent_id = ' . ROOT_CATEGORY_ID;

		$categories = $this->generateTree(ROOT_CATEGORY_ID, $order);
		
		foreach ($categories as &$category) {
			$subtree_path_ids = $path_ids;
			unset($subtree_path_ids[0]);
			if (in_array($category['Category']['id'], $path_ids)) {
				$conditions[] = "parent_id IN ('" . implode("', '", $subtree_path_ids) . "')";
				$subtree = $this->find('threaded', array(
					'conditions' => $conditions,
					'contain' => array(),
					'fields' => array('Category.id', 'Category.lft', 'Category.url', 'Category.name', 'Category.parent_id'),
					'order' => $order,
				));
				$category['subtree'] = $subtree;
			}
		}

		// ke kazde kategorii si zjistim kolik ma v sobe produktu
		$categories = $this->countProducts($categories);

		return array(
			'categories' => $categories, 'path_ids' => $path_ids, 'opened_category_id' => $opened_category_id
		);
	}
	
	function getSubcategoriesMenuList($opened_category_id = null, $logged = false, $order_by_opened = true, $show_all = false) {
		$horizontal_categories_tree_ids = $this->get_horizontal_categories_tree_ids();
		if (in_array($opened_category_id, $horizontal_categories_tree_ids)) {
			$opened_category_id = 0;
		}
		$fields = array('id', 'name'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		// zjistim cestu k otevrene kategorii
		$path = $this->getPath($opened_category_id, $fields, -1);
		// zjistim idcka kategorii v ceste
		$path_ids = Set::extract('/Category/id', $path);
		$order = array();
		
		if ($order_by_opened) {
			// aktualne otevrenou kategorii chci vypsat ve strome na prvnim miste
			if ($opened_category_id) {
				$lead_id = $path_ids[0];
				$order[] = 'FIELD (Category.id, ' . $lead_id . ') DESC';
			// pokud nemam nastavenou aktualne otevrenou kategorii, chci mit rozbalenou kategorii "sportovni vyziva" s id 9
			} else {
				$path_ids[] = 9;
			}
		}
		$order['Category.lft'] = 'asc';
		$path_ids[] = ROOT_CATEGORY_ID;
		
		// je mozne, ze uz jsem v podstromu sportovni vyzivy, proto mozne duplicity smazu
		$path_ids = array_unique($path_ids);
		
		$conditions = array();
		
		$path_condition = "parent_id IN ('" . implode("', '", $path_ids) . "')";
		
		// pokud jsem v podkategorii "sportovni obleceni", chci vykreslit cely jeji podstrom
		$fitness_clothes_cat_ids = $this->subtree_ids(11);
		if (in_array($opened_category_id, $fitness_clothes_cat_ids)) {
			$conditions[] = array(
				'OR' => array(
					$path_condition,
					'id IN (' . implode(',', $fitness_clothes_cat_ids) . ')'
				)
			);
		} else {
			$conditions[] = $path_condition;
		}
		
		// idcka kategorii, ktere nechci ve vertikalnim menu zobrazit
		$unwanted_category_ids = array();
		if (!empty($unwanted_category_ids)) {
			$conditions[] = 'Category.id NOT IN (' . implode(',', $unwanted_category_ids) . ')';
		}
		
		if (!$show_all) {
			$conditions['active'] = true;
			$conditions['public'] = true;
		}
		
		// pokud je uzivatel prihlaseny, vypisu i kategorie, ktere jsou urceny pouze prihlasenym
		if ($logged) {
			unset($conditions['public']);
		}

		$categories = $this->find('threaded', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Category.id', 'Category.lft', 'Category.url', 'Category.name', 'Category.parent_id'),
			'order' => $order,
		));

		// ke kazde kategorii si zjistim kolik ma v sobe produktu
		$categories = $this->countProducts($categories);

		return array(
			'categories' => $categories, 'path_ids' => $path_ids, 'opened_category_id' => $opened_category_id
		);
	}
	
	function getSubmenuCategories() {
		$submenu_category_ids = array();
		$categories = $this->find('all', array(
			'conditions' => array('Category.id' => $submenu_category_ids),
			'contain' => array(),
			'fields' => array('Category.id', 'Category.name', 'Category.url'),
			'order' => array('Category.id')	
		));

		return ($categories);
	}
	
	/**
	 * Seznam idcek kategorii v podstromu
	 * @param in $category_id
	 */
	function subtree_ids($id) {
		// zjistim idcka kategorii v podstromu
		$category_ids = $this->children($id);
		$category_ids = Set::extract('/Category/id', $category_ids);
		
		$category_ids[] = $id;
		
		return $category_ids;
	}
	
	function get_subcategories_ids($category_id) {
		$result = array();
	
		$result[] = $category_id;
		$subcategories = $this->find('all', array(
				'conditions' => array('parent_id' => $category_id),
				'contain' => array(),
				'fields' => array('id')
		));
	
		if (!empty($subcategories)) {
			foreach ($subcategories as $subcategory) {
				$result = array_merge($result, $this->get_subcategories_ids($subcategory['Category']['id']));
			}
		}
	
		return $result;
	}
	
	function get_horizontal_categories_tree_ids() {
		$horizontal_categories_ids = array();
		$horizontal_categories_tree_ids = array();
		foreach ($horizontal_categories_ids as $hci) {
			$children = $this->children($hci);
			$children = Set::extract('/Category/id', $children);
			$horizontal_categories_tree_ids[] = $hci;
			$horizontal_categories_tree_ids = array_merge($children, $horizontal_categories_tree_ids);
		}
		return $horizontal_categories_tree_ids;
	}
	
	function redirect_url($url) {
		$redirect_url = '/';
		// zjistim na co chci presmerovat
		// odstranim cast adresy, ktera mi urcuje, ze se jedna o produkt
		$pattern = preg_replace('/^\/category\//', '', $url);

		// vytahnu si id produktu na sportnutritionu
		if (preg_match('/^[^:]+:(\d+)/', $pattern, $matches)) {
			$sn_id = $matches[1];
	
			// najdu nas kategorii odpovidajici sn adrese
			$category = $this->find('first', array(
				'conditions' => array('Category.sportnutrition_id' => $sn_id),
				'contain' => array(),
				'fields' => array('Category.id', 'Category.url')
			));
	
			if (!empty($category)) {
				// vratim url pro presmerovani
				$redirect_url = $category['Category']['url'];
			}
		}
	
		return $redirect_url;
	}
	
	/*
	 * Natahne sportnutrition data
	 */
	function import() {
		// vyprazdnim tabulku
		if ($this->truncate()) {
			// nejdriv natahnu deti rootove kategorie
			$condition = 'parent_id = 0';
			$this->Behaviors->detach('Tree');
			// vytahnu si kategorie ze sportnutritionu
			while ($snCategories = $this->findAllSn($condition)) {
				foreach ($snCategories as $snCategory) {
					// transformuju do tvaru pro nas shop
					$category = $this->transformSn($snCategory);
					$this->create();
					if (!$this->save($category)) {
						debug($category);
						trigger_error('Nepodarilo se ulozit kategorii', E_USER_WARNING);
					}
				}
				// posunu se stromem o uroven niz
				$condition = Set::extract('/SnCategory/id', $snCategories);
				$condition = 'parent_id IN (' . implode(',', $condition) . ')';
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM categories AS SnCategory
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$query .= '
			ORDER BY parent_id ASC, lft ASC
		';
		$snCategories = $this->query($query);
		$this->setDataSource('default');
		return $snCategories;
	}
	
	function findBySnId($snId) {
		$category = $this->find('first', array(
			'conditions' => array('Category.id' => $snId),
			'contain' => array()
		));
		
		if (empty($category)) {
			trigger_error('Kategorie se sportnutrition_id ' . $snId . ' neexistuje.', E_USER_ERROR);
		}
		
		return $category;
	}
	
	function transformSn($snCategory) {
		$category = array(
			'Category' => array(
				'id' => $snCategory['SnCategory']['id'],
				'name' => $snCategory['SnCategory']['name'],
				'title' => $snCategory['SnCategory']['title'],
				'description' => $snCategory['SnCategory']['description'],
				'heading' => $snCategory['SnCategory']['heading'],
				'breadcrumb' => $snCategory['SnCategory']['breadcrumb'],
				'content' => $snCategory['SnCategory']['content'],
				'url' => $snCategory['SnCategory']['url'],
				'parent_id' => intval($snCategory['SnCategory']['parent_id']),
				'lft' => $snCategory['SnCategory']['lft'],
				'rght' => $snCategory['SnCategory']['rght'],
				'active' => 1,
				'public' => 1
			)	
		);

		return $category;
	}
	
	function getParentId($snCategory) {
		$parentId = ROOT_CATEGORY_ID;
		if ($snCategory['SnCategory']['parent_id']) {
			$parent = $this->findBySnId($snCategory['SnCategory']['parent_id']);
			if ($parent) {
				$parentId = $parent['Category']['id'];
			}
		}
		return $parentId;
	}
	
	function loadImage($image_data) {
		// pokud neni zadan obrazek, nahraje se bez nej
		if (empty($image_data['name']) && empty($image_data['tmp_name'])) {
			return '';
		}
		
		$file_name = $this->image_path . DS . $image_data['name'];
		$file_name_arr = explode('.', $file_name);
		$file_name_ext = $file_name_arr[count($file_name_arr)-1];
		unset($file_name_arr[count($file_name_arr)-1]);
		$file_name_prefix = implode('.' , $file_name_arr);
		$counter = '';
		$file_name = $file_name_prefix . $counter . '.' . $file_name_ext;
		$i = 1;
		while (file_exists($file_name)) {
			$counter = '_' . $i;
			$file_name = $file_name_prefix . $counter . '.' . $file_name_ext;
			$i++;
		}

		$tmp_name = $image_data['tmp_name'];
		$width = 150;
		$height = 150;
		
		// zmenim velikost obrazku
		App::import('Model', 'Image');
		$this->Image = new Image;
		
		if ($this->Image->resize($tmp_name, $file_name, $width, $height)) {
			$file_name = str_replace($this->image_path . DS, '', $file_name);
			return $file_name;
		}
		return false;
	}
	
	function set_most_sold($id) {
		// zjistim idcka daneho podstromu kategorii
		$subtree_categories = $this->subtree_ids($id);
		//	najdu nejvice prodavane aktivni produkty za posledni mesic
		$from = date('Y-m-d', strtotime('-1 month'));
		$to = date('Y-m-d');
		$products = $this->CategoriesProduct->find('all', array(
			'conditions' => array(
				'CategoriesProduct.category_id' => $subtree_categories,
				'NOT' => array('OrderedProduct.product_quantity' => null),
				'Product.active' => true,
				'DATE(Order.created) >=' => $from,
				'DATE(Order.created) <=' => $to
			),
			'contain' => array('Product'),
			'joins' => array(
				array(
					'table' => 'ordered_products',
					'alias' => 'OrderedProduct',
					'type' => 'LEFT',
					'conditions' => array('OrderedProduct.product_id = CategoriesProduct.product_id')
				),
				array(
					'table' => 'orders',
					'alias' => 'Order',
					'type' => 'LEFT',
					'conditions' => array('Order.id = OrderedProduct.order_id')
				)
			),
			'fields' => array('CategoriesProduct.product_id', 'CategoriesProduct.quantity'),
			'group' => 'CategoriesProduct.id',
			'limit' => $this->CategoriesMostSoldProduct->count,
			'order' => array('CategoriesProduct.quantity' => 'desc')
		));

		// zapamatuju si je v db
		foreach ($products as $product) {
			$save = array(
				'CategoriesMostSoldProduct' => array(
					'category_id' => $id,
					'product_id' => $product['CategoriesProduct']['product_id']
				)
			);
	
			$this->CategoriesMostSoldProduct->create();
			if (!$this->CategoriesMostSoldProduct->save($save)) {
				return false;
			}
		}
		return true;
	}
	
	// vrati nejprodavanejsi produkty dane kategorie
	function get_most_sold($id = null, $customer_type_id = null) {
		if (!$id) {
			return false;
		}
		$limit = $this->CategoriesMostSoldProduct->count;

		// zjistim idcka kategorii v podstromu
		$category_ids = $this->subtree_ids($id);
		$this->CategoriesProduct->Product->virtualFields['price'] = $this->CategoriesProduct->Product->price;

		$products = $this->CategoriesProduct->Product->find('all', array(
			'conditions' => array(
				'Product.active' => true
			),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.price',
					
				'Image.id',
				'Image.name'
			),
			'joins' => array(
				array(
					'table' => 'categories_most_sold_products',
					'alias' => 'CategoriesMostSoldProduct',	
					'type' => 'INNER',
					'conditions' => array('Product.id = CategoriesMostSoldProduct.product_id AND CategoriesMostSoldProduct.category_id = ' . $id)
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id AND Image.is_main = 1')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
			),
			'limit' => $limit,
			'group' => 'Product.id',
		));

		// produkty maji byt 3 (vic se jich tam pri generovani neulozi
		// ale muze jich byt min, proto nahodne vyberu zbytek z dane kategorie
		if (count($products) < $limit) {
			$product_ids = Set::extract('/Product/id', $products);
			$subtree_ids = $this->get_subcategories_ids($id);
			$complement_categories_products_conditions = 'CategoriesProduct.product_id = Product.id AND CategoriesProduct.category_id IN (' . implode(',', $subtree_ids) . ')';
			if (!empty($product_ids)) {
				$complement_categories_products_conditions .= ' AND CategoriesProduct.product_id NOT IN (' . implode(',', $product_ids) . ')';
			}
			// vyberu nahodne produkt z kategorie a vlozim ho do pole nejprodavanejsich
			$complement_products = $this->CategoriesProduct->Product->find('all', array(
				'conditions' => array(
					'Product.active' => true
				),
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.short_description',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.price',
				'Product.rate',
					
				'Image.id',
				'Image.name'
			),
			'joins' => array(
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array($complement_categories_products_conditions)
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id AND Image.is_main = 1')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
			),
				'limit' => $this->CategoriesMostSoldProduct->count - count($products),
				'order' => 'Rand()'
			));
			$products = array_merge($products, $complement_products);
		}

		return $products;
	}
	
	function generateTree($parent_id, $order) {
		$conditions = array(
			'active' => true,
			'public' => true,
			'parent_id' => $parent_id
		);

		$categories = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Category.id', 'Category.lft', 'Category.url', 'Category.name', 'Category.parent_id'),
			'order' => $order,
		));
		foreach ($categories as &$category) {
			$category['children'] = $this->generateTree($category['Category']['id'], $order);
		}
		
		return $categories;
	}
}
?>