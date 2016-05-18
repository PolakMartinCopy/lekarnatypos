<?php
class ProductPropertiesProductsController extends AppController {
	var $name = 'ProductPropertiesProducts';
	
	/*
	 * Slouzi k inicializaci nastaveni, jestli chceme updatovat vlastnosti produktu daty z feedu
	 * Touto metodou nastavim, ze u produktu Syncare nechci updatovat nikde nic
	 */
	function admin_init() {
		$conditions = array('Product.supplier_id' => array(4, 5)); // Alliance - updatovat sukl a pdk
		
		// chci nastavit, ze chci updatovat active a dostupnost
		$products = $this->ProductPropertiesProduct->Product->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Product.id')
		));

		$productIds = Set::extract('/Product/id', $products);

		$properties = $this->ProductPropertiesProduct->ProductProperty->find('all', array(
			'conditions' => array(
				'OR' => array(
					array('ProductProperty.name' => 'Product.sukl'),
					array('ProductProperty.name' => 'Product.pdk_code')
				)
			),
			'contain' => array(),
			'fields' => array('ProductProperty.id')
		));

		$save = array();
		
		foreach ($products as $product) {
			foreach ($properties as $property) {
				$update = true;
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
