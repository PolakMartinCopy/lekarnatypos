<?
class ExportsController extends AppController{
	var $name = 'Exports';
	
	function get_products($comparator_name) {
		// natahnu si model Product
		App::Import('model', 'Product');
		$this->Product = &new Product;
		
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		$conditions = array(
			"Product.short_description != ''",
			'Availability.cart_allowed' => true,
			'Product.active' => true,
			'Product.feed' => true
		);
		
		$categories_conditions = 'Category.id = CategoriesProduct.category_id';
		$not_active_categories = $this->Product->CategoriesProduct->Category->find('all', array(
			'conditions' => array('Category.active' => false),
			'contain' => array(),
			'fields' => array('Category.id')	
		));
		
		$not_active_categories_ids = array();
		if (!empty($not_active_categories)) {
			foreach ($not_active_categories as $not_active_category) {
				$not_active_categories_ids = array_merge($not_active_categories_ids, $this->Product->CategoriesProduct->Category->subtree_ids($not_active_category['Category']['id']));
			}
		}
		if (!empty($not_active_categories_ids)) {
			$categories_conditions .= ' AND Category.id NOT IN (' . implode(',', $not_active_categories_ids) . ')';
		}
		$categories_conditions_arr[] = $categories_conditions;
		
		$this->Product->virtualFields['price'] = $this->Product->price;
		$products = $this->Product->find('all', array(
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
					'conditions' => array('Comparator.id = ComparatorProductClickPrice.comparator_id AND Comparator.name="' . $comparator_name . '"')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.short_description',
				'Product.url',
				'Product.zbozi_name',
				'Product.heureka_name',
				'Product.price',
				'Product.ean',
				
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
				'ComparatorProductClickPrice.click_price'
			),
//			'limit' => 10
		));
		unset($this->Product->virtualFields['price']);

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
	
	function seznam_cz(){
		// nastavim si layout do ktereho budu cpat data v XML
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products('zbozi.cz');
		$this->set('products', $products);
		
		// produkty zobrazovane na detailu na firmy.cz
		$this->set('firmy_cz_products', array(762, 971, 880, 363, 654));
	}
	
