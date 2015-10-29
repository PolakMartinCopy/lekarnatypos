<?
class ExportsController extends AppController{
	var $name = 'Exports';
	
	function get_products($comparator_id) {
		// natahnu si model Product
		$this->Export->Product = ClassRegistry::init('Product');
		$this->Export->CustomerType = ClassRegistry::init('CustomerType');

		$customer_type_id = $this->Export->CustomerType->get_id($this->Session->read());
		
		$conditions = array(
			"Product.short_description != ''",
			'Availability.cart_allowed' => true,
			'Product.active' => true,
			'Product.feed' => true
		);
		
		$categories_conditions = 'Category.id = CategoriesProduct.category_id';
		$not_active_categories = $this->Export->Product->CategoriesProduct->Category->find('all', array(
			'conditions' => array('Category.active' => false),
			'contain' => array(),
			'fields' => array('Category.id')	
		));
		
		$not_active_categories_ids = array();
		if (!empty($not_active_categories)) {
			foreach ($not_active_categories as $not_active_category) {
				$not_active_categories_ids = array_merge($not_active_categories_ids, $this->Export->Product->CategoriesProduct->Category->subtree_ids($not_active_category['Category']['id']));
			}
		}
		if (!empty($not_active_categories_ids)) {
			$categories_conditions .= ' AND Category.id NOT IN (' . implode(',', $not_active_categories_ids) . ')';
		}
		$categories_conditions_arr[] = $categories_conditions;
		
		$this->Export->Product->virtualFields['price'] = $this->Export->Product->price;
		$products = $this->Export->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'TaxClass' => array(
					'fields' => array('id', 'value')
				),
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
			),
			'joins' => array(
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id AND Image.is_main = "1"')
				),
				array(
					'table' => 'availabilities',
					'alias' => 'Availability',
					'type' => 'INNER',
					'conditions' => array('Availability.id = Product.availability_id AND Availability.cart_allowed = 1')
				),
				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('CategoriesProduct.product_id = Product.id')
				),
				array(
					'table' => 'categories',
					'alias' => 'Category',
					'type' => 'INNER',
					'conditions' => $categories_conditions_arr
				),
				array(
					'table' => 'comparator_product_click_prices',
					'alias' => 'ComparatorProductClickPrice',
					'type' => 'LEFT',
					'conditions' => array('Product.id = ComparatorProductClickPrice.product_id')
				),
				array(
					'table' => 'comparators',
					'alias' => 'Comparator',
					'type' => 'LEFT',
					'conditions' => array('Comparator.id = ComparatorProductClickPrice.comparator_id AND Comparator.id=' . $comparator_id)
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.short_description',
				'Product.url',
				'Product.zbozi_name',
				'Product.heureka_name',
				'Product.heureka_extended_name',
				'Product.heureka_category',
				'Product.price',
				'Product.ean',
				'Product.supplier_id',
				
				'Image.id',
				'Image.name',
					
				'Availability.id',
				'Availability.name',
					
				'CategoriesProduct.id',
				'CategoriesProduct.product_id',
				'CategoriesProduct.category_id',
					
				'Category.id',
				'Category.name',
					
				'TaxClass.id',
				'TaxClass.value',
					
				'Manufacturer.id',
				'Manufacturer.name',
					
				'ComparatorProductClickPrice.id',
				'ComparatorProductClickPrice.click_price',
			),
