<?php
class Supplier extends AppModel {
	var $name = 'Supplier';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'SupplierCategory',
		'Product'
	);
	
	var $belongsTo = array(
		'Category'
	);
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Zadejte název'
		),
		'url' => array(
			'rule' => array('url', true),
			'message' => 'Zadejte URL xml feedu'
		),
		'price_field' => array(
			'rule' => 'notEmpty',
			'message' => 'Zadejte název tagu, který reprezentuje cenu v XML feedu'
		)
	);
	
	function beforeValidate() {
		// pokusim se opravit spatne zadanou url
		if (array_key_exists(('url'), $this->data['Supplier']) && preg_match('/^www./', $this->data['Supplier']['url'])) {
			$this->data['Supplier']['url'] = 'http://' . $this->data['Supplier']['url'];
		}
		
		return true;
	}
	
	function delete($id = null) {
		if (!isset($id)) {
			return null;
		}
		if ($this->hasAny(array('id' => $id))) {
			$save = array(
				'Supplier' => array(
					'id' => $id,
					'active' => false
				)
			);
			
			return $this->save($save);
		}
		return false;
	}
	
	/**
	 * Otestuje, jestli zadane xml je ve spravnem tvaru pro import do naseho shopu (jestli produkt obsahuje vsechna potrebna data)
	 * podle prvniho produktu ve feedu
	 * @param SimpleXMLElement $simple_xml_element
	 */
	function validate_feed($simple_xml_element, $price_field) {
		$valid = true;
		// povinne atributy elementu XML zdroje
		$required = array(0 => 'ID', 'PRODUCT', 'DESCRIPTION', 'VAT');
		$required[] = $price_field;
		
		$i = 0;
		while ($valid && $i < count($required)) {
			// zkousim, jestli tam je povinny atribut
			$shop_item = $simple_xml_element->SHOPITEM;
			$valid = $valid && $shop_item->{$required[$i]};
			if (!$valid) {
				debug($required[$i]);
			}
			$i++;
		}
		return $valid;
	}
	
	/**
	 * vyparsuje z xml data o produktu k ulozeni
	 * @param SimpleXMLElement $feed_product
	 */
	function product($feed_product, $discount, $price_field, $supplier_id) {
		$product = array();
		// atributy produktu
		try {
			//		- nazev
			$name = $this->product_name($feed_product);
			//		- heading
			$heading = $this->product_heading($feed_product);
			//		- breadcrumb
			$breadcrumb = $this->product_breadcrumb($feed_product);
			//		- related_name
			$related_name = $this->product_related_name($feed_product);
			//		- zbozi_name
			$zbozi_name = $this->product_zbozi_name($feed_product);
			//		- title
			$title = $this->product_title($feed_product);
			//		- short_description
			$short_description = $this->product_short_description($feed_product);
			//		- description
			$description = $this->product_description($feed_product);
			//		- retail_price_with_dph
			$retail_price_with_dph = $this->product_retail_price_with_dph($feed_product, $price_field);
			//		- discount_common
			$discount_common = $this->product_discount_common($feed_product, $price_field, $discount);
			//		- ean
			$ean = $this->product_ean($feed_product);
			//		- supplier product id - id produktu ve feedu dodavatele
			$supplier_product_id = $this->product_supplier_product_id($feed_product);
			//		- supplier_category_id - id kategorie dodavatele u nas v systemu
			$supplier_category_id = $this->product_supplier_category_id($feed_product, $supplier_id);
			// 		- dostupnost
			$availability_id = $this->product_availability_id($feed_product);
			// 		- vyrobce
			$manufacturer_id = $this->product_manufacturer_id($feed_product);
			// 		- danova trida
			$tax_class_id = $this->product_tax_class_id($feed_product);
		} catch (Exception $e) {
			debug($feed_product);
			debug($e->getMessage());
			return false;
		}
		
		$product = array(
			'Product' => array(
				'name' => $name,
				'heading' => $heading,
				'breadcrumb' => $breadcrumb,
				'related_name' => $related_name,
				'zbozi_name' => $zbozi_name,
				'title' => $title,
				'short_description' => $short_description,
				'description' => $description,
				'retail_price_with_dph' => $retail_price_with_dph,
				'discount_common' => $discount_common,
				'ean' => $ean,
				'supplier_product_id' => $supplier_product_id,
				'supplier_category_id' => $supplier_category_id,
				'availability_id' => $availability_id,
				'manufacturer_id' => $manufacturer_id,
				'tax_class_id' => $tax_class_id
			)	
		);
		
		return $product;
	}
	
	function product_name($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function product_heading($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function product_breadcrumb($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function product_related_name($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function product_zbozi_name($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function product_title($feed_product) {
		return $feed_product->PRODUCT->__toString();
	}
	
	function product_short_description($feed_product) {
		$short_description = $feed_product->PRODUCT->__toString();
		$suffix = $feed_product->DESCRIPTION->__toString();
		$suffix = str_replace('<![CDATA[', '', $suffix);
		$suffix = str_replace(']]>', '', $suffix);
		$suffix = trim($suffix);
		if (!empty($suffix)) {
			$short_description = $short_description . ' - ' . $suffix;
		}
		return $short_description;
	}
	
	function product_description($feed_product) {
		$description = $feed_product->HTML_DESCRIPTION->__toString();
		$description = str_replace('<![CDATA[', '', $description);
		$description = str_replace(']]>', '', $description);
		$description = trim($description);
		return $description;
	}
	
	function product_retail_price_with_dph($feed_product, $price_field) {
		return $feed_product->$price_field->__toString();
	}
	
	function product_discount_common($feed_product, $price_field, $discount) {
		$recommended_price = $feed_product->$price_field->__toString();
		$discount_price = round($recommended_price * (1 - ($discount / 100)));
		return $discount_price;
	}
	
	function product_ean($feed_product) {
		return $feed_product->EAN->__toString();
	}
	
	function product_supplier_product_id($feed_product) {
		return $feed_product->PRODUCTNO->__toString();
	}
	
	function product_supplier_category_id($feed_product, $supplier_id) {
		if ($supplier_category_name = $feed_product->CATEGORYTEXT->__toString()) {

			// pokud nemam info o teto kategori v systemu, zalozim zaznam
			$conditions = array(
				'name' => $supplier_category_name,
				'supplier_id' => $supplier_id
			);
			$supplier_category = $this->SupplierCategory->find('first', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array('SupplierCategory.id', 'SupplierCategory.active')					
			));

			if (empty($supplier_category)) {
				$supplier_category['SupplierCategory'] = $conditions;
				$supplier_category['SupplierCategory']['category_id'] = 0;
				$supplier_category['SupplierCategory']['active'] = true;
				$this->SupplierCategory->create();
				if ($this->SupplierCategory->save($supplier_category)) {
					return $this->SupplierCategory->id;
				} else {
					trigger_error('Nepodarilo se ulozit kategorii dodavatele ' . $supplier_category_name . ' pro ucely parovani');
					return false;
				}
			} else {
				if ($supplier_category['SupplierCategory']['active']) {
					return $supplier_category['SupplierCategory']['id'];
				} else {
					return false;
				}
			}
		}
		return false;
	}
	
	function product_availability_id($feed_product) {
		$availability = $feed_product->DELIVERY_DATE->__toString();
		$availability_id = 6;
		// namapuju hodnoty z feedu na idcka dostupnosti u nas v obchode
		if ($availability == 0) {
			$availability_id = 1;
		} elseif ($availability >= 1 && $availability <= 7) {
			$availability_id = 4;
		} elseif ($availability >= 7) {
			$availability_id = 5;
		}
		return $availability_id;
	}
	
	function product_manufacturer_id($feed_product) {
		if ($manufacturer = $feed_product->MANUFACTURER->__toString()) {
			$db_manufacturer = $this->Product->Manufacturer->find('first', array(
				'conditions' => array('Manufacturer.supplier_alias' => $manufacturer),
				'contain' => array(),
				'fields' => array('Manufacturer.id')
			));
			
			if (empty($db_manufacturer)) {
				$save_manufacturer = array(
					'Manufacturer' => array(
						'name' => $manufacturer,
						'supplier_alias'
					)
				);
				$this->Product->Manufacturer->create();
				if ($this->Product->Manufacturer->save($save_manufacturer)) {
					return $this->Product->Manufacturer->id;
				} else {
					throw new Exception('CHYBA PARSOVANI VYROBCE - Nepodařilo se uložit nového výrobce ' . $manufacturer);
					return false;
				}
			} else {
				return $db_manufacturer['Manufacturer']['id'];
			}
		}
		return false;
	}
	
	function product_tax_class_id($feed_product) {
		$tax_class = $feed_product->VAT->__toString();
		if (!$tax_class) {
			throw new Exception('CHYBA PARSOVANI DANOVE TRIDY - Nepodařilo se vyparsovat daňovou třídu');
			return false;
		}
		$tax_class_id = false;
		$db_tax_class = $this->Product->TaxClass->find('first', array(
			'conditions' => array('TaxClass.value' => $tax_class),
			'contain' => array(),
			'fields' => array('TaxClass.id')
		));
		if (empty($db_tax_class)) {
			throw new Exception('CHYBA PARSOVANI DANOVE TRIDY - Nepodařilo se nalézt daňovou třídu s hodnotou ' . $tax_class);
			return false;
		} else {
			return $db_tax_class['TaxClass']['id'];
		}
		return false;
	}
	
	function image_url($feed_product) {
		return $feed_product->IMGURL->__toString();
	}
	
	function image_save($product_id, $image_url) {
		if ($image_url) {
			$db_image = $this->Product->Image->find('first', array(
				'conditions' => array(
					'Image.product_id' => $product_id,
					'Image.supplier_url' => $image_url
				)
			));
			// v systemu obrazek z dane url pro dany produkt nemam 
			if (empty($db_image)) {
				// nahraju obrazek
				$image_name = $this->Product->image_name($product_id);
				$save_image = array(
					'Image' => array(
						'name' => $image_name,
						'product_id' => $product_id,
						'is_main' => true,
						'supplier_url' => $image_url
					)
				);
				$this->Product->Image->create();
				if (!$this->Product->Image->save($save_image)) {
					debug($save_image);
					trigger_error('Nepodarilo se ulozit obrazek', E_USER_NOTICE);
					return false;
				} else {
					// stahnu obrazek
					if ($image_content = download_url($image_url)) {
						// nahraju obrazek na disk
						if (file_put_contents('product-images/' . $image_name, $image_content)) {
							$this->Product->Image->makeThumbnails('product-images/' . $image_name);
							return true;
						} else {
							trigger_error('Nepodarilo se ulozit obrazek ' . $image_url . ' do ' . $image_name, E_USER_NOTICE);
						}
					} else {
						trigger_error('Nepodarilo se stahnout obrazek ' . $image_url, E_USER_NOTICE);
					}
				}
			}
		}
		return false;
	}
	
	function category_id($feed_product, $id) {
		$category_id = 0;
		// zjistim rootovou kategorii stromu
		$root_category_id = $this->find('first', array(
			'conditions' => array('Supplier.id' => $id),
			'contain' => array(),
			'fields' => array('Supplier.category_id')
		));
		$parent_id = $root_category_id['Supplier']['category_id'];
		// pokud ma dodavatel definovanou kategorii, do ktere se ma vytvorit strom kategorii
		// a vyparsuju popis vetve s kategorii v shopu dodavatele
		if ($parent_id && $supplier_category_name = $feed_product->CATEGORYTEXT->__toString()) {
			$category_names = explode('|', $supplier_category_name);
			// prochazim vetev stromu, danou rodicovskym uzlem a jmenem ditete
			foreach ($category_names as $category_name) {
				$category_name = trim($category_name);
				$db_category = $this->Category->find('first', array(
					'conditions' => array(
						'Category.name' => $category_name,
						'Category.parent_id' => $parent_id
					),
					'contain' => array(),
					'fields' => array('Category.id')
				));
				// pokud root nema dite s danym jmenem
				if (empty($db_category)) {
					// vytvorim ho a cyklim dal
					$this->Category->create();
					$category = array(
						'Category' => array(
							'name' => $category_name,
							'heading' => $category_name,
							'breadcrumb' => $category_name,
							'title' => $category_name,
							'description' => $category_name,
							'parent_id' => $parent_id,
						)	
					);
					if ($this->Category->save($category)) {
						$parent_id = $this->Category->id;
						$url = strip_diacritic($category_name) . '-c' . $parent_id;
						$category['Category']['id'] = $parent_id;
						$category['Category']['url'] = $url;
						if (!$this->Category->save($category)) {
							debug($category);
							trigger_error('Nepodarilo se ulozit url kategorie', E_USER_NOTICE);
							return false;
						}
					} else {
						debug($category);
						trigger_error('Nepodarilo se ulozit kategorii do stromu', E_USER_NOTICE);
						return false;
					}
				} else {
					$parent_id = $db_category['Category']['id'];
				}
			}
			$category_id = $parent_id;
		}

		return $category_id;
	}
}