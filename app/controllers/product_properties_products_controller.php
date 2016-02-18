<?php
class ProductPropertiesProductsController extends AppController {
	var $name = 'ProductPropertiesProducts';
	
	/*
	 * Slouzi k inicializaci nastaveni, jestli chceme updatovat vlastnosti produktu daty z feedu
	 * Touto metodou nastavim, ze u produktu Syncare nechci updatovat nikde nic
	 */
	function admin_init() {
		$conditions = array('Product.supplier_id' => array(1)); // Syncare a Topvet
		
		// chci nastavit, ze chci updatovat active a dostupnost
		$products = $this->ProductPropertiesProduct->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Product.id')
		));
		
		// active
		// $property_id = 18;
		//$availability;
		$property_id = 13;
		
		$save = array();
		
		foreach ($products as $product) {
			$property = $this->ProductPropertiesProduct->find('first', array(
				'conditions' => array(
					'ProductPropertiesProduct.product_id' => $product['Product']['id'],
					'ProductPropertiesProduct.product_property_id' => $property_id,
					'ProductPropertiesProduct.update' => false
				),
				'contain' => array(),
				'fields' => array('ProductPropertiesProduct.id')
			));
			if (empty($property)) {
				$product_property = array(
					'product_id' => $product['Product']['id'],
					'product_property_id' => $property_id,
				);
			} else {
				$product_property = array(
					'id' => $property['ProductPropertiesProduct']['id'],
				);
			}
			$product_property['update'] = true;
			$save[] = $product_property;
		}
		
//		debug($save); die();

		$this->ProductPropertiesProduct->saveAll($save);
		
		die('hotovo');
	}
}
