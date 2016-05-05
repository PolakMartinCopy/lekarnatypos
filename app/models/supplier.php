<?php
class Supplier extends AppModel {
	var $name = 'Supplier';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'SupplierCategory',
		'Product'
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
		'feed_type' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte typ feedu'
			)
		),
		'product_field' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název tagu, ve kterém jsou v XML data o produktu'
			)
		),
		'id_field' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název tagu, ve kterém je v XML feedu ID produktu dodavatele'
			)
		),
		'name_field' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název tagu, ve kterém je v XML feedu název produktu '
			)
		),
		'short_description_field' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název tagu, ve kterém je v XML feedu krátký popis produktu'
			)
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
	
	function beforeSave() {
		if (array_key_exists('heading_field', $this->data['Supplier']) && empty($this->data['Supplier']['heading_field']) && array_key_exists('name_field', $this->data['Supplier'])) {
			$this->data['Supplier']['heading_field'] = $this->data['Supplier']['name_field'];
		}
		if (array_key_exists('breadcrumb_field', $this->data['Supplier']) && empty($this->data['Supplier']['breadcrumb_field']) && array_key_exists('name_field', $this->data['Supplier'])) {
			$this->data['Supplier']['breadcrumb_field'] = $this->data['Supplier']['name_field'];
		}
		if (array_key_exists('related_name_field', $this->data['Supplier']) && empty($this->data['Supplier']['related_name_field']) && array_key_exists('name_field', $this->data['Supplier'])) {
			$this->data['Supplier']['related_name_field'] = $this->data['Supplier']['name_field'];
		}
		if (array_key_exists('heureka_name_field', $this->data['Supplier']) && empty($this->data['Supplier']['heureka_name_field']) && array_key_exists('name_field', $this->data['Supplier'])) {
			$this->data['Supplier']['heureka_name_field'] = $this->data['Supplier']['name_field'];
		}
		if (array_key_exists('zbozi_name_field', $this->data['Supplier']) && empty($this->data['Supplier']['zbozi_name_field']) && array_key_exists('name_field', $this->data['Supplier'])) {
			$this->data['Supplier']['zbozi_name_field'] = $this->data['Supplier']['name_field'];
		}
		if (array_key_exists('title_field', $this->data['Supplier']) && empty($this->data['Supplier']['title_field']) && array_key_exists('name_field', $this->data['Supplier'])) {
			$this->data['Supplier']['title_field'] = $this->data['Supplier']['name_field'];
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
	function validate_feed($feed_product, $id_field, $name_field, $short_description_field, $price_field) {
		$valid = true;
		// povinne atributy elementu XML zdroje
		$required = array(0 => $id_field, $name_field, $short_description_field, $price_field);
		
		$i = 0;
		while ($valid && $i < count($required)) {
/*			// zkousim, jestli tam je povinny atribut
			// musim zjistit, jestli nazev elementu s hodnotou atributu neobsahuje namespace
			if (preg_match('/([^:]+):(.*)/', $required[$i], $matches)) {
				$namespace_name = $matches[1];
				$element_name = $matches[2];
				$namespace = $feed_product->children($namespaces[$namespace_name]);

				$valid = $valid && $namespace->$element_name;
			} else {
				$valid = $valid && $feed_product->{$required[$i]};
			}
*/
			$valid = $valid && simpleXMLChildExists($feed_product, $required[$i]);			
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
	function product($feed_product, $supplier) {
		$product = array();
		// atributy produktu
		try {
			//		- nazev
			$name = $this->product_name($feed_product, $supplier['Supplier']['name_field'], $supplier['Supplier']['id']);
			//		- heading
			$heading = $this->product_heading($feed_product, $supplier['Supplier']['heading_field'], $supplier['Supplier']['id']);
			//		- breadcrumb
			$breadcrumb = $this->product_breadcrumb($feed_product, $supplier['Supplier']['breadcrumb_field'], $supplier['Supplier']['id']);
			//		- related_name
			$related_name = $this->product_related_name($feed_product, $supplier['Supplier']['related_name_field'], $supplier['Supplier']['id']);
			//		- zbozi_name
			$zbozi_name = $this->product_zbozi_name($feed_product, $supplier['Supplier']['zbozi_name_field'], $supplier['Supplier']['id']);
			//		- zbozi_name
			$heureka_name = $this->product_heureka_name($feed_product, $supplier['Supplier']['heureka_name_field'], $supplier['Supplier']['id']);
			//		- title
			$title = $this->product_title($feed_product, $supplier['Supplier']['title_field'], $supplier['Supplier']['id']);
			//		- short_description
			$short_description = $this->product_short_description($feed_product, $supplier['Supplier']['short_description_field']);
			//		- description
			$description = $this->product_description($feed_product, $supplier['Supplier']['description_field']);
			//		- retail_price_with_dph
			$retail_price_with_dph = $this->product_retail_price_with_dph($feed_product, $supplier['Supplier']['price_field'], $supplier['Supplier']['price_increase']);
			// v google feedu je soucasti ceny mena, v nasem pripade CZK
			if ($supplier['Supplier']['feed_type'] == 'google') {
				$retail_price_with_dph = str_replace(' CZK', '', $retail_price_with_dph);
			}
			//		- discount_common
			$discount_common = $this->product_discount_common($feed_product, $supplier['Supplier']['price_field'], $supplier['Supplier']['discount_field'], $supplier['Supplier']['discount']);
			// v google feedu je soucasti ceny mena, v nasem pripade CZK
			if ($supplier['Supplier']['feed_type'] == 'google') {
				$discount_common = str_replace(' CZK', '', $discount_common);
			}
			//		- nakupni cena
			$wholesale_price = $this->product_wholesale_price($feed_product, $supplier['Supplier']['wholesale_price_field']);
			//		- ean
			$ean = $this->product_ean($feed_product, $supplier['Supplier']['ean_field']);
			//		- supplier product id - id produktu ve feedu dodavatele
			$supplier_product_id = $this->product_supplier_product_id($feed_product, $supplier['Supplier']['id_field']);
			//		- supplier_category_id - id kategorie dodavatele u nas v systemu
			$supplier_category_id = $this->product_supplier_category_id($feed_product, $supplier['Supplier']['category_field'], $supplier['Supplier']['id']);
			// 		- dostupnost
			$availability_id = $this->product_availability_id($feed_product, $supplier['Supplier']['feed_type'], $supplier['Supplier']['availability_field']);
			// 		- vyrobce
			$manufacturer_id = $this->product_manufacturer_id($feed_product, $supplier['Supplier']['manufacturer_field']);
			// feed syncare nema vyrobce, ale vsechny produkty jsou jeho, takze id vyrobce nastavim natvrdo
			if (!$manufacturer_id && $supplier['Supplier']['id'] == 2) {
				$manufacturer_id = 127;
			}
			// feed boneco nema vyrobce, ale vsechny produkty jsou jeho, takze id vyrobce nastavim natvrdo
			if (!$manufacturer_id && $supplier['Supplier']['id'] == 3) {
				$manufacturer_id = 177;
			}
				
			// 		- danova trida
			$tax_class_id = $this->product_tax_class_id($feed_product, $supplier['Supplier']['vat_field']);
			// feed syncare nema danovou tridu, ale vsechny produkty jsou s DPH 21%
			if (!$tax_class_id && $supplier['Supplier']['id'] == 2) {
				$tax_class_id = 1;
			}
			// feed boneco nema danovou tridu, ale vsechny produkty jsou s DPH 21%
			if (!$tax_class_id && $supplier['Supplier']['id'] == 3) {
				$tax_class_id = 1;
			}
			if (!$tax_class_id) {
				debug($feed_product);
				debug('Chyba pri zjistovani danove tridy produktu');
				return false;
			}
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
				'heureka_name' => $heureka_name,
				'title' => $title,
				'short_description' => $short_description,
				'description' => $description,
				'retail_price_with_dph' => $retail_price_with_dph,
				'discount_common' => $discount_common,
				'wholesale_price' => $wholesale_price,
				'ean' => $ean,
				'supplier_product_id' => $supplier_product_id,
				'supplier_category_id' => $supplier_category_id,
				'availability_id' => $availability_id,
				'manufacturer_id' => $manufacturer_id,
				'tax_class_id' => $tax_class_id,
				'feed' => true
			)	
		);
		return $product;
	}
	
	function product_name($feed_product, $name_field, $supplier_id) {
		$name = simpleXMLChildValue($feed_product, $name_field);
		// u syncare musim k nazvu priradit oznaceni velikosti
		if ($supplier_id == 2) {
			$name .= $this->get_syncare_size($feed_product);
		}
		return $name;
	}
	
	function product_heading($feed_product, $heading_field, $supplier_id) {
		$heading = simpleXMLChildValue($feed_product, $heading_field);
		// u syncare musim k nazvu priradit oznaceni velikosti
		if ($supplier_id == 2) {
			$heading .= $this->get_syncare_size($feed_product);
		}
		return $heading;
	}
	
	function product_breadcrumb($feed_product, $breadcrumb_field, $supplier_id) {
		$breadcrumb = simpleXMLChildValue($feed_product, $breadcrumb_field);
		// u syncare musim k nazvu priradit oznaceni velikosti
		if ($supplier_id == 2) {
			$breadcrumb .= $this->get_syncare_size($feed_product);
		}
		return $breadcrumb;
	}
	
	function product_related_name($feed_product, $related_name_field, $supplier_id) {
		$related_name = simpleXMLChildValue($feed_product, $related_name_field);
		// u syncare musim k nazvu priradit oznaceni velikosti
		if ($supplier_id == 2) {
			$related_name .= $this->get_syncare_size($feed_product);
		}
		return $related_name;
	}
	
	function product_zbozi_name($feed_product, $zbozi_name_field, $supplier_id) {
		$zbozi_name = simpleXMLChildValue($feed_product, $zbozi_name_field);
		// u syncare musim k nazvu priradit oznaceni velikosti
		if ($supplier_id == 2) {
			$zbozi_name .= $this->get_syncare_size($feed_product);
		}
		return $zbozi_name;
	}
	
	function product_heureka_name($feed_product, $heureka_name_field, $supplier_id) {
		$heureka_name = simpleXMLChildValue($feed_product, $heureka_name_field);
		// u syncare musim k nazvu priradit oznaceni velikosti
		if ($supplier_id == 2) {
			$heureka_name .= $this->get_syncare_size($feed_product);
		}
		return $heureka_name;
	}
	
	function product_title($feed_product, $title_field, $supplier_id) {
		$title = simpleXMLChildValue($feed_product, $title_field);
		// u syncare musim k nazvu priradit oznaceni velikosti
		if ($supplier_id == 2) {
			$title .= $this->get_syncare_size($feed_product);
		}
		return $title;
	}
	
	function product_short_description($feed_product, $short_description_field) {
		return simpleXMLChildValue($feed_product, $short_description_field);
	}
	
	function product_description($feed_product, $description_field) {
		return simpleXMLChildValue($feed_product, $description_field);
	}
	
	function product_retail_price_with_dph($feed_product, $price_field, $price_increase = null) {
		$price = simpleXMLChildValue($feed_product, $price_field);
		if (isset($price_increase) && $price_increase > 0) {
			$price = round($price * (1 + ($price_increase / 100)));
		}
		return $price;
	}
	
	function product_discount_common($feed_product, $price_field, $discount_field, $discount = null) {
		$discount_price = simpleXMLChildValue($feed_product, $discount_field);
		if (isset($discount) && $discount > 0) {
			$price = simpleXMLChildValue($feed_product, $price_field);
			$discount_price = round($price * (1 - ($discount / 100)));
		}
		return $discount_price;
	}
	
	function product_wholesale_price($feed_product, $wholesale_price_field) {
		return simpleXMLChildValue($feed_product, $wholesale_price_field);
	}
	
	function product_ean($feed_product, $ean_field) {
		return simpleXMLChildValue($feed_product, $ean_field);
	}
	
	function product_supplier_product_id($feed_product, $id_field) {
		return simpleXMLChildValue($feed_product, $id_field);
	}
	
	function product_supplier_category_id($feed_product, $category_field, $supplier_id) {
		$supplier_category_name = 'NEDEFINOVANO';
		if ($feed_supplier_category_name = simpleXMLChildValue($feed_product, $category_field)) {
			$supplier_category_name = $feed_supplier_category_name;
		}

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
			return $supplier_category['SupplierCategory']['id'];
		}

		return false;
	}
	
	function product_availability_id($feed_product, $feed_type, $availability_field) {
		$availability = simpleXMLChildValue($feed_product, $availability_field);
		$availability_id = 5;
		if ($feed_type == 'heureka') {
			// namapuju hodnoty z feedu na idcka dostupnosti u nas v obchode
			if ($availability == 0) {
				$availability_id = 1;
			} elseif ($availability >= 1 && $availability <= 7) {
				$availability_id = 2;
			} elseif ($availability >= 7) {
				$availability_id = 5;
			}
		} elseif ($feed_type == 'google') {
			switch ($availability) {
				case 'in stock': $availability_id = 1; break;
				case 'out of stock': $availability_id = 4; break;
				case 'preorder': $availability_id = 2; break;
			}
		} else {
			die('musim ziskat nejak dostupnost produktu');			
		}
		return $availability_id;
	}
	
	function product_manufacturer_id($feed_product, $manufacturer_field) {
		if ($manufacturer = simpleXMLChildValue($feed_product, $manufacturer_field)) {
			$db_manufacturer = $this->Product->Manufacturer->find('first', array(
				'conditions' => array('Manufacturer.supplier_alias' => $manufacturer),
				'contain' => array(),
				'fields' => array('Manufacturer.id')
			));
			
			if (empty($db_manufacturer)) {
				$save_manufacturer = array(
					'Manufacturer' => array(
						'name' => $manufacturer,
						'supplier_alias' => $manufacturer,
						'active' => true
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
	
	function product_tax_class_id($feed_product, $vat_field) {
		$tax_class = simpleXMLChildValue($feed_product, $vat_field);
		if (!$tax_class) {
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
	
	function image_url($feed_product, $image_field) {
		return simpleXMLChildValue($feed_product, $image_field);
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
					trigger_error('Nepodarilo se ulozit obrazek', E_USER_NOTICE);
					return false;
				} else {
					// stahnu obrazek
					if ($image_content = download_url_like_browser($image_url)) {
						// nahraju obrazek na disk
						if (file_put_contents('product-images/' . $image_name, $image_content)) {
							// pokud je obrazek typu PNG, musim mu udelat bile pozadi
							$file_ext = explode('.', $image_url);
							$file_ext = $file_ext[count($file_ext)-1];
							if ($file_ext == 'png' || $file_ext == 'PNG') {
								$file_path = 'product-images/' . $image_name;
								$save_path = 'product-images/' . $image_name;
								$color_rgb = array('red' => 255, 'green' => 255, 'blue' => 255);

								$img = @imagecreatefrompng($file_path);
								$width  = imagesx($img);
								$height = imagesy($img);
								//create new image and fill with background color
								$background_img = @imagecreatetruecolor($width, $height);
								$color = imagecolorallocate($background_img, $color_rgb['red'], $color_rgb['green'], $color_rgb['blue']);
								imagefill($background_img, 0, 0, $color);
								
								//copy original image to background
								imagecopy($background_img, $img, 0, 0, 0, 0, $width, $height);
								
								//save as png
								imagepng($background_img, $save_path, 0);
							}
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
	
	function category_id($feed_product, $category_field, $id) {
		$category_id = 0;
		if ($supplier_category_name = simpleXMLChildValue($feed_product, $category_field)) {
			$supplier_category = $this->SupplierCategory->find('first', array(
				'conditions' => array(
					'SupplierCategory.name' => $supplier_category_name,
					'SupplierCategory.supplier_id' => $id
				),
				'contain' => array()
			));

			if (!empty($supplier_category)) {
				$category_id = $supplier_category['SupplierCategory']['category_id'];
			}
		}
		return $category_id;
	}
	
	function get_local_xml_path($id) {
		$dir = 'files/uploads/';
		
		$supplier = $this->find('first', array(
			'conditions' => array('Supplier.id' => $id),
			'contain' => array()
		));
		
		if (!isset($supplier) || empty($supplier)) {
			return false;
		}
		
		return $dir . strip_diacritic($supplier['Supplier']['name']) . '.xml';
	}
	
	function get_product_conditions($id) {
		$conditions = array();
		switch ($id) {
			case 1: $conditions = array('Product.manufacturer_id' => 159, 'Product.active' => true); break;
			case 2: $conditions = array('Product.manufacturer_id' => 127, 'Product.active' => true); break;
		}
		return $conditions;
	}
	
	function get_xml_products_list($id) {
		$xml_products_list = array();
		$file = $this->get_local_xml_path($id);
		$file_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $file;
		switch ($id) {
			case 1: 
				$id_xpath = '//SHOPITEM/PRODUCTNO';
				$title_xpath = '//SHOPITEM/PRODUCT';
				if (!$xml_products_list = get_topvet_xml_products_list($file_url, $id_xpath, $title_xpath)) {
					trigger_error('Nepodarilo se vyparsovat ID a nazvy produktu z XML', E_USER_ERROR);
				}
				break;
			case 2: 
				$id_xpath = '//item/g:id';
				$title_xpath = '//item/title';
				$size_xpath = '//item/g:size';
				if (!$xml_products_list = get_syncare_xml_products_list($file_url, $id_xpath, $title_xpath, $size_xpath)) {
					trigger_error('Nepodarilo se vyparsovat ID a nazvy produktu z XML', E_USER_ERROR);
				}
				break;
		}
		return $xml_products_list;
	}
	
	// u syncare musim k nazvu priradit oznaceni velikosti, ktere je v elementu g:size
	function get_syncare_size($feed_product) {
		$size = simpleXMLChildValue($feed_product, 'g:size');
		if ($size != 'kus' && $size != '1 kus') {
			return ' ' . $size;
		}
		return '';
	}
	
	/*
	 * Pri natahovani produktu z feedu vypustim ty atributy, ktere nechci updatovat
	 */
	function product_required_properties($product) {
		$id = $product['Product']['id'];

		$properties = $this->Product->ProductPropertiesProduct->find('all', array(
			'conditions' => array('ProductPropertiesProduct.product_id' => $id),
			'contain' => array('ProductProperty'),
		));

		// pokud nemam zadano jinak, u produktu SynCare a TopVet a Boneco nechci updatovat nic
		if (empty($properties) && ($product['Product']['supplier_id'] == 2 || $product['Product']['supplier_id'] == 1) || $product['Product']['supplier_id'] == 3) {
			foreach ($product['Product'] as $key => $value) {
				if ($key != 'id') {
					unset($product['Product'][$key]);
				}
			}
		} else {
			foreach ($properties as $property) {
				switch($property['ProductProperty']['name']) {
					case 'Product.name':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['name']);
						}
						break;
					case 'Product.heading':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['heading']);
						}
						break;
					case 'Product.breadcrumb':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['breadcrumb']);
						}
						break;
					case 'Product.related_name':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['related_name']);
						}
						break;
					case 'Product.zbozi_name':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['zbozi_name']);
						}
						break;
					case 'Product.heureka_name':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['heureka_name']);
						}
						break;
					case 'Product.title':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['title']);
						}
						break;
					case 'Product.short_description':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['short_description']);
						}
						break;
					case 'Product.description':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['description']);
						}
						break;
					case 'Product.wholesale_price':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['wholesale_price']);
						}
						break;
					case 'Product.ean':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['ean']);
						}
						break;
					case 'Product.availability':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['availability_id']);
						}
						break;
					case 'Product.manufacturer':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['manufacturer_id']);
						}
						break;
					case 'Product.tax_class':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['tax_class_id']);
						}
						break;
					case 'Product.prices':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['retail_price_with_dph']);
							unset($product['Product']['discount_common']);
						}
						break;
					case 'Product.active':
						if (!$property['ProductPropertiesProduct']['update']) {
							unset($product['Product']['active']);
						}
						break;
				}
			}
		}

		return $product;
	}
	
	// zjisti, jestli chci u produktu updatovat data o obrazku daty z feedu
	function product_update_image($product_id, $id) {
		return $this->Product->is_product_property_editable($product_id, 16, $id);
	}
	
	function product_update_categories($product_id, $id) {
		return $this->Product->is_product_property_editable($product_id, 17, $id);
	}
	
	function product_update_prices($product_id, $id) {
		return $this->Product->is_product_property_editable($product_id, 11, $id);
	}
}
