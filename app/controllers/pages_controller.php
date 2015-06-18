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
		$this->layout = REDESIGN_PATH . 'homepage';
		$title = 'Online lékárna Brno střed';
		$description = 'Internetová lékárna přímo v centru Brna. Odborně vyškolený personál a široká nabídka produktů. Léky si můžete osobně vyzvednout ve středu Brna.';
		
		$this->set('_title', $title);
		$this->set('_description', $description);
		
		App::import('Model', 'CustomerType');
		$this->CustomerType = new CustomerType;
		$customer_type_id = $this->CustomerType->get_id($this->Session->read());
		
		App::import('Model', 'MostSoldProduct');
		$this->MostSoldProduct = new MostSoldProduct;
		$hp_most_sold = $this->MostSoldProduct->hp_list($customer_type_id);
		$this->set('hp_most_sold', $hp_most_sold);

/*		App::import('Model', 'News');
		$this->News = new News;
		$hp_news = $this->News->hp_list();
		$this->set('hp_news', $hp_news);
		
		App::import('Model', 'DiscountedProduct');
		$this->DiscountedProduct = new DiscountedProduct;
		$hp_discounted = $this->DiscountedProduct->hp_list($customer_type_id);
		$this->set('hp_discounted', $hp_discounted);
		
		App::import('Model', 'RecommendedProduct');
		$this->RecommendedProduct = new RecommendedProduct;
		$hp_recommended = $this->RecommendedProduct->hp_list($customer_type_id);
		$this->set('hp_recommended', $hp_recommended);		

*/
	}
}
?>
