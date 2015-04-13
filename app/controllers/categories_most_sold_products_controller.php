<?php 
class CategoriesMostSoldProductsController extends AppController {
	var $name = 'CategoriesMostSoldProducts';
	
	function generate() {
		// vyprazdnim tabulku
		if (!$this->CategoriesMostSoldProduct->truncate()) {
			die('nepodarilo se vyprazdnit tabulky categories_most_sold_products');
		}
	
		// nactu seznam kategorii
		$categories = $this->CategoriesMostSoldProduct->Category->find('all', array(
			'conditions' => array(
				'NOT' => array('Category.id' => $this->CategoriesMostSoldProduct->Category->unactive_categories_ids)
			),
			'contain' => array(),
			'fields' => array('id', 'name')
		));
	
		// nastavim virtualni pole pro sumu prodanych produktu
		$this->CategoriesMostSoldProduct->Category->CategoriesProduct->virtualFields = array('quantity' => 'SUM(OrderedProduct.product_quantity)');
	
		// pro kazdou kategorii:
		foreach ($categories as $category) {
			if (!$this->CategoriesMostSoldProduct->Category->set_most_sold($category['Category']['id'])) {
				die('ulozeni nejprodavanejsich produktu v kategorii ' . $category['Category']['name'] . ' se nezdarilo');
			}
		}
		die();
	}
}
?>
