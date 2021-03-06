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
		'CategoriesProduct' => array(
			'dependent' => true
		),
		'CategoriesMostSoldProduct' => array(
			'dependent' => true
		),
		'TSVisitCategory',
		'CategoriesComparator' => array(
			'dependent' => true
		),
		'DiscountCouponsCategory',
		'AdministratorsCategory' => array(
			'dependent' => true
		)
	);
	
	// id kategorii, ktere se nebudou brat v potaz pri generovani souvisejicich produktu
	var $unactive_categories_ids = array(397);
	
	// id kategorie s darky, jejiz produkty nechci vypisovat v seznamu produktu podle vyrobce
	var $present_category_id = null;

	var $image_path = 'images/categories';
	
	// IDcko kategorie, ktera se pouzije jako rootova pro podstrom KATEGORIE
	var $category_subtree_root_id = 408;
	// IDcko kategorie, ktera se pouzite jako rootova pro podstrom CO VAS TRAPI
	var $bothers_subtree_root_id = 398;
	// IDcko kategorie, ktera se pouzije jako rootova pro podstrom INZERCE
	var $ad_subtree_root_id = 913;
	
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
	
	/**
	 * Vrati strom kategorii pro vypis
	 * @param unknown $opened_category_id - aktualni otevrena kategorie
	 * @param string $logged - je zakaznik zalogovany?
	 * @param string $order_by_opened - aktualne otevrenou kategorii posunout v seznamu nahoru?
	 * @param string $show_all - zaradit i neaktivni a neverejne kategorie (pro admin nastavit na true)
	 * @param unknown $only_active_subtree - vratit jen rootove kategorie s otevrenou aktualni vetvi nebo kompletni strom
	 * @return multitype:string unknown
	 */
	function getSidebarMenu($opened_category_id, $logged = false, $order_by_opened = true, $show_all = false, $only_active_subtree = false, $root_category_id = ROOT_CATEGORY_ID) {
		// pokud chci vypisovat menu na HP, podivam se nejdriv do kese, jestli nemam data tam a pokud ne, natahnu si je cely znova (a ulozim do kese)
		$file_name = md5(md5($opened_category_id) . md5($logged) . md5($order_by_opened) . md5($show_all) . md5($only_active_subtree) . md5($root_category_id));
		$file_path = 'tmp/cache/' . $file_name;
		// soubory chci kesovat hodinu
		$cacheLength = 60*60;
		if ($res = getCache($file_path, $cacheLength)) {
			return $res;
		}
		
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
		
		// pokud chci aktualne otevrenou kategorii vypsat ve strome na prvnim miste
		if ($order_by_opened) {
			// nastavim razeni
			if ($opened_category_id) {
				if (isset($path_ids[1])) {
					$lead_id = $path_ids[1];
					$order[] = 'FIELD (Category.id, ' . $lead_id . ') DESC';
				}
			}
		}

		$order['Category.lft'] = 'asc';
		// pridam kategorii s KATEGORIEMI
		$path_ids[] = $this->category_subtree_root_id;
		// pridam kategorii PODLE PRIZNAKU
		$path_ids[] = $this->bothers_subtree_root_id;
		
		// mozne duplicity smazu
		$path_ids = array_unique($path_ids);

		$conditions = array();
		
		// idcka kategorii, ktere nechci ve vertikalnim menu zobrazit
		$unwanted_category_ids = array();
		if (!empty($unwanted_category_ids)) {
			$conditions[] = 'Category.id NOT IN (' . implode(',', $unwanted_category_ids) . ')';
		}
		
		if (!$show_all) {
			$conditions['Category.active'] = true;
			$conditions['Category.public'] = true;
		}
		
		// pokud je uzivatel prihlaseny, vypisu i kategorie, ktere jsou urceny pouze prihlasenym
		if ($logged) {
			unset($conditions['public']);
		}

		$path_ids_conditions = array();
		if ($only_active_subtree) {
			$path_ids_conditions = $path_ids;
		}

		$categories = $this->generateTree($root_category_id, $order, $path_ids_conditions);

		// ke kazde kategorii si zjistim kolik ma v sobe produktu
//		$categories = $this->countProducts($categories);
		
		// vyfiltruju kategorie, ktere jsou prazdne (nema produkty, ani podkategorie s produkty)
		$categories = $this->filterEmpty($categories);
		
		$res = array(
			'categories' => $categories, 'path_ids' => $path_ids, 'opened_category_id' => $opened_category_id
		);
		
		// zapisu do kese
		writeCache($file_path, $res);

		return $res;
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

		$conditions[] = $path_condition;
		
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
		$width = 88;
		$height = 88;
		
		// zmenim velikost obrazku
		App::import('Model', 'Image');
		$this->Image = new Image;
		
		
		if (file_exists($tmp_name)) {
			if ($this->Image->resize($tmp_name, $file_name, $width, $height)) {
				$file_name = str_replace($this->image_path . DS, '', $file_name);
				return $file_name;
			}
		}
		return false;
	}
	
	function set_most_sold($id) {
		// zjistim idcka daneho podstromu kategorii
		$subtree_categories = $this->subtree_ids($id);
		//	najdu nejvice prodavane aktivni produkty za posledni mesic
		$from = date('Y-m-d', strtotime('-3 months'));
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
		$this->CategoriesProduct->Product->virtualFields['discount'] = $this->CategoriesProduct->Product->discount;
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
				'Product.discount',
					
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
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
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
				'Product.discount',
					
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
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
				),
			),
				'limit' => $this->CategoriesMostSoldProduct->count - count($products),
				'order' => 'Rand()'
			));
			$products = array_merge($products, $complement_products);
		}
		unset($this->CategoriesProduct->Product->virtualFields['price']);
		unset($this->CategoriesProduct->Product->virtualFields['discount']);

		return $products;
	}
	
	function generateTree($parent_id, $order, $path_ids = array()) {
		if (!empty($path_ids)) {
			if (!in_array($parent_id, $path_ids)) {
				return array();
			}
		}
		
		$conditions = array(
			'active' => true,
			'public' => true,
			'parent_id' => $parent_id
		);

		$categories = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Category.id', 'Category.lft', 'Category.url', 'Category.name', 'Category.parent_id', 'Category.homepage_class'),
			'order' => $order,
		));
		foreach ($categories as &$category) {
			$category['children'] = $this->generateTree($category['Category']['id'], $order, $path_ids);
		}
		
		return $categories;
	}
	
	function generateAllPaths($setFirstEmpty) {
		$unwanted_categories_ids = $this->unwanted_subtree_ids($this->unactive_categories_ids);
		$conditions = array();
		if (!empty($unwanted_categories_ids)) {
			$conditions[] = 'Category.id NOT IN (' . implode(',', $unwanted_categories_ids) . ')';
		}

		$categories = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Category.id'),
			'order' => array('Category.lft' => 'asc')
		));
		
		foreach ($categories as $index => &$category) {
			if ($path = $this->pathToString($category['Category']['id'])) {
				$category['Category']['path'] = $path;
			} else {
				unset($categories[$index]);
			}
		}
		if ($setFirstEmpty) {
			$empty = array(
				0 => array(
					'Category' => array(
						'id' => 0,
						'path' => '-- Všechny --'
					)
				)
			);
			$categories = array_merge($empty, $categories);
		}

		return $categories;
	}
	
	// vrati ve stringu cestu ke kategorii zadane pomoci ID
	function pathToString($id, $separator = ' > ') {
		if ($path = $this->getPath($id)) {
			$path = Set::extract('/Category/name', $path);
			unset($path[0]);
			if (!empty($path)) {
				$path = implode($separator, $path);
				return $path;
			}
		}
		return false;
	}
	
	function unwanted_subtree_ids($unwanted_ids) {
		$unwanted_subtree_ids = array();
		foreach ($unwanted_ids as $unwanted_id) {
			$unwanted_subtree_ids = array_merge($unwanted_subtree_ids, $this->subtree_ids($unwanted_id));
		}
		return $unwanted_subtree_ids;
	}
	
	function pseudo_root_categories_ids() {
		$categories = $this->find('all', array(
			'conditions' => array('parent_id' => ROOT_CATEGORY_ID),
			'contain' => array(),
			'fields' => array('Category.id')
		));
		
		$categories_ids = Set::extract('/Category/id', $categories);
		return $categories_ids;
	}
	
	function pseudo_root_category_id($id) {
		$path = $this->getPath($id);
		if (!empty($path)) {
			if (count($path) > 2) {
				$pseudo_root_category_id = $path[1];
				$pseudo_root_category_id = $pseudo_root_category_id['Category']['id'];
				return $pseudo_root_category_id;
			}
		}
		return false;
	}
	
	function filterEmpty($categories) {
		foreach ($categories as $index => &$category) {
			if (!empty($category['children'])) {
				$category['children'] = $this->filterEmpty($category['children']);
			}
			if ($this->isEmpty($category['Category']['id'])) {
				unset($categories[$index]);
			}
		}
		return $categories;
	}
	
	function isEmpty($id) {
		$subtree_ids = $this->subtree_ids($id);
		$cp = $this->CategoriesProduct->find('first', array(
			'conditions' => array(
				'Product.active' => true,
				'CategoriesProduct.category_id' => $subtree_ids
			),
			'contain' => array('Product'),
			'fields' => array('CategoriesProduct.id')
		));
		$isEmpty = empty($cp);
		return $isEmpty;
	}
	
	static function sortByName($a, $b) {
		if ($a['Category']['name'] == $b['Category']['name']) {
			return 0;
		}
		return ($a['Category']['name'] < $b['Category']['name']) ? -1 : 1;
	}
	
	// vrati seznam zakazniku do autocompletu napr pri zakladani slevovych kuponu
	function autocompleteList($term = null) {
		$autocomplete_list = array();
		if ($term) {
			$conditions = array(
				'Category.active' => true,
				'Category.name LIKE "%' . $term . '%"'
			);
			
			$unwanted_categories_ids = $this->unwanted_subtree_ids($this->unactive_categories_ids);
			if (!empty($unwanted_categories_ids)) {
				$conditions[] = 'Category.id NOT IN (' . implode(',', $unwanted_categories_ids) . ')';
			}
	
			// do autocomplete chci vratit cestu ke kategorii
			$categories = $this->find('all', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('Category.id', 'Category.name'),
				'order' => array('Category.lft' => 'asc')
			));
	
			foreach ($categories as $category) {
				$path = $this->pathToString($category['Category']['id']);
				$autocomplete_list[] = array(
					'label' => $path,
					'value' => $category['Category']['id']
				);
			}
		}
		return $autocomplete_list;
	}
	
	function getSubtreesIds($categoryRootIds) {
		$categoryIds = array();
		foreach ($categoryRootIds as $categoryRootId) {
			$categoryIds = array_merge($categoryIds, $this->subtree_ids($categoryRootId));
		}
		return $categoryIds;
	}
}
?>
