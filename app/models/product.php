<?php
class Product extends AppModel {

	var $name = 'Product';
	
	var $actsAs = array('Containable');
	
	var $hasAndBelongsToMany = array(
		'Cart' => array('className' => 'Cart'),
		'Flag' => array('className' => 'Flag')
	);

	var $hasMany = array(
		'Subproduct' => array(
			'dependent' => true
		),
		'Image' =>array(
			'dependent' => true
		),
		'ProductDocument' => array(
			'dependent' => true	
		),
		'CartsProduct' => array(
			'dependent' => true
		),
		'Comment' => array(
			'dependent' => true
		),
		'CategoriesProduct' => array(
			'dependent' => true
		),
		'RelatedProduct' => array(
			'dependent' => true
		),
		'CustomerTypeProductPrice' => array(
			'dependent' => true
		),
		'RecommendedProduct' => array(
			'dependent' => true
		),
		'OrderedProduct',
		'ComparatorProductClickPrice' => array(
			'dependent' => true
		),
		'CategoriesMostSoldProduct' => array(
			'dependent' => true
		),
		'MostSoldProduct' => array(
			'dependent' => true
		),
		'DiscountedProduct' => array(
			'dependent' => true
		),
		'ProductPropertiesProduct' => array(
			'dependent' => true
		),
		'FreeShippingProduct' => array(
			'dependent' => true
		),
		'TSVisitProduct',
		'TSCartAddition'
	);

	var $belongsTo = array(
		'Manufacturer' => array('className' => 'Manufacturer',
			'foreignKey' => 'manufacturer_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => ''),
		'TaxClass' => array('className' => 'TaxClass',
			'foreignKey' => 'tax_class_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => ''),
		'Availability',
		'ProductType'
	);
	
	var $order = array('Product.active' => 'desc', 'Product.priority' => 'asc');
	
	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'message' => 'Název produktu musí být vyplněn!'
		),
		'short_description' => array(
			'rule' => array('minLength', 1),
			'message' => 'Krátký popis produktu musí být vyplněn!'
		),
		'retail_price_with_dph' => array(
			'rule' => array('minLength', 1),
			'message' => 'Cena produktu musí být vyplněna!'
		),
		'tax_class_id' => array(
			'rule' => array('minLength', 1),
			'message' => 'Není vybrána žádná daňová třída!'
		),
/*		'ean' => array(
			'length13' => array(
				'rule' => array('between', 12, 13),
				'message' => 'EAN musí mít 13 znaků',
				'allowEmpty' => true
			)
		) */
	);
	
	var $price = 'FLOOR(IF(CustomerTypeProductPrice.price, CustomerTypeProductPrice.price, IF(CustomerTypeProductPriceCommon.price, CustomerTypeProductPriceCommon.price, IF(Product.discount_common, Product.discount_common, Product.retail_price_with_dph))))';
		
	var $virtualFields = array(
		'rate' => 'ROUND(COALESCE(Product.overall_rate / Product.voted_count))'	
	);
	
	var $product_types = null;
	
	var $sorting_options = array(
		0 => array('name' => 'Doporučujeme', 'conditions' => array('Product.is_akce' => 'desc', 'Product.priority' => 'asc')),
			array('name' => 'Od nejnovějšího', 'conditions' => array('Product.created' => 'desc')),
			array('name' => 'Od nejlevnějšího', 'conditions' => array('Product.price' => 'asc')),
			array('name' => 'Od nejdražšího', 'conditions' => array('Product.price' => 'desc')),
			array('name' => 'Abecedně A-Z', 'conditions' => array('Product.name' => 'asc')),
			array('name' => 'Abecedně Z-A', 'conditions' => array('Product.name' => 'desc')),
			array('name' => 'Abecedně dle výrobce A-Z', 'conditions' => array('Manufacturer.name' => 'asc')),
			array('name' => 'Abecedně dle výrobce Z-A', 'conditions' => array('Manufacturer.name' => 'desc'))
	);
	
	var $search_properties = array(
		0 => array(
			'id' => 0,
			'name' => 'Vše',
			'conditions' => array()
		),
		array(
			'id' => 1,
			'name' => 'Bez EANu',
			'conditions' => array('(Product.ean IS NULL OR Product.ean = "")')
		),
		array(
			'id' => 2,
			'name' => 'Neaktivní',
			'conditions' => array(
				'Product.active' => false
			)
		),
		array(
			'id' => 3,
			'name' => 'Nelze vložit do košíku',
			'conditions' => array(
				'Availability.cart_allowed' => false
			)
		),
		array(
			'id' => 4,
			'name' => 'Nezařazení v kategorii',
			'conditions' => array(
				'CategoriesProduct.id IS NULL'
			)
		),
		array(
			'id' => 5,
			'name' => 'Nejsou v nových kategoriích'
		),
		array(
			'id' => 6,
			'name' => 'Duplicitní podle EAN'
		)
	);
	
	function __construct() {
		parent::__construct();
		$this->product_types = $this->ProductType->find('list', array(
			'fields' => array('ProductType.id', 'ProductType.text')
		));
		$this->discount = '100 - ROUND(100 * ' . $this->price . '/ Product.retail_price_with_dph)';
	}
	
