<?php
/* SVN FILE: $Id: pages_controller.php 5847 2007-10-22 03:39:01Z phpnut $ */
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.controller
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 5847 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-10-21 22:39:01 -0500 (Sun, 21 Oct 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller
 */
class PagesController extends AppController{
/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'Pages';
/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', 'Javascript');
/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array();
/**
 * Displays a view
 *
 * @param mixed What page to display
 * @access public
 */
	function display() {
		$this->layout = REDESIGN_PATH . 'homepage';

		if (!func_num_args()) {
			$this->redirect('/');
		}
		$path = func_get_args();

		if (!count($path)) {
			$this->redirect('/');
		}
		$count = count($path);
		$page = null;
		$subpage = null;
		$title = null;
		$description = null;
		$keywords = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title = $_description = Inflector::humanize($path[$count - 1]);
		}
		$this->set('page', $page);
		$this->set('subpage', $subpage);
		$this->set('_title', $title);
		$this->set('_description', $description);
		$this->set('_keywords', $keywords);
		
		$this->render(join('/', $path));
	}
	
	function home() {
		$params = array();
		if (isset($this->params['url']['filter'])) {
			$params = $this->params['url']['filter']; 
		}

		// odnastavuju nektery z filtru
		if (isset($_GET['filter']['reset_filter'])) {
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
		}
		
		 App::import('Model', 'HomepageBanner');
		 $this->Page->HomepageBanner = &new HomepageBanner;
		 $banner_image = $this->Page->HomepageBanner->getImage();
		 if ($this->Page->HomepageBanner->isActive() && $banner_image) {
		 	$banner_url = $this->Page->HomepageBanner->getUrl();
		 	$this->set(compact('banner_image', 'banner_url'));
		 }
		
		App::import('Model', 'Product');
		$this->Page->Product = &new Product;
		$customer_type_id = $this->Page->Product->CustomerTypeProductPrice->CustomerType->get_id($this->Session->read());
				
		// potrebuju vybrat produkty pres paginate, aby mi fungovalo strankovani
		// produkty budu vybirat na zaklade nekolika kriterii (customizovat vyber - nakoupil, navstivil, je navoleno atd.)
		// nejdriv vyberu idecka produktu a pak k nim dotahnu potrebne info
		$productIds = $this->Page->Product->homepageProductIds($customer_type_id);
		$products = array();
		if (!empty($productIds)) {
			$conditions = array('Product.id' => $productIds);
			
			// DO TETO CHVILE MAM PODMINKY PRO VYBRANI PRODUKTU NEOMEZUJICI PODLE FILTRU
			$filter_manufacturers_order = array('Manufacturer.name' => 'asc');
			$filter_manufacturers_conditions = array_merge($conditions, array('Manufacturer.active' => true));
			$filter_manufacturers = $this->Page->Product->Manufacturer->filter_manufacturers($filter_manufacturers_conditions, $filter_manufacturers_order);
			$this->set('filter_manufacturers', $filter_manufacturers);
			// musim tedy vybrat nejlevnejsi a nejdrazsi produkt v podle dosavadnich podminek, protoze jakmile tam prihodim podminky o cene, zkresli mi to hodnoty pro slider
			// nejlevnejsi a nejdrazsi produkt pro ucely filtru podle ceny
			$cheapest_product = $this->Page->Product->cheapest($conditions, $customer_type_id);
			$cheapest_product_price = 0;
			if (!empty($cheapest_product)) {
				$cheapest_product_price = $cheapest_product['Product']['price'];
			}
			$this->set('cheapest_price', $cheapest_product_price);
			
			$most_expensive_product = $this->Page->Product->most_expensive($conditions, $customer_type_id);
			$most_expensive_product_price = 1000;
			if (!empty($most_expensive_product)) {
				$most_expensive_product_price = $most_expensive_product['Product']['price'];
			}
			$this->set('most_expensive_price', $most_expensive_product_price);
			
			// pokud mam nastavene filtry jako defaultne, presmeruju, at se mi neduplikuje hp
			if (!isset($params['manufacturer_id']) && (!isset($params['sorting']) || $params['sorting'][0] == 0) && (!isset($params['price']) || (isset($params['price']['min']) && $params['price']['min'] == $cheapest_product_price && isset($params['price']['max']) && $params['price']['max'] == $most_expensive_product_price)) && $_SERVER['REQUEST_URI'] != '/') {
				$this->redirect('/');
			}
			
			// pokud jsem zrusil vyber z nektereho z filtru, presmeruju na novou url
			if (isset($params['reset_filter']) && $params['reset_filter']) {
				$url = '/';
				unset($params['reset_filter']);
				$params = array('filter' => $params);
				$params = http_build_query($params);
				$url .= '?' . $params;					
				$this->redirect($url);
			}

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
			}
			
			$joins = array(
				array(
					'table' => 'ordered_products',
					'alias' => 'OrderedProduct',
					'type' => 'LEFT',
					'conditions' => array('OrderedProduct.product_id = Product.id')
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
					'table' => 'manufacturers',
					'alias' => 'Manufacturer',
					'type' => 'LEFT',
					'conditions' => array('Manufacturer.id = Product.manufacturer_id')
				),
				array(
					'table' => 'most_sold_products',
					'alias' => 'MostSoldProduct',
					'type' => 'LEFT',
					'conditions' => array('Product.id = MostSoldProduct.product_id')
				),
			);

			$fields = array(
				'Product.id',
				'Product.name',
				'Product.url',
				'Product.short_description',
				'Product.retail_price_with_dph',
				'Product.discount_common',
				'Product.sold',
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
			);
			
			// sestavim podminku pro razeni podle toho, co je vybrano
			$order = array();
			$extended_order = array(
				'ISNULL(MostSoldProduct.id)' => 'ASC',
				'MostSoldProduct.order' => 'ASC'
			);
			$order = array_merge($order, array_merge($extended_order, $this->Page->Product->sorting_options[0]['conditions']));
			if (isset($_GET['filter']['sorting']) && !empty($_GET['filter']['sorting'])) {
				if ($_GET['filter']['sorting'][0] != 0) {
					$order = $this->Page->Product->sorting_options[$_GET['filter']['sorting'][0]]['conditions'];
				}
			} else {
				$_GET['filter']['sorting'][0] = 0;
			}
			
			$this->Page->Product->virtualFields['sold'] = 'SUM(OrderedProduct.product_quantity)';
			$this->Page->Product->virtualFields['price'] = $this->Page->Product->price;
			$this->Page->Product->virtualFields['discount'] = $this->Page->Product->discount;
			
			$this->paginate['Product'] = array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => $fields,
				'joins' => $joins,
				'group' => 'Product.id',
				'order' => $order
			);
			
			$products = $this->paginate('Product');

			foreach ($products as &$product) {
				$product['Product']['free_shipping_min_quantity'] = $this->Page->Product->minQuantityFreeShipping($product['Product']['id']);
			}
			
			unset($this->Page->Product->virtualFields['sold']);
			unset($this->Page->Product->virtualFields['price']);
			unset($this->Page->Product->virtualFields['discount']);
			
		}
		
		$this->set('products', $products);
		
		$this->set('sorting_options', $this->Page->Product->sorting_options);
		
		$this->layout = REDESIGN_PATH . 'content';
		$title = 'Online lékárna Brno střed';
		$description = 'Internetová lékárna přímo v centru Brna. Odborně vyškolený personál a široká nabídka produktů. Léky si můžete osobně vyzvednout ve středu Brna.';
		
		$this->set('_title', $title);
		$this->set('_description', $description);
		$this->set('listing_style', 'products_listing_grid');
	}
}
?>
