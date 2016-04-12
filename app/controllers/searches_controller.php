<?php
class SearchesController extends AppController {

	var $name = 'Searches';

	/**
	 * Vyhledavani produktu v administraci.
	 *
	 */
	function admin_do(){
		if ( isset($this->data) ){
			$this->data['Search']['query'] = trim($this->data['Search']['query']);
			
			App::import('Model', 'Product');
			$this->Product = &new Product;
			
			// vysledky s celym retezcem
			$products = $this->Product->find('all', array(
				'conditions' => array(
					'OR' => array(
						array("Product.name LIKE '%%" . $this->data['Search']['query'] . "%%'"),
						array('Product.id' => $this->data['Search']['query'])
					)
				),
				'contain' => array('CategoriesProduct')
			));

			// vysledky s rozsekanym retezcem
			$split_query = explode(" ", $this->data['Search']['query']);
			$count_split = count($split_query);
			
			if ( $count_split > 1 ){
				$not_ids = array();
				for ( $i = 0; $i < count($products); $i++ ){
					$not_ids[] = $products[$i]['Product']['id'];
				}
				
				$split_conditions = array();
				for ( $i = 0; $i < $count_split; $i++ ){
					$split_conditions[] = "Product.name LIKE '%%" . $split_query[$i] . "%%'";
				}

				// vysledky s rozsekanym retezcem
				$products2 = $this->Product->find('all', array(
					'conditions' => array('AND' => $split_conditions, 'NOT' => array('Product.id' => $not_ids))
				));
				$products = am($products, $products2);
			}
			
			for ( $i = 0; $i < count($products); $i++ ){
				for ( $j = 0; $j < count($products[$i]['CategoriesProduct']); $j++ ){
					$products[$i]['CategoriesProduct'][$j]['path'] = $this->Product->CategoriesProduct->Category->getpath($products[$i]['CategoriesProduct'][$j]['category_id']);
				}
			}

			$this->set('products', $products);
		}
	}
	
	function index($query = null, $start = 0){
		// layout
		$this->layout = 'default_front_end';
		
		// nastavim nadpis
		$this->set('page_heading', 'Vyhledávání');

		if ( !empty($query) ){
			$XML = $this->Search->doSearch($query, $start);
			$this->set('xml', $XML);
		}
	}
	
	function parsequery(){
		$target = array('controller' => 'searches', 'action' => 'index');
		if ( isset($this->data) && !empty($this->data['Search']['q']) ){
			$target[0] = urlencode($this->data['Search']['q']);
			$target[1] = '0';
		}
		$this->redirect($target, null, true);
	}