	function beforeValidate() {
		// udelam si kontrolu, jestli je vyplneny titulek a url
		if (array_key_exists('title', $this->data['Product']) && empty($this->data['Product']['title'])){
			$this->data['Product']['title'] = $this->data['Product']['name'];
		}
		// zkontroluju, jestli jsou vyplnene heading, breadcrumb, zbozi a related name
		if (array_key_exists('heading', $this->data['Product']) && empty($this->data['Product']['heading'])) {
			$this->data['Product']['heading'] = $this->data['Product']['name'];
		}
		if (array_key_exists('breadcrumb', $this->data['Product']) && empty($this->data['Product']['breadcrumb'])) {
			$this->data['Product']['breadcrumb'] = $this->data['Product']['name'];
		}
		if (array_key_exists('related_name', $this->data['Product']) && empty($this->data['Product']['related_name'])) {
			$this->data['Product']['related_name'] = $this->data['Product']['name'];
		}
		if (array_key_exists('zbozi_name', $this->data['Product']) && empty($this->data['Product']['zbozi_name'])) {
			$this->data['Product']['zbozi_name'] = $this->data['Product']['name'];
		}
		if (array_key_exists('heureka_name', $this->data['Product']) && empty($this->data['Product']['heureka_name'])) {
			$this->data['Product']['heureka_name'] = $this->data['Product']['name'];
		}
	}
	
	function beforeSave() {
		// uprava pole s cenou, aby se mohlo vkladat take s desetinnou carkou
		if (array_key_exists('retail_price_with_dph', $this->data['Product'])) {
			$this->data['Product']['retail_price_with_dph'] = str_replace(',', '.', $this->data['Product']['retail_price_with_dph']);
			$this->data['Product']['retail_price_with_dph'] = floatval($this->data['Product']['retail_price_with_dph']);
		}
		// uprava kategorie na heurece, at se da vkladat pouze cut&paste
		if (array_key_exists('heureka_category', $this->data['Product'])) {
			$this->data['Product']['heureka_category'] = str_replace('»', '|', $this->data['Product']['heureka_category']);
			$this->data['Product']['heureka_category'] = str_replace('Heureka.cz | ', '', $this->data['Product']['heureka_category']);
		}
		
		if (!empty($this->data['Product']['discount_common'])) {
			$this->data['Product']['discount_common'] = floatval(str_replace(',', '.', $this->data['Product']['discount_common']));
		}
		if (array_key_exists('CustomerTypeProductPrice', $this->data)) {
			foreach ($this->data['CustomerTypeProductPrice'] as &$ctpp) {
				$ctpp['price'] = str_replace(',', '.', $ctpp);
				$ctpp['price'] = floatval($ctpp);
			}
		}
		
		return true;
	}
	
	function afterSave($created) {
		if ($created) {
			// vygeneruju url
			if ($url = $this->buildUrl($this->data)) {
				$product = array(
					'Product' => array(
						'id' => $this->id,
						'url' => $url
					)
				);
				return $this->save($product);
			} else {
				return false;
			}
		}
	
		return true;
	}
	
	function buildUrl($product) {
		if (isset($product['Product']['name']) && isset($this->id)) {
			return strip_diacritic($product['Product']['name']) . '-p' . $this->id;
		}
		trigger_error('Nejsou potrebna data k vytvoreni url produktu', E_USER_ERROR);
		return false;
	}
	
	function assign_discount_price($product){
		App::import('Helper', 'Session');
		$this->Session = new SessionHelper;

		$discount_price = $product['Product']['retail_price_with_dph'];
		
		// vychozi sleva je obecna sleva
		if ($product['Product']['discount_common'] > 0 && $product['Product']['discount_common'] < $discount_price) {
			$discount_price = $product['Product']['discount_common'];
		}
		
		// jestlize je uzivatel prihlaseny
		if ($this->Session->check('Customer')) {
			// zjistim, jestli je pro dany typ uzivatele zadana sleva produktu a pokud ano, jestli je mensi, nez sleva obecna
			$customer = $this->Session->read('Customer');
			// pokud ma uzivatel prirazeny customer_type
			if (isset($customer['customer_type_id'])) {
				// najdu typ daneho customera, abych podle poradi typu mohl vzit nejblizsi vyssi slevu 
				$customer_type = $this->CustomerTypeProductPrice->CustomerType->find('first', array(
					'conditions' => array('CustomerType.id' => $customer['customer_type_id']),
					'contain' => array(),
					'fields' => array('CustomerType.order')
				));
				
				// najdu cenu produktu, ktera odpovida dane skupine customeru (nebo nejblizsi slevu v hierarchii typu smerem dolu)
				$discount = $this->CustomerTypeProductPrice->find('first', array(
					'conditions' => array(
						'CustomerType.order <=' => $customer_type['CustomerType']['order'],
						'CustomerTypeProductPrice.product_id' => $product['Product']['id'],
						'CustomerTypeProductPrice.price IS NOT NULL'
					),
					'contain' => array('CustomerType'),
					'fields' => array('CustomerTypeProductPrice.price'),
					'order' => array('CustomerType.order' => 'desc')
				));
	
				// podivam se, jestli je zadana sleva pro prihlasene a je mensi, nez obecna sleva
				if (!empty($discount) && $discount['CustomerTypeProductPrice']['price'] && $discount['CustomerTypeProductPrice']['price'] > 0 && $discount['CustomerTypeProductPrice']['price'] < $discount_price) {
					// kdyz jo, tak ji dam jako vyslednou slevu
					$discount_price = $discount['CustomerTypeProductPrice']['price'];
				}
			}
		}
		
		return $discount_price;
	}
	
