<?php
class ProductPropertiesProductsController extends AppController {
	var $name = 'ProductPropertiesProducts';
	
	/*
	 * Slouzi k inicializaci nastaveni, jestli chceme updatovat vlastnosti produktu daty z feedu
	 * Touto metodou nastavim, ze u produktu Syncare nechci updatovat nikde nic
	 */
	function admin_init() {
		$conditions = array('Product.supplier_id' => 3); // Boneco - updatovat vse krom popisu
		
		// chci nastavit, ze chci updatovat active a dostupnost
		$products = $this->ProductPropertiesProduct->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Product.id')
		));

		$productIds = Set::extract('/Product/id', $products);
		// smazu dosavadni nastaveni
		$this->ProductPropertiesProduct->deleteAll(array('product_id' => $productIds));

		$properties = $this->ProductPropertiesProduct->ProductProperty->find('all', array(
			'contain' => array(),
			'fields' => array('ProductProperty.id')
		));
		
		$save = array();
		
		foreach ($products as $product) {
			foreach ($properties as $property) {
				// updatuju jen ceny (11), obrazky (16) a active (18)
				$toUpdate = array(11, 16, 18);
				$update = false;
				if (in_array($property['ProductProperty']['id'], $toUpdate)) {
					$update = true;
				}
				$save[] = array(
					'product_id' => $product['Product']['id'],
					'product_property_id' => $property['ProductProperty']['id'],
					'update' => $update
				);
			}
		}

		$this->ProductPropertiesProduct->saveAll($save);
		
		die('hotovo');
	}
}