	/**
	 * Vyhledavani produktu v obchode.
	 *
	 * @param string $id
	 */
	function do_search() {
		if (isset($_GET['filter']['reset_filter'])) {
			$url = $this->params['url']['url'];
			$params = $this->params['url']['filter'];
			switch ($_GET['filter']['reset_filter']) {
				case 'brand':
					unset($params['manufacturer_id']);
					break;
				case 'price':
					unset($params['price']);
					break;
				case 'sorting':
					unset($params['sorting']);
					break;
			}
			unset($params['reset_filter']);
			$params = array('filter' => $params);
			$params = http_build_query($params);
			$url = '/' . $url . '?' . $params;
			$this->redirect($url);
		}
		
		$this->Search->Product = ClassRegistry::init('Product');
		
		$this->layout = REDESIGN_PATH . 'content';
		$this->set('_title', 'Vyhledávání produktů');
		$this->set('_description', 'Vyhledávač produktů v obchodě ' . CUST_NAME);
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Vyhledávání produktů', 'href' => '/' . $this->params['url']['url'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		$products = array();
		$customer_type_id = 2;
		
		$query = '';
		if (!empty($_GET) && isset($_GET['q']) && !empty($_GET['q'])) {
			$query = $_GET['q'];
		} elseif (!empty($_GET) && isset($_GET['filter']['query']) && !empty($_GET['filter']['query'])) {
			$query = $_GET['filter']['query'];
		}
		
		$this->set('query', $query);
		$this->data['Search']['q'] = $query;

		if (!empty($this->data) && isset($this->data['Search']['q'])){
			// hledany vyraz musim ocistit
			// od mezer na zacatku a konci celeho vyrazu
			$queries = trim($this->data['Search']['q']);
			
			// od vice mezer za sebou
			while (eregi("  ", $queries)) {
				$queries = str_replace("  ", " ", $queries);
			}
			
			// zjistim jestli se nejedna o viceslovny nazev produktu
			$queries = explode(" ", $queries);
			
			$or = array();
			foreach ( $queries as $key => $value ){
				$or[] = array(
					'OR' => array(
						'Product.id' => $value,
						"Product.name LIKE '%%" . $value . "%%'",
						"Product.title LIKE '%%" . $value . "%%'",
						"Product.heading LIKE '%%" . $value . "%%'",
						"Product.related_name LIKE '%%" . $value . "%%'",
						"Product.zbozi_name LIKE '%%" . $value . "%%'",
						"Product.short_description LIKE '%%" . $value . "%%'",
						"Product.description LIKE '%%" . $value . "%%'",
						"Product.alliance_description LIKE '%%" . $value . "%%'",
						"Manufacturer.name  LIKE '%%" . $value . "%%'",
					)
				);
			}
			
			$conditions = array(
				'AND' => array(
					// podminka z formu pro vyhledavani
					$or,
					// chci jen aktivni produkty
					'Product.active' => true,
					'Category.active' => true
				)
			);
			
			App::import('Model', 'CustomerType');
			$this->CustomerType = new CustomerType;
			$customer_type_id = $this->CustomerType->get_id($this->Session->read());
			
			// DO TETO CHVILE MAM PODMINKY PRO VYBRANI PRODUKTU NEOMEZUJICI PODLE FILTRU
			$filter_manufacturers_order = array('Manufacturer.name' => 'asc');
			$filter_manufacturers_conditions = array_merge($conditions, array('Manufacturer.active' => true));
			$filter_manufacturers = $this->Search->Product->Manufacturer->filter_manufacturers($filter_manufacturers_conditions, $filter_manufacturers_order);
			$this->set('filter_manufacturers', $filter_manufacturers);
			// musim tedy vybrat nejlevnejsi a nejdrazsi produkt v podle dosavadnich podminek, protoze jakmile tam prihodim podminky o cene, zkresli mi to hodnoty pro slider
			// nejlevnejsi a nejdrazsi produkt pro ucely filtru podle ceny
			$cheapest_product = $this->Search->Product->cheapest($conditions, $customer_type_id);
			$cheapest_product_price = 0;
			if (!empty($cheapest_product)) {
				$cheapest_product_price = $cheapest_product['Product']['price'];
			}
			$this->set('cheapest_price', $cheapest_product_price);
			
			$most_expensive_product = $this->Search->Product->most_expensive($conditions, $customer_type_id);
			$most_expensive_product_price = 1000;
			if (!empty($most_expensive_product)) {
				$most_expensive_product_price = $most_expensive_product['Product']['price'];
			}
			$this->set('most_expensive_price', $most_expensive_product_price);
			
			if (isset($_GET['filter']['price']['min']) && !empty($_GET['filter']['price']['min'])) {
				$conditions['Product.price >='] = $_GET['filter']['price']['min'];
			}
			if (isset($_GET['filter']['price']['max']) && !empty($_GET['filter']['price']['max'])) {
				$conditions['Product.price <='] = $_GET['filter']['price']['max'];
			}
			
			// filtr podle vyrobcu
			if (isset($_GET['filter']['manufacturer_id']) && !empty($_GET['filter']['manufacturer_id'])) {
				$manufacturer_id_arr = $_GET['filter']['manufacturer_id'];
				$conditions = array_merge($conditions, array('Product.manufacturer_id' => $manufacturer_id_arr));
				// $this->data['CategoriesProduct']['manufacturer_id'] = $manufacturer_id;
			}
			
			$joins = array(
				array(
					'table' => 'ordered_products',
					'alias' => 'OrderedProduct',
					'type' => 'LEFT',
					'conditions' => array('OrderedProduct.product_id = Product.id')
				),
 				array(
					'table' => 'categories_products',
					'alias' => 'CategoriesProduct',
					'type' => 'INNER',
					'conditions' => array('Product.id = CategoriesProduct.product_id')
				),
				array(
					'table' => 'categories',
					'alias' => 'Category',
					'type' => 'INNER',
					'conditions' => array('Category.id = CategoriesProduct.category_id')
				),
				array(
					'table' => 'images',
					'alias' => 'Image',
					'type' => 'LEFT',
					'conditions' => array('Image.product_id = Product.id AND Image.is_main = "1"')
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

			$this->paginate['Product'] = array(
				'conditions' => $conditions,
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
					'Product.rate',
					'Product.is_akce',
					'Product.is_novinka',
					'Product.is_doprodej',
					'Product.is_bestseller',
					'Product.is_darek_zdarma',
						
					'Image.id',
					'Image.name',
						
					'Availability.id',
					'Availability.cart_allowed'
				),
				'joins' => $joins,
				'group' => 'Product.id',
				'limit' => 15
			);
			
			// sestavim podminku pro razeni podle toho, co je vybrano
			$order = array('Availability.cart_allowed' => 'desc');
			if (isset($_GET['filter']['sorting']) && !empty($_GET['filter']['sorting'])) {
				$order = array_merge($order, $this->Search->Product->sorting_options[$_GET['filter']['sorting'][0]]['conditions']);
			} else {
				$order = array_merge($order, array('Product.is_akce' => 'desc', 'Product.priority' => 'asc', 'Product.sold' => 'desc', 'Product.price' => 'asc'));
				$_GET['filter']['sorting'][0] = 0;
			}
			
			$this->paginate['Product']['order'] = $order;
	
			$this->Search->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
			// cenu produktu urcim jako cenu podle typu zakaznika, pokud je nastavena, pokud neni nastavena cena podle typu zakaznika, vezmu za cenu beznou slevu, pokud ani ta neni nastavena
			// vezmu jako cenu produktu obycejnou cenu
			$this->Search->Product->virtualFields['price'] = $this->Search->Product->price;
			// sleva
			$this->Search->Product->virtualFields['discount'] = $this->Search->Product->discount;

			$products = $this->paginate('Product');
			foreach ($products as &$product) {
				$product['Product']['free_shipping_min_quantity'] = $this->Search->Product->minQuantityFreeShipping($product['Product']['id']);
			}

			// opetovne vypnuti virtualnich poli, nastavenych za behu
			unset($this->Search->Product->virtualFields['sold']);
			unset($this->Search->Product->virtualFields['price']);
			unset($this->Search->Product->virtualFields['discount']);
		}
		$this->set('products', $products);
		
		$this->set('sorting_options', $this->Search->Product->sorting_options);
		
		$this->set('listing_style', 'products_listing_grid');
	}
}
?>