	function copy_images($new_product_id, $images) {
		if (!empty($images)) {
			foreach ($images as $image) {
				// zkopiruju fyzicky na disku
				if (file_exists('product-images/' . $image['name'])) {
					// zjistim si jmeno obrazku a musim ho prejmenovat
					$image_name = explode('.', $image['name']);
					$i = 1;
					while ( file_exists('product-images/' . $image_name[0] . '_' . $i . '.jpg') ){
						$i = $i + 1;
					}
						
					// vim jake muzu dat nove jmeno obrazku
					// obstaram na disku kopii obrazku
					if ( !copy('product-images/' . $image['name'], 'product-images/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
						
					if ( !copy('product-images/small/' . $image['name'], 'product-images/small/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat SMALL obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
						
					if ( !copy('product-images/medium/' . $image['name'], 'product-images/medium/' . $image_name[0] . '_' . $i . '.jpg') ){
						return 'Nepodařilo se zkopírovat MEDIUM obrázek ' . $image['name'] . ' do ' . $image_name[0] . '_' . $i . '.jpg';
					}
				} else {
					return 'Nepodařilo se nalézt obrázek ' . $image['name'] . ' na disku.';
				}
	
				// vyresetuju si ID obrazku
				unset($this->Image->id);
	
				$new_image_data = array(
					'name' => $image_name[0] . '_' . $i . '.jpg',
					'product_id' => $new_product_id,
					'is_main' => $image['is_main']
				);
	
				if (!$this->Image->save($new_image_data)) {
					return 'Nepodařilo se uložit nový obrázek ' . $new_image_data['name'] . ' do databáze.';
				}
			}
		}
		return true;
	}

	function get_subproducts($id) {
		$options = $this->Subproduct->AttributesSubproduct->Attribute->Option->find('list');
		
		// projdu si existujici atributy a k nim si priradim subprodukty
		$subs = array();
		$hasAttributes = false;
		$this->Subproduct->unbindModel(array('belongsTo' => array('Product')));
		$subproducts = $this->Subproduct->find('list', array(
			'conditions' => array('product_id' => $id),
			'contain' => array()
		));
		//
		foreach ( $options as $option => $value ){
			
			$attributes = $this->Subproduct->AttributesSubproduct->find('all',
				array(
					'conditions' => array(
						'AttributesSubproduct.subproduct_id' => $subproducts,
						'Attribute.option_id' => $option
					),
					'contain' => array(
						'Attribute'
					),
					'order' => array('Attribute.sort_order' => 'asc'),
					'fields' => array('DISTINCT Attribute.id', 'Attribute.value')
				)
			);

			if ( $this->Subproduct->AttributesSubproduct->getNumRows() > 0 ){
				$hasAttributes = true;
			}
			$subs[$option] = array('Option' => array('name' => $options[$option], 'id' => $option));
			foreach ( $attributes as $attribute ){
				$subs[$option]['Value'][] = array(
					'id' => $attribute['Attribute']['id'],
					'name' => $options[$option],
					'value' => $attribute['Attribute']['value'],
				);
			}
		}
		if ( !$hasAttributes ){
			$subs = null;
		}
		
		return $subs;
	}
	
	function checkSubproductChoices($data){
		// zkontrolujeme, jestli pri vkladani do kosiku
		// jsou zvolene vsechny atributy produktu,
		// ktere se zvolit daji

		// nacitam si vsechny options ktere produkt muze mit
		$options = $this->optionsList($data['Product']['id']);

		// pokud nejake muze mit, musim zkontrolovat zda jsou zadany
		if ( !empty($options) ){
			foreach ( $options as $option ){
				$index = $option['Attribute']['option_id'];
				if ( !isset($data['Product']['Option'][$index]) || empty($data['Product']['Option'][$index])  ){
					return false;
				}
			}
		}
		return true;
	}

	function optionsList($id){
		return $this->query('SELECT DISTINCT (Attribute.option_id) FROM attributes Attribute, subproducts s WHERE s.product_id = ' . $id . ' AND s.attribute_id = Attribute.id');
	}

			/**
	 * z atributu produktu tvori vsechny jejich mozne kombinace 
	 *
	 * @param pole atributu $array
	 * @return pole vsech moznych kombinaci vstupnich atributu
	 */
	function combine($array) {
		$res = array();
		if (!empty($array)) {
			$first = current($array);
			array_shift($array);
			$tail = $array;
			if (empty($tail)) {
				foreach ($first as $item) {
					$res[] = array($item);
				}
			} else {
				foreach ($first as $item) {
					foreach ($this->combine($tail) as $j) {
						$res[] = array_merge(array($item), $j);
					}
				}
			}
		}
		return $res;
	}
	
	function sort_by_price($products, $direction){
		function sort_by_final_price_desc($a, $b){
			$a_final_price = $a['Product']['retail_price_with_dph'];
			if ( !empty($a['Product']['discount_price']) ){
				$a_final_price = $a['Product']['discount_price'];
			}
			
			$b_final_price = $b['Product']['retail_price_with_dph'];
			if ( !empty($b['Product']['discount_price']) ){
				$b_final_price = $b['Product']['discount_price'];
			}
			
			return $a_final_price < $b_final_price;
		}

		function sort_by_final_price_asc($a, $b){
			$a_final_price = $a['Product']['retail_price_with_dph'];
			if ( !empty($a['Product']['discount_price']) ){
				$a_final_price = $a['Product']['discount_price'];
			}
			
			$b_final_price = $b['Product']['retail_price_with_dph'];
			if ( !empty($b['Product']['discount_price']) ){
				$b_final_price = $b['Product']['discount_price'];
			}
			
			return $b_final_price < $a_final_price;
		}

		usort($products, 'sort_by_final_price' . $direction);
		return $products;
	}
	
	/**
	 * Updatuje zasobnik v minulosti navstivenych produktu
	 * @param array $stack
	 * @param int $product_id
	 * @return multitype:array
	 */
	function update_stack($stack, $product_id) {
		// v zasobniku muze byt max 2 produktu
		$stack_size = 8;
		// najdu produkt, ktery zakaznik navstivil
		$product = $this->find('first', array(
			'conditions' => array('Product.id' => $product_id),
			'contain' => array(),
			'fields' => array('Product.id', 'Product.name', 'Product.url')
		));
		if (!empty($product)) {
			// pokud uz zakaznik ma neco v zasobniku navstivenych produktu
			if ($stack) {
				// pokud jiz mam v zasobniku prave navstiveny produkt, vypustim ho
				$filter_func = function($element) use ($product_id) {
					return $element['Product']['id'] != $product_id;
				};
				$stack = array_filter($stack, $filter_func);

				// pridam prave navstiveny produkt na zacatek
				array_unshift($stack, $product);
				
				// vypustim vsechny produkty na konci zasobniku, aby jeho velikost byla max $stack_size
				$stack = array_slice($stack, 0, $stack_size);
			} else {
				// jinak vytvorim zasobnik, kde bude navstiveny produkt nahore
				$stack = array(0 => $product);
			}
		}
		return $stack;
	}
	
	/**
	 * Vrati 10 podobnych produktu:
	 * 	a) vybere souvisejici produkty k danemu produktu
	 * 	b) zbytek budou produkty nejvice prodavane s danym produktem, ktere nejsou oznacene jako related 
	 * @param int $id
	 */
	function similar_products($id, $customer_type_id) {
		$limit = 10;
		
		// natahnu souvisejici produkty
		$related_products = $this->related_products($id, $customer_type_id);
		// snizim limit o pocet souvisejicich produktu
		$limit -= count($related_products);
		// dohledam si zbytek produktu podle toho, ze byly nejprodavanejsi s danym produktem za posledni mesic
		$excluded_ids = Set::extract('/Product/id', $related_products);
		$range = '-2 months';
		$options = array(
			'excluded_ids' => $excluded_ids,
			'range' => $range,
			'limit' => $limit
		);
		$most_sold_with_products = $this->most_sold_with_products($id, $customer_type_id, $options);
		
		$products = array_merge($related_products, $most_sold_with_products);

		return $products;
	}
	
	function right_sidebar_products($id, $customer_type_id) {
		$product = $this->find('first', array(
			'conditions' => array('Product.id' => $id),
			'contain' => array('CategoriesProduct'),
			'fields' => array('Product.id')	
		));
		
		$products = array();
		if (!empty($product['CategoriesProduct'])) {
			$products = $this->find('all', array(
				'conditions' => array(
					'Product.active' => true,
					'CategoriesProduct.category_id' => $product['CategoriesProduct'][0]['category_id'],
					'Availability.cart_allowed' => true,
					'Product.id !=' => $product['Product']['id']
				),
				'contain' => array(),
				'joins' => array(
					array(
						'table' => 'categories_products',
						'alias' => 'CategoriesProduct',
						'type' => 'INNER',
						'conditions' => array('Product.id = CategoriesProduct.product_id')
					),
					array(
						'table' => 'availabilities',
						'alias' => 'Availability',
						'type' => 'INNER',
						'conditions' => array('Product.availability_id = Availability.id')
					),
					array(
						'table' => 'images',
						'alias' => 'Image',
						'type' => 'INNER',
						'conditions' => array('Product.id = Image.product_id AND Image.is_main = 1')
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
				'fields' => array(
					'Product.id',
					'Product.name',
					$this->price . ' AS price',
					'Product.url',
					'Product.retail_price_with_dph',
					'Image.id',
					'Image.name'
				),
				'limit' => 3
			));
		}
		return $products;
	}
	
	function paginateCount($conditions, $recursive, $extra) {
		$parameters = compact('conditions');
		if ($recursive != $this->recursive) {
			$parameters['recursive'] = $recursive;
		}
		$parameters = array_merge($parameters, $extra);
		$parameters['fields'] = array('id');
		$count = $this->find('all', $parameters);
		return count($count);
	}
	
	function redirect_url($url) {
		$redirect_url = '/';
		// zjistim na co chci presmerovat
		// odstranim cast adresy, ktera mi urcuje, ze se jedna o produkt
		if (preg_match('/^\/product\//', $url)) {
			$pattern = preg_replace('/^\/product\//', '', $url);
			
			// vytahnu si id produktu na sportnutritionu
			if (preg_match('/^[^:]+:(\d+)/', $pattern, $matches)) {
				$sn_id = $matches[1];
			}
		} elseif (preg_match('/^\/produkty-id\/(\d+)/', $url, $matches)) {
			$sn_id = $matches[1];
		}

		if (isset($sn_id) && !empty($sn_id)) {
			// najdu nas produkt odpovidajici sn adrese
			$product = $this->find('first', array(
				'conditions' => array('Product.id' => $sn_id),
				'contain' => array(),
				'fields' => array('Product.id', 'Product.url')
			));
			if (!empty($product)) {
				// vratim url pro presmerovani
				$redirect_url = $product['Product']['url'];
			}
		}

		return $redirect_url;
	}
	
	function get_list($id = null){
		// option nese informaci o tom, ktery seznam chceme publikovat
		switch ( $id ){
			case "most_sold":
				// seznam nejprodavanejsich produktu
				$most_sold = $this->MostSoldProduct->find('all', array(
					'conditions' => array('Product.active' => true),
					'contain' => array('Product'),
					'fields' => array('Product.id')
				));
				$list = Set::extract('/Product/id', $most_sold);
				break;
			case "suggested":
				// seznam doporucenych produktu
				$recomended = $this->RecommendedProduct->find('all', array(
					'conditions' => array('Product.active' => true),
					'contain' => array('Product'),
					'fields' => array('Product.id')
				));
				$list = Set::extract('/Product/id', $recomended);
				break;
			case "newest":
				// seznam doporucenych produktu
				$newest = $this->DiscountedProduct->find('all', array(
					'conditions' => array('Product.active' => true),
					'contain' => array(),
					'fields' => array('Product.id')
				));
				$list = Set::extract('/Product/id', $newest);
				break;
					
		}
	
		// seznam vlozim do podminek
		$conditions = array(
			'Product.id' => $list,
		);
	
		$this->virtualFields['price'] = $this->price;
		// vyhledam si produkty
		$products = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'Image' => array(
						'conditions' => array('is_main' => '1')
				)
			),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.price',
			)
		));
		unset($this->virtualFields['price']);
	
		return $products;
	}
	
	function get_action_products($customer_type_id, $limit = 3) {
		// nejprodavanejsi produkty
		$category_most_sold = $this->CategoriesProduct->Category->most_sold_products(5, $customer_type_id, $limit);

		return $category_most_sold;
	}
	
	function image_name($name, $suffix = 'jpg') {
		if (is_numeric($name)) {
			$product = $this->find('first', array(
					'conditions' => array('Product.id' => $name),
					'contain' => array(),
					'fields' => array('Product.name')
			));
			$name = $product['Product']['name'];
		}
		// vygeneruju nazev obrazku
		$image_name = strip_diacritic($name . '.' . $suffix, false);
		// zjistim, jestli nemusim obrazek cislovat
		$image_name = $this->Image->checkName('product-images/' . $image_name);
		$image_name = explode("/", $image_name);
		$image_name = $image_name[count($image_name) -1];
		return $image_name;
	}
	
	function do_form_search($conditions, $data) {
		if (isset($data['Category']['id']) && !empty($data['Category']['id']) && $data['Category']['id'] != 0) {
			$conditions['CategoriesProduct.category_id'] = $this->CategoriesProduct->Category->subtree_ids($data['Category']['id']);
		}
		$fulltext_fields = array('fulltext1', 'fulltext2');
		foreach ($fulltext_fields as $fulltext_field) {
			if (isset($data['Product'][$fulltext_field]) && !empty($data['Product'][$fulltext_field])) {
				$conditions[] = array(
					'OR' => array(
						array('Product.id' => $data['Product'][$fulltext_field]),
						array('Product.name LIKE "%%' . $data['Product'][$fulltext_field] . '%%"'),
						array('Manufacturer.name LIKE "%%' . $data['Product'][$fulltext_field] . '%%"'),
						array('Product.short_description LIKE "%%' . $data['Product'][$fulltext_field] . '%%"'),
						array('Product.description LIKE "%%' . $data['Product'][$fulltext_field] . '%%"'),
						array('Product.ean LIKE "%%' . $data['Product'][$fulltext_field] . '%%"'),
						array('Product.sukl LIKE "%%' . $data['Product'][$fulltext_field] . '%%"'),
						array('Product.code LIKE "%%' . $data['Product'][$fulltext_field] . '%%"'),
					)
				);
			}
		}
		if (isset($data['Product']['search_property_id']) && !empty($data['Product']['search_property_id'])) {
			if ($data['Product']['search_property_id'] == 5) {
				// zjistim id produktu, ktere jsou v novem podstrome
				$complement_products = $this->CategoriesProduct->find('all', array(
					'conditions' => array('CategoriesProduct.category_id IN (' . implode(',', $this->CategoriesProduct->Category->subtree_ids(398)) . ',' . implode(',', $this->CategoriesProduct->Category->subtree_ids(408)) . ')'),
					'contain' => array(),
					'fields' => array('DISTINCT CategoriesProduct.product_id')
				));

				if (!empty($complement_products)) {
					$complement_products = SET::extract('/CategoriesProduct/product_id', $complement_products);
					// a vypisu vsechny ostatni
					$conditions[] = 'Product.id NOT IN (' . implode(',', $complement_products) . ')';
				}
			} elseif ($data['Product']['search_property_id'] == 6) {
				$duplicities = $this->find('all', array(
					'conditions' => array(),
					'contain' => array(),
					'fields' => array('Product.id'),
					'joins' => array(
						array(
							'table' => 'products',
							'alias' => 'Product1',
							'type' => 'INNER',
							'conditions' => array('Product.ean IS NOT NULL AND Product.ean != "" AND Product.ean = Product1.ean AND Product.id != Product1.id')
						)
					)
				));

				if (!empty($duplicities)) {
					$duplicities = SET::extract('/Product/id', $duplicities);
					// a vypisu vsechny ostatni
					$conditions[] = 'Product.id IN (' . implode(',', $duplicities) . ')';
				}
			} else {
				foreach ($this->search_properties as $search_property) {
					if ($search_property['id'] == $data['Product']['search_property_id']) {
						$conditions[] = $search_property['conditions'];
						break;
					}
				}
			}
		}
		
		if (isset($data['Product']['is_alliance']) && !empty($data['Product']['is_alliance'])) {
			if ($data['Product']['is_alliance'] == 1) {
				$conditions[] = '(Product.supplier_id NOT IN (4, 5) OR Product.supplier_id IS NULL)';
			} elseif ($data['Product']['is_alliance'] == 2) {
				$conditions[] = 'Product.supplier_id IN (4, 5)';
			}
		}
		
		return $conditions;
	}
	
	function update_product_property($id, $product_property_id, $update) {
		// nastavim si ukladanou hodnotu
		$save = array(
			'ProductPropertiesProduct' => array(
				'update' => $update
			)
		);
		// mam v systemu pro danou property a produkt vztah?
		$product_properties_product = $this->ProductPropertiesProduct->find('first', array(
			'conditions' => array(
				'ProductPropertiesProduct.product_id' => $id,
				'ProductPropertiesProduct.product_property_id' => $product_property_id
			),
			'contain' => array(),
			'fields' => array('ProductPropertiesProduct.id')
		));
		
		// pokud ne, vytvorim novy
		if (empty($product_properties_product)) {
			$save['ProductPropertiesProduct']['product_id'] = $id;
			$save['ProductPropertiesProduct']['product_property_id'] = $product_property_id;
			$this->ProductPropertiesProduct->create();
		// pokud ano, updatuju stavajici
		} else {
			$save['ProductPropertiesProduct']['id'] = $product_properties_product['ProductPropertiesProduct']['id'];
		}
		
		return $this->ProductPropertiesProduct->save($save);
	}
	
	function is_product_property_editable($id, $product_property_id, $supplier_id) {
		// u produktu syncare nechci updatovat nic
		$product_property = $this->ProductPropertiesProduct->find('first', array(
			'conditions' => array(
				'ProductPropertiesProduct.product_id' => $id,
				'ProductPropertiesProduct.product_property_id' => $product_property_id
			),
			'contain' => array(),
		));
		// defaultne chci updatovat
		$update = true;
		if (!empty($product_property) && isset($product_property['ProductPropertiesProduct']['update'])) {
			$update = $product_property['ProductPropertiesProduct']['update'];
			// pokud neni zadano jinak, u produktu syncare nechci updatovat
		} elseif ($supplier_id == 2 || $supplier_id == 1 || $supplier_id == 3 || $supplier_id == 4 || $supplier_id == 5) {
			$update = false;
		}
		return $update;
	}
	
	function related_products($id, $customer_type_id) {
		$conditions = array('RelatedProduct.product_id' => $id);
		$products = $this->RelatedProduct->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.related_name',
				'Product.url',
				$this->price . ' AS price',
				$this->discount . ' AS discount',
				'Product.retail_price_with_dph',
				'Image.id',
				'Image.name'
			),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = RelatedProduct.related_product_id AND Product.active = 1')
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Product.id = Image.product_id AND Image.is_main = "1"')
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
			'order' => array('RelatedProduct.order' => 'asc')
		));

