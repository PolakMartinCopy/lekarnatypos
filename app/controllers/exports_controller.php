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
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
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
		$res_ids = array();
		foreach ($products as $i => &$product) {
			// kazdy produkt chci ve vystupu pouze jednou
			$to_res = true;
			if (!in_array($product['Product']['id'], $res_ids)) {
				$product['Product']['name'] = str_replace('&times;', 'x', $product['Product']['name']);
				$product['Product']['short_description'] = str_replace('&times;', 'x', $product['Product']['short_description']);
				$res[] = $product;
				$res_ids[] = $product['Product']['id'];				
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
		$this->layout = 'xml/zbozi';
		
		$products = $this->get_products(2);
		$this->set('products', $products);
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
			foreach ($shippings as $shipping) {
				$free_shipping_product = $this->Export->Product->FreeShippingProduct->getByProductShipping($product['Product']['id'], $shipping['Shipping']['id']);
				if (!empty($free_shipping_product)) {
					$products[$index]['Product']['Shipping'][] = array(
						'id' => $shipping['Shipping']['id'],
						'free_shipping_min_quantity' => $free_shipping_product['FreeShippingProduct']['quantity']
					);
				}
			}			
		}

		$this->set('products', $products);
		
	}
	
	function save_google_feed() {
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/exports/google_merchant';
		$content = download_url($url);
		$file_name = 'files/xml/google_merchant.xml';
		file_put_contents($file_name, $content);
		die('here');
	}
	
	function google_merchant() {
		// bez layoutu
		$this->autoLayout = false;
		
		// natahnu si model Product
		$this->Export->Product = ClassRegistry::init('Product');
		// typ uzivatele - neprihlaseny
		$customer_type_id = 2;
		// srovnavac - Google Merchant Center
		$comparator_id = 3;
		// podminky pro vyhledani produktu
		$conditions = array(
				"Product.short_description != ''",
				'Availability.cart_allowed' => true,
				'Product.active' => true,
				'Product.feed' => true,
				'Product.price >' => 0
		);
		
		// podminky pro vyhledani kategorii
		$categories_conditions = 'Category.id = CategoriesProduct.category_id';
		// vytahnu si kategorie, ktere nejsou aktivni
		$not_active_categories = $this->Export->Product->CategoriesProduct->Category->find('all', array(
				'conditions' => array('Category.active' => false),
				'contain' => array(),
				'fields' => array('Category.id')
		));
		// zjistim idcka podstromu neaktivnich kategorii
		$not_active_categories_ids = array();
		if (!empty($not_active_categories)) {
			foreach ($not_active_categories as $not_active_category) {
				$not_active_categories_ids = array_merge($not_active_categories_ids, $this->Export->Product->CategoriesProduct->Category->subtree_ids($not_active_category['Category']['id']));
			}
		}
		// nechci do zdroje produkty z neaktivnich kategorii
		if (!empty($not_active_categories_ids)) {
			$categories_conditions .= ' AND Category.id NOT IN (' . implode(',', $not_active_categories_ids) . ')';
		}
		$categories_conditions_arr[] = $categories_conditions;
		
		$this->Export->Product->virtualFields['price'] = $this->Export->Product->price;
		$products = $this->Export->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'Manufacturer' => array(
					'fields' => array('id', 'name')
				),
			),
			'joins' => array(
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPrice',
					'type' => 'INNER',
					'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customer_type_id)
				),
				array(
					'table' => 'customer_type_product_prices',
					'alias' => 'CustomerTypeProductPriceCommon',
					'type' => 'LEFT',
					'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'INNER',
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
					'table' => 'categories_comparators',
					'alias' => 'CategoriesComparator',
					'type' => 'LEFT',
					'conditions' => array('Category.id = CategoriesComparator.category_id AND CategoriesComparator.comparator_id = ' . $comparator_id)
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
					
				'CategoriesComparator.id',
				'CategoriesComparator.path',
 
				'Manufacturer.id',
				'Manufacturer.name',
			),
//			'limit' => 1000
		));
		unset($this->Export->Product->virtualFields['price']);

		$res = array();
		$res_ids = array();
		foreach ($products as $i => &$product) {
			if (!in_array($product['Product']['id'], $res_ids)) {

				$subproducts = $this->Export->Product->Subproduct->find('all', array(
					'conditions' => array('Subproduct.product_id' => $product['Product']['id']),
					'contain' => array(),
					'fields' => array('Subproduct.id')
				));
				if (!empty($subproducts)) {
					foreach ($subproducts as &$subproduct) {
						$subproduct = $this->Export->Product->Subproduct->getById($subproduct['Subproduct']['id'], true);
						$product['Subproduct'][] = $subproduct['Subproduct'];
					}
				}
				
				$product['Product']['type_text'] = $this->Export->Product->CategoriesProduct->Category->getPath($product['CategoriesProduct']['category_id']);
				if (!empty($Product['Product']['type_text'])) {
					unset($product['Product']['type_text'][0]);
					unset($product['Product']['type_text'][1]);
				}
				$product['Product']['type_text'] = Set::extract('/Category/name', $product['Product']['type_text']);
				$product['Product']['type_text'] = implode(' | ', $product['Product']['type_text']);
				
				$product['Product']['name'] = str_replace('&times;', 'x', $product['Product']['name']);
				$product['Product']['short_description'] = str_replace('&times;', 'x', $product['Product']['short_description']);
				$res[] = $product;
				$res_ids[] = $product['Product']['id'];
			}
		}

		$this->set('products', $res);
	}
}
?>