//			'limit' => 1000,
		));

		unset($this->Export->Product->virtualFields['price']);
		$res = array();
		foreach ($products as $i => &$product) {
			// kazdy produkt chci ve vystupu pouze jednou
			$to_res = true;
			foreach ($res as $r) {
				if ($r['Product']['id'] == $product['Product']['id']) {
					$to_res = false;
					break;
				}
			}
			if ($to_res) {
				$product['Product']['name'] = str_replace('&times;', 'x', $product['Product']['name']);
				$product['Product']['short_description'] = str_replace('&times;', 'x', $product['Product']['short_description']);
				$res[] = $product;				
			}

		}

		return $res;
	}
	
	function save_zbozi_feed() {
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/exports/seznam_cz';
		$content = download_url($url);
		$file_name = 'files/xml/zbozi.xml';
		file_put_contents($file_name, $content);
		die('here');
	}
	
	function seznam_cz(){
		// nastavim si layout do ktereho budu cpat data v XML
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products(2);
		$this->set('products', $products);
		
		// produkty zobrazovane na detailu na firmy.cz
		$this->set('firmy_cz_products', array(762, 971, 880, 363, 654));
	}
	
	function save_heureka_feed() {
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/exports/heureka_cz';
		$content = download_url($url);
		$file_name = 'files/xml/heureka.xml';
		file_put_contents($file_name, $content);
		die('here');
	}
	
	function heureka_cz() {
		$this->layout = 'xml/heureka';
		// heureka ma v systemu id = 1 (comparators)
		$comparator_id = 1;
		
		$products = $this->get_products($comparator_id);

		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Heureka.cz | Oblečení a móda | Obuv' => array(769),
			'Heureka.cz | Oblečení a móda | Obuv | Dámská obuv' => array(833, 836, 837, 838, 839, 840),
			'Heureka.cz | Oblečení a móda | Obuv | Dětská obuv' => array(834, 841, 842),
			'Heureka.cz | Oblečení a móda | Obuv | Pánská obuv' => array(835),
		);
		
		App::import('Model', 'Category');
		$this->Export->Category = &new Category;

		foreach ($products as $index => $product) {
			// pokud mam kategorii heureky definovanou primo u produktu
			if (isset($product['Product']['heureka_category']) && !empty($product['Product']['heureka_category'])) {
				// pouziju ji do feedu
				$products[$index]['CATEGORYTEXT'] = $product['Product']['heureka_category'];
			// pokud je produkt z Alliance
			} elseif (in_array($product['Product']['supplier_id'], array(4,5))) {
				// mam k dane kategorii produktu prirazenou kategorii na heurece?
				$categories_comparator = $this->Export->Category->CategoriesComparator->find('first', array(
					'conditions' => array(
						'comparator_id' => $comparator_id,
						'category_id' => $product['CategoriesProduct']['category_id']
					),
					'contain' => array(),
					'fields' => array('CategoriesComparator.path')
				));

				if (!empty($categories_comparator)) {
					$products[$index]['CATEGORYTEXT'] = $categories_comparator['CategoriesComparator']['path'];
				}
			}

			if (!isset($products[$index]['CATEGORYTEXT'])) {
				// pokud je kategorie produktu sparovana s heurekou, nastavi se cesta ze shopu
				foreach ($pairs as $name => $array) {
					if (in_array($product['CategoriesProduct']['category_id'], $array)) {
						$products[$index]['CATEGORYTEXT'] = $name;
						break;
					}
				}
	
				// jinak se vytvori retezec ze stromu kategorii v obchode
				if (!isset($products[$index]['CATEGORYTEXT'])) {
					$path = $this->Export->Product->CategoriesProduct->Category->getPath($product['CategoriesProduct']['category_id']);
					$keys = Set::extract('/Category/name', $path);
					unset($keys[0]);
					unset($keys[1]);
					$products[$index]['CATEGORYTEXT'] = implode(' | ', $keys);
				}
			}
		}

		$this->set('products', $products);
		
		// udaje o moznych variantach dopravy
		App::import('Model', 'Shipping');
		$this->Shipping = new Shipping;
		// vytahnu si vsechny zpusoby dopravy mimo osobniho odberu (id = 1)
		$shippings = $this->Shipping->find('all', array(
			'conditions' => array('NOT' => array('Shipping.heureka_id' => null)),
			'contain' => array(),
			'group' => array('Shipping.heureka_id'),
			'fields' => array('*', 'MIN(Shipping.price) AS min_price') 
		));
		$this->set('shippings', $shippings);
	}
	
	function google_merchant() {
		// bez layoutu
		$this->autoLayout = false;
		
		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Zdraví a krása > Zdravotní péče > Fitness a výživa' => array(1, 2, 6, 7, 9, 25, 26, 28, 14),	
			'Zdraví a krása > Zdravotní péče > Fitness a výživa > Doplňky na zvýšení růstu svalové hmoty' => array(15, 57, 58, 59, 60, 87, 88, 89, 61, 62, 16, 67, 68, 69, 70, 17, 71, 72, 73, 18, 63, 64, 19, 77, 78, 79, 80, 20),
			'Zdraví a krása > Zdravotní péče > Fitness a výživa > Vitamíny a výživové doplňky' => array(21, 74, 75, 76, 22, 65, 66, 23, 81, 82, 24, 83, 84, 85, 86),
			'Média > Knihy > Naučná a odborná literatura > Knihy o zdraví a fitness' => array(12),
			'Sportovní potřeby > Cvičení a fitness' => array(10, 40, 41, 42, 43, 44, 13, 33, 38),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení' => array(11, 90, 91, 50, 51),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní kalhoty' => array(45, 49),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní šortky' => array(46, 94),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Sportovní trika' => array(47, 93),
			'Oblečení a doplňky > Oblečení > Sportovní oblečení > Mikiny' => array(48, 92),
			'Sportovní potřeby > Cvičení a fitness > Činky' => array(29),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Spinningová kola' => array(30, 36),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Šlapací trenažéry' => array(31),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Běžecké trenažéry' => array(34),
			'Sportovní potřeby > Cvičení a fitness > Trenažéry > Veslařské trenažéry' => array(35),
			'Sportovní potřeby > Cvičení a fitness > Vzpěračské lavice' => array(37),
		);
		
		$products = $this->get_products('google merchant center');
		
		App::import('Model', 'Category');
		$this->Category = &new Category;
		
		foreach ($products as $index => &$product) {
			// pokud je produkt v kategorii 77 - pripravky s tribulusem - nechci ho do feedu
			$categories = $this->Category->CategoriesProduct->find('all', array(
				'conditions' => array('CategoriesProduct.product_id' => $product['Product']['id']),
				'contain' => array(),
				'fields' => array('CategoriesProduct.category_id')
			));
			$categories = Set::extract('/CategoriesProduct/category_id', $categories);
			if (in_array(77, $categories)) {
				unset($products[$index]);
				continue;
			}
			// chci odchytit produkty, ktere maji v nekde textu "tribulus"
			if (preg_match('/tribulus/i', $product['Product']['name']) || preg_match('/tribulus/i', $product['Product']['short_description'])) {
				unset($products[$index]);
				continue;
			}
			
			$product['Product']['category_text'] = '';
			// pokud je kategorie produktu sparovana , nastavi se rovnou jako 'Sportovni vyziva | *odpovidajici nazev kategorie*
			foreach ($pairs as $name => $array) {
				if (in_array($product['CategoriesProduct']['category_id'], $array)) {
					$product['Product']['category_text'] = $name;
					break;
				}
			}

			$product['Product']['type_text'] = $this->Category->getPath($product['CategoriesProduct']['category_id']);
			$product['Product']['type_text'] = Set::extract('/Category/name', $product['Product']['type_text']);
			$product['Product']['type_text'] = implode(' | ', $product['Product']['type_text']);
		}
		$this->set('products', $products);
	}
}
?>