		foreach ($products as &$product) {
			$product['Product']['price'] = $product[0]['price'];
			$product['Product']['discount'] = $product[0]['discount'];
		}
		
		return $products;
	}
	
	function most_sold_with_products($id, $customer_type_id, $options) {
		$limit = 10;
		if (isset($options['limit'])) {
			$limit = $options['limit'];
		}
		
		$conditions = array('OrderedProduct.product_id' => $id);
		if (isset($options['excluded_ids']) && !empty($options['excluded_ids'])) {
			$conditions[] = 'OtherOrderedProduct.product_id NOT IN (' . implode(',', $options['excluded_ids']) . ')';
		}
		if (isset($options['range'])) {
			$from = date('Y-m-d', strtotime($options['range']));
			$conditions[] = 'DATE(OrderedProduct.created) > "' . $from . '"';
		}

		$products = $this->OrderedProduct->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.related_name',
				'Product.url',
				$this->price . ' AS price',
				$this->discount . ' AS discount',
				'Product.retail_price_with_dph',
				'SUM(OtherOrderedProduct.product_quantity) AS ordered_quantity',
				'Image.id',
				'Image.name'
			),
			'joins' => array(
				array(
					'table' => 'orders',
					'alias' => 'Order',
					'type' => 'INNER',
					'conditions' => array('Order.id = OrderedProduct.order_id')
				),
				array(
					'table' => 'ordered_products',
					'alias' => 'OtherOrderedProduct',
					'type' => 'INNER',
					'conditions' => array('Order.id = OtherOrderedProduct.order_id AND OtherOrderedProduct.product_id != OrderedProduct.product_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'INNER',
					'conditions' => array('Product.id = OtherOrderedProduct.product_id AND Product.active = 1')
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Product.id = Image.product_id AND Image.is_main = "1"')
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
			'group' => array('OtherOrderedProduct.product_id'),
			'order' => array('ordered_quantity' => 'desc'),
			'limit' => $limit
		));
		foreach ($products as &$product) {
			$product['Product']['price'] = $product[0]['price'];
			$product['Product']['discount'] = $product[0]['discount'];
		}

		return $products;
	}
	
	// z produktu odpovidajicich podminkam, vybere nejlevnejsi
	function cheapest($conditions, $customer_type_id) {
		$joins = array(
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
				'conditions' => array('Category.id = CategoriesProduct.category_id')
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
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'INNER',
				'conditions' => array('Availability.id = Product.availability_id')
			),
			array(
				'table' => 'manufacturers',
				'alias' => 'Manufacturer',
				'type' => 'LEFT',
				'conditions' => array('Manufacturer.id = Product.manufacturer_id')
			)
		);

		// vezmu jako cenu produktu obycejnou cenu
		$this->virtualFields['price'] = $this->price;
		$cheapest = $this->find('first', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.price',
				
				'Availability.id',
				'Availability.cart_allowed'

			),
			'joins' => $joins,
			'group' => 'Product.id',
			'order' => array('Product.price' => 'asc')
		));
		unset($this->virtualFields['price']);
		return $cheapest;
	}
	
	// z produktu odpovidajicich podminkam, vybere nejdrazsi
	function most_expensive($conditions, $customer_type_id) {
		// z podminek musim odstranit ty, ktere mi omezuji vyber produktu podle ceny
		
		$joins = array(
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
				'conditions' => array('Category.id = CategoriesProduct.category_id')
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
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'INNER',
				'conditions' => array('Availability.id = Product.availability_id')
			),
			array(
				'table' => 'manufacturers',
				'alias' => 'Manufacturer',
				'type' => 'LEFT',
				'conditions' => array('Manufacturer.id = Product.manufacturer_id')
			)
		);

		// vezmu jako cenu produktu obycejnou cenu
		$this->virtualFields['price'] = $this->price;
		$most_sold = $this->find('first', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array(
				'Product.id',
				'Product.name',
				'Product.price',
				
				'Availability.id',
				'Availability.cart_allowed'

			),
			'joins' => $joins,
			'group' => 'Product.id',
			'order' => array('Product.price' => 'desc')
		));
		unset($this->virtualFields['price']);
		return $most_sold;
	}
	
	function generateFreeShipping($data) {
		$res = array();
		if (array_key_exists('free_shipping_quantity', $data['Product'])) {
			// doprava zdarma se definuje pouze pro dopravce geis s platbou predem
			//  -- GEIS balik platbu predem - ID 32
			//  -- GEIS POINT s platbou predem - ID 35
			$shipping_ids = array(32, 35);
			$quantity =  $data['Product']['free_shipping_quantity'];
			if ($quantity == '') {
				$quantity = 0;
			}
			if ($quantity > 0) {
				foreach ($shipping_ids as $shipping_id) {
					$res_item = array(
						'shipping_id' => $shipping_id,
						'quantity' => $quantity
					);
					if (isset($data['Product']['id'])) {
						$res_item['product_id'] = $data['Product']['id'];
					}
					
					$res[] = $res_item; 
				}
			}
		}
		return $res;
	}
	
	function minQuantityFreeShipping($id) {
		$free_shipping_products = $this->FreeShippingProduct->find('all', array(
			'conditions' => array('FreeShippingProduct.product_id' => $id),
			'contain' => array(),
		));

		$min = null;
		foreach ($free_shipping_products as $free_shipping_product) {
			if (!$min || $free_shipping_product['FreeShippingProduct']['quantity'] < $min) {
				$min = $free_shipping_product['FreeShippingProduct']['quantity'];
			}
		}
		
		return $min;
	}
	
	/*
	 * IDcka produktu, ktere se budou vykreslovat na hlavni strance
	 * vyber na zaklade definice uzivatelem + customizace na zaklade pouzivani webu zakaznikem
	 */
	function homepageProductIds($customerTypeId = 2) {
		$this->virtualFields['price'] = $this->price;
		$defLimit = 15;
		
		$productIds = array();
		
		$limit = $defLimit;
		$commonConditions = array(
			'Product.active' => true,
			'Category.active' => true,
			'Product.price >' => 0,
			'Availability.cart_allowed' => true
		);
		
		$commonJoins = array(
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
				'conditions' => array('Category.id = CategoriesProduct.category_id')
			),
			array(
				'table' => 'customer_type_product_prices',
				'alias' => 'CustomerTypeProductPrice',
				'type' => 'LEFT',
				'conditions' => array('Product.id = CustomerTypeProductPrice.product_id AND CustomerTypeProductPrice.customer_type_id = ' . $customerTypeId)
			),
			array(
				'table' => 'customer_type_product_prices',
				'alias' => 'CustomerTypeProductPriceCommon',
				'type' => 'LEFT',
				'conditions' => array('Product.id = CustomerTypeProductPriceCommon.product_id AND CustomerTypeProductPriceCommon.customer_type_id = 2')
			),
			array(
				'table' => 'availabilities',
				'alias' => 'Availability',
				'type' => 'INNER',
				'conditions' => array('Availability.id = Product.availability_id')
			),
		);
		
		$commonContain = array();
		$commonFields = array('DISTINCT Product.id');
		
		// nejdriv tam dam produkty, ktere mam definovane v administraci
		$definedJoins = array(
			array(
				'table' => 'most_sold_products',
				'alias' => 'MostSoldProduct',
				'type' => 'INNER',
				'conditions' => array('Product.id = MostSoldProduct.product_id')
			),
		);
		$definedJoins = array_merge($commonJoins, $definedJoins);
		
		$definedProductIds = $this->find('all', array(
			'conditions' => $commonConditions,
			'contain' => $commonContain,
			'fields' => $commonFields,
			'joins' => $definedJoins,
			'order' => array('MostSoldProduct.order' => 'asc'),
			'limit' => $limit
		));

		$definedProductIds = Set::extract('/Product/id', $definedProductIds);
		$productIds = array_merge($productIds, $definedProductIds);

		// pak tam dam produkty, ktere zakaznik koupil
		//$limit -= count($productIds);
		
		// pak tam dam produkty, ktere zakaznik navstivil
		//$limit -= count($productIds);
		
		// pak tam dam nejvice prodavane produkty, ktere stoji alespon 300
		$limit -= count($productIds);
		if ($limit > 0) {

			$mostSoldIdsHlp = $this->OrderedProduct->find('all', array(
				// doplnit omezeni na datum?
				'conditions' => array(),
				'contain' => array(),
				'fields' => array('OrderedProduct.product_id'),
				'order' => array('SUM(OrderedProduct.product_quantity)' => 'desc'),
				'group' => 'OrderedProduct.product_id',
				'limit' => 100
			));
	
			if (!empty($mostSoldIdsHlp)) {
				$mostSoldIdsHlp = Set::extract('/OrderedProduct/product_id', $mostSoldIdsHlp);
				
				// vyberu jen nejprodavanejsi produkty podle idcek, ale s cenou vyssi nez dana mez
				$mostSoldConditions = array(
					'Product.id IN (' . implode(',', $mostSoldIdsHlp) . ')',
					'Product.price >=' => 300
				);
				// a nechci tam ty, ktere uz mam vybrane
				$excludeChosenConditions = array();
				if (!empty($productIds)) {
					$excludeChosenConditions[] = 'Product.id NOT IN (' . implode(',', $productIds) . ')';
				}

				$mostSoldConditions = array_merge($commonConditions, $excludeChosenConditions, $mostSoldConditions);
				$mostSoldOrder = array('FIELD(Product.id, ' . implode(',', $mostSoldIdsHlp) . ' )');

				$mostSoldIds = $this->find('all', array(
					'conditions' => $mostSoldConditions,
					'contain' => $commonContain,
					'joins' => $commonJoins,
					'fields' => $commonFields,
					'order' => $mostSoldOrder,
					'limit' => $limit,
				));
				$mostSoldIds = Set::extract('/Product/id', $mostSoldIds);
				$productIds = array_merge($productIds, $mostSoldIds);
			}
		}
		
		unset($this->virtualFields['price']);

		return $productIds;
	}
	
	/*
	 * vybere mnozinu $limit produktu, ktere jsou nejprodavanejsi spolu s mnozinou produktu, definovanych podle IDcek
	 */
	function similarProductIds($productIds, $customerTypeId, $limit) {
		$range = '-2 months';
		$options = array(
			'excluded_ids' => $productIds,
			'range' => $range,
			'limit' => $limit
		);
		
		$products = array(
			'ids' => array(),
			'values' => array()
		);
		foreach ($productIds as $id) {
			$mostSoldProducts = $this->most_sold_with_products($id, $customerTypeId, $options);
			foreach ($mostSoldProducts as $mostSoldProduct) {
				$mostSoldProduct['Product']['ordered_quantity'] = $mostSoldProduct[0]['ordered_quantity'];
				if (in_array($mostSoldProduct['Product']['id'], $products['ids'])) {
					$key = array_search($mostSoldProduct['Product']['id'], $products['ids']);
					$products['values'][$key]['Product']['ordered_quantity'] += $mostSoldProduct['Product']['ordered_quantity'];
				} else {
					$products['ids'][] = $mostSoldProduct['Product']['id'];
					$products['values'][] = $mostSoldProduct;
				}
			}
		}
		// vyfiltruju produkty, ktere byly objenany mene nez je zadana mez
		$products = $this->filterMinQuantity($products['values']);
		$products = array_slice($products, 0, $limit);
		$productIds = Set::extract('/Product/id', $products);
		return $productIds;
	}
	
	// ziska z pole pouze produkty, ktere maji nastaveny dany atribut na vice nez je pozadavek 
	function filterMinQuantity($products, $fieldName = 'ordered_quantity', $minQuantity = 2) {
		$res = array();
		foreach ($products as $product) {
			if ($product['Product'][$fieldName] >= $minQuantity) {
				$res[] = $product;
			}
		}
		return $res;
	}
}
?>
