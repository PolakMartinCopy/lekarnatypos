<?php
class ProductPropertiesProductsController extends AppController {
	var $name = 'ProductPropertiesProducts';
	
	/*
	 * Slouzi k inicializaci nastaveni, jestli chceme updatovat vlastnosti produktu daty z feedu
	 * Touto metodou nastavim, ze u produktu Syncare nechci updatovat nikde nic
	 */
	function admin_init() {
		$conditions = array('Product.supplier_id' => array(1, 2)); // Syncare a Topvet
		
		// pro kazdy z vybranych produktu nastavim u vsech vlasnosti, ze ji nechci updatovat
		$products = $this->ProductPropertiesProduct->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Product.id')
		));
		$product_ids = Set::extract('/Product/id', $products);
		// pokud mam neco pro dane produkty nadefinovane, tak to smazu
		$this->ProductPropertiesProduct->deleteAll(array(
			'ProductPropertiesProduct.product_id' =>  $product_ids 	
		)); 
		
		// natahnu si vsechny sledovane vlastnosti
		$properties = $this->ProductPropertiesProduct->ProductProperty->find('all', array(
			'contain' => array(),
			'fields' => array('ProductProperty.id')	
		));
		
		// vygeneruju si pole pro ulozeni, ze u zadneho z vybranych produktu nechci nic updatovat feedem
		$properties_save = array();
		foreach ($products as $product) {
			foreach ($properties as $property) {
				if (!$this->ProductPropertiesProduct->hasAny(array(
					'product_id' => $product['Product']['id'],
					'product_property_id' => $property['ProductProperty']['id']
				))) {
					$properties_save[] = array(
						'product_id' => $product['Product']['id'],
						'product_property_id' => $property['ProductProperty']['id'],
						'update' => false
					);
				}
			}
		}

		$this->ProductPropertiesProduct->saveAll($properties_save);
		
		die('hotovo');
	}
}