	function heureka_cz() {
		$this->layout = 'xml/heureka';
		
		$products = $this->get_products('heureka.cz');

		// sparovani kategorii na heurece s kategoriemi u nas v obchode
		$pairs = array(
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky' => array(228, 229, 264, 266, 276, 332),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na bolesti hlavy' => array(230, 231),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na klouby a kosti' => array(232, 269, ),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na chřipku' => array(233, 234, 239),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na bolesti v krku' => array(235),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na kašel' => array(236, 237),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na rýmu' => array(238),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na akné' => array(348),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na bradavice' => array(247),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na ekzém' => array(248),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na jizvy' => array(249),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na plísně' => array(250),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na opary' => array(312),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Vitamíny pro těhotné a kojící' => array(257, 258, 313, 259),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Vitamíny a minerály' => array(265, 365, 366, 323),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na posílení imunity' => array(367, 274),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Dětské vitamíny' => array(322),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na ledviny a močový měchýř' => array(267),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na cévy' => array(268),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na únavu' => array(270),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na střeva' => array(271, 272),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na obezitu' => array(273),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na klimakterium' => array(275),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na alergie, astma' => array(324, 333),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na mozek, paměť' => array(373),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Diagnostické testy' => array(326, 327, 328, 368),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Diagnostické testy | Těhotenské testy' => array(325),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na zácpu' => array(334),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na průjem' => array(335),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na žaludeční vředy' => array(336),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na žaludeční vředy' => array(337),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky proti nadýmání' => array(338, 339),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Přípravky na játra' => array(372),
			'Kosmetika a zdraví | Zdraví | Léky, vitamíny a potravinové doplňky | Homeopatika' => array(297, 298, 299),
				
				
			'Kosmetika a zdraví | Zdraví | Péče o zuby' => array(255, 261),
			'Kosmetika a zdraví | Zdraví | Péče o zuby | Zubní kartáčky' => array(307),
			'Kosmetika a zdraví | Zdraví | Péče o zuby | Mezizubní péče | Mezizubní kartáčky' => array(308),
			'Kosmetika a zdraví | Zdraví | Péče o zuby | Zubní pasty' => array(309),
			'Kosmetika a zdraví | Zdraví | Péče o zuby | Ústní vody' => array(310),
			'Kosmetika a zdraví | Zdraví | Péče o zuby | Péče o umělý chrup' => array(311),
			'Kosmetika a zdraví | Zdraví | Repelenty' => array(256),
			'Kosmetika a zdraví | Zdraví | Zdravotní potřeby' => array(289, 294),
			'Kosmetika a zdraví | Zdraví | Zdravotní potřeby | Náplasti' => array(290),
			'Kosmetika a zdraví | Zdraví | Zdravotní potřeby | Obvazové materiály' => array(291),
			'Kosmetika a zdraví | Zdraví | Zdravotní potřeby | Lékárničky' => array(292),
			'Kosmetika a zdraví | Zdraví | Zdravotní potřeby | Přípravky na inkontinenci' => array(329),
			'Kosmetika a zdraví | Zdraví | Oční optika | Roztoky a pomůcky ke kontaktním čočkám' => array(296),
			'Kosmetika a zdraví | Zdraví | Přístroje | Inhalátory' => array(369),

			'Kosmetika a zdraví | Kosmetika' => array(241, 242, 243, 244, 245, 306, 295),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika' => array(352),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika | Přípravky pro péči o oční okolí' => array(347),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika | Přípravky na problematickou pleť' => array(349, 350, 340, 341, 346, 371),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika | Speciální péče o pleť' => array(351, 353, 356, 358, 343),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika | Přípravky na vrásky a stárnoucí pleť' => array(354, 355, 344),
			'Kosmetika a zdraví | Kosmetika | Pleťová kosmetika | Přípravky na stařecké skvrny' => array(342),
			'Kosmetika a zdraví | Kosmetika | Dekorativní kosmetika | Přípravky na tvář' => array(357),
			'Kosmetika a zdraví | Kosmetika | Vlasová kosmetika' => array(359, 252),
			'Kosmetika a zdraví | Kosmetika | Tělová kosmetika' => array(360, 314),
			'Kosmetika a zdraví | Kosmetika | Tělová kosmetika | Přípravky pro péči o ruce a nehty' => array(361, 254),
			'Kosmetika a zdraví | Kosmetika | Tělová kosmetika | Přípravky pro péči o nohy' => array(362, 253),
			'Kosmetika a zdraví | Kosmetika | Sluneční ochrana | Přípravky na opalování' => array(363),
			'Kosmetika a zdraví | Kosmetika | Sluneční ochrana | Přípravky po opalování' => array(370),
			'Kosmetika a zdraví | Kosmetika | Dětská kosmetika' => array(345, 262, 317, 318, 319, 320, 321),
			'Kosmetika a zdraví | Kosmetika | Intimní kosmetika' => array(251),
			'Kosmetika a zdraví | Kosmetika | Intimní kosmetika | Hygienické vložky' => array(330),
				
			'Dětské zboží | Dětská výživa' => array(260),
			'Dětské zboží | Dětská výživa | Kojenecká mléka' => array(315, 316),
			'Dětské zboží | Pleny' => array(331),
				
			'Sport | Sportovní výživa | Ostatní sportovní výživa' => array(277, 364),
			'Sport | Sportovní výživa | Proteiny' => array(278),
			
			'Jídlo a nápoje | Nápoje | Nealkoholické nápoje | Čaje' => array(284, 287, 288),
			'Jídlo a nápoje | Nápoje | Nealkoholické nápoje | Bylinné čaje' => array(285, 286),
				
			'Bílé zboží | Malé spotřebiče | Péče o tělo | Zdravotní měřicí přístroje | Teploměry osobní' => array(293)
		);

		foreach ($products as $index => $product) {
			// pokud je kategorie produktu sparovana s heurekou, nastavi se rovnou jako 'Sportovni vyziva | *odpovidajici nazev kategorie*
			foreach ($pairs as $name => $array) {
				if (in_array($product['CategoriesProduct']['category_id'], $array)) {
					$products[$index]['CATEGORYTEXT'] = $name;
					break;
				}
			}

			// jinak se vytvori retezec ze stromu kategorii v obchode
			if (!isset($products[$index]['CATEGORYTEXT'])) {
				$path = $this->Product->CategoriesProduct->Category->getPath($product['CategoriesProduct']['category_id']);
				$keys = Set::extract('/Category/name', $path);
				unset($keys[0]);
				unset($keys[1]);
				$products[$index]['CATEGORYTEXT'] = implode(' | ', $keys);
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
