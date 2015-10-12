<?php
class AllianceProductsController extends AppController {
	var $name = 'AllianceProducts';
	
	function parse_alliance() {
		// stahnu feed (je tam vubec)
		$alliance_file = $this->AllianceProduct->getAllianceFile();
		if (!$xml = file_get_contents($alliance_file)) {
			trigger_error('Chyba při stahování URL ' . $alliance_file, E_USER_ERROR);
			die();
		}
		
		$document = new SimpleXMLElement($xml);
		$feed_products = $document->xpath('//items/item');

		$save = array();
		foreach ($feed_products as $feed_product) {
			 $product = $this->AllianceProduct->allianceParseProduct($feed_product);
			 if ($product) {
			 	$product['AllianceProduct']['all_active'] = true;
			 	$save[] = $product;
			 }
		}

		$message = 'OK';
		// inicializace transakce
		$source = $this->AllianceProduct->getDataSource();
		$source->begin($this->AllianceProduct);
		// nastavim active u vsech aktivnich produktu na false
		$this->AllianceProduct->query('UPDATE alliance_products SET all_active = 0 WHERE all_active = 1');
		// ulozim vyparsovane produkty
		if (!$this->AllianceProduct->saveAll($save)) {
			$source->rollback($this->AllianceProduct);
			$message = 'Produkty z feedu Alliance se nepodarilo vyparsovat';
		} else {
			$source->commit($this->AllianceProduct);
		}
		
		echo $message;
		die();
	}
	
	function sukl_s_c_a_u_file_parse() {
		// stahnu feed (je tam vubec)
		if (!$csv = file_get_contents($this->AllianceProduct->suklSCAUFile)) {
			trigger_error('Chyba při stahování URL ' . $this->AllianceProduct->suklSCAUFile, E_USER_ERROR);
			die();
		}
		
		$lines = explode("\n", $csv);
		$save = array();
		
		foreach ($lines as $line) {
			$line = str_getcsv($line, '|');
			
			if (isset($line[0]) && isset($line[83])) {
				$sukl = $line[0];
				$sign = $line[83];
	
				$product = $this->AllianceProduct->findBySukl($sukl);
				
				if (!empty($product)) {
					$save[] = array(
						'id' => $product['AllianceProduct']['id'],
						'scau_sign' => $sign
					);
				}
			}
		}
		$message = 'OK';
		if (!$this->AllianceProduct->saveAll($save)) {
			$message = 'Produkty z feedu Alliance se nepodarilo vyparsovat';
		}
		echo $message;
		die();
	}
	
	function sukl_s_c_a_u_bez_uhrad_file_parse() {
		// stahnu feed (je tam vubec)
		if (!$csv = file_get_contents($this->AllianceProduct->suklSCAUBezUhradFile)) {
			trigger_error('Chyba při stahování URL ' . $this->AllianceProduct->suklSCAUBezUhradFile, E_USER_ERROR);
			die();
		}
	
		$lines = explode("\n", $csv);
		$save = array();
	
		foreach ($lines as $line) {
			$line = str_getcsv($line, '|');
				
			if (isset($line[0]) && isset($line[83])) {
				$sukl = $line[0];
				$sign = $line[83];
	
				$product = $this->AllianceProduct->findBySukl($sukl);
	
				if (!empty($product)) {
					$save[] = array(
							'id' => $product['AllianceProduct']['id'],
							'scau_bez_uhrad_sign' => $sign
					);
				}
			}
		}
		$message = 'OK';
		if (!$this->AllianceProduct->saveAll($save)) {
			$message = 'Produkty z feedu Alliance se nepodarilo vyparsovat';
		}
		echo $message;
		die();
	}
	
	function upload_pharmdata($limit = 1000) {
		// chci pouze volne prodejne leky, ktere jeste nemaji dotazeny data z PD
		$conditions = array(
			'AllianceProduct.pd_downloaded' => '',
			'AllianceProduct.all_code_pdk !=' => '',
			'AllianceProduct.all_active' => true
		);
		$conditions = array_merge($conditions, $this->AllianceProduct->free_sale_conditions);

		// vyberu X produktu, ktere nemaji natazeny informace z PD
		$products = $this->AllianceProduct->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'limit' => $limit
		));

		$save = array();
		
		foreach ($products as $product) {
			$short_description = $this->AllianceProduct->download_pdk_short_description($product['AllianceProduct']['all_code_pdk']);
			$image = null;
			$description = null;
			if ($short_description) {
				$image = $this->AllianceProduct->download_pdk_image($product['AllianceProduct']['all_code_pdk']);
				$description = $this->AllianceProduct->download_pdk_description($product['AllianceProduct']['all_code_pdk']);
			}
			$save[] = array(
				'id' => $product['AllianceProduct']['id'],
				'all_code_pdk' => $product['AllianceProduct']['all_code_pdk'],
				'pd_short_description' => $short_description,
				'pd_image' => $image,
				'pd_description' => $description,
				'pd_downloaded' => true
			);
		}
		
		if (empty($save)) {
			echo 'Nejsou zadne produkty, ke kterym by bylo treba natahnout PharmData';
		} else {
			if (!$this->AllianceProduct->saveAll($save)) {
				debug($save);
				echo 'Chyba pri ukladani dat stazenych z PDK';
			}
		}
		die('OK');
	}
	
	function upload_dial_data() {
		$products = $this->AllianceProduct->find('all', array(
			'conditions' => array(
				'AllianceProduct.dial_downloaded' => false,
				'AllianceProduct.all_active' => true
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'p_d_k_relations',
					'alias' => 'PDKRelation',
					'type' => 'inner',
					'conditions' => array('PDKRelation.pdk = AllianceProduct.all_code_pdk')
				),
				array(
					'table' => 'p_d_k_manufacturers',
					'alias' => 'PDKManufacturer',
					'type' => 'left',
					'conditions' => array('PDKManufacturer.shortcut = PDKRelation.manufacturer_name')
				),
				array(
					'table' => 'p_d_k_nomens',
					'alias' => 'PDKNomen',
					'type' => 'left',
					'conditions' => array('PDKNomen.shortcut = PDKRelation.nomen')
				),
			),
			'fields' => array(
				'AllianceProduct.id',
				'PDKManufacturer.name',
				'PDKNomen.name',
				'PDKRelation.ean'
			),
			'limit' => 5000
		));

		if (empty($products)) {
			echo 'Nejsou zadne produkty, ktere potrebuji stahnout data z ciselniku';
		} else {
			$save = array();
			foreach ($products as $product) {
				$save[] = array(
					'id' => $product['AllianceProduct']['id'],
					'manufacturer' => $product['PDKManufacturer']['name'],
					'nomen' => $product['PDKNomen']['name'],
					'ean' => $product['PDKRelation']['ean'],
					'dial_downloaded' => true
				);
			}
			$this->AllianceProduct->saveAll($save);
		}
		die('OK');
	}
	
	function feed($count = 1) {
		$conditions = array(
			'AllianceProduct.pd_image !=' => '',
			'AllianceProduct.pd_short_description !=' => '',
			'AllianceProduct.pd_description !=' => '',
			'AllianceProduct.pd_downloaded' => true,
			'AllianceProduct.dial_downloaded' => true,
			'AllianceProduct.all_active' => true,
			'OR' => array(
				array('AllianceProduct.all_qty1 >' => 0),
				array('AllianceProduct.all_qty2 >' => 0)
			)
		);
		// protoze je vysledny feed prilis veliky, rozdelim ho pomoci priznaku count na 2
		// feed s count 1 bude obsahovat produktu a-l
		// feed s count 2 bude obsahovat produkty m-z
		if ($count == 1) {
			$conditions[] = '((SUBSTRING(AllianceProduct.all_prod_title, 1, 1) >= "a" AND SUBSTRING(AllianceProduct.all_prod_title, 1, 1) <= "l"))';
		} elseif ($count == 2) {
			$conditions[] = '((SUBSTRING(AllianceProduct.all_prod_title, 1, 1) >= "m" AND SUBSTRING(AllianceProduct.all_prod_title, 1, 1) <= "z"))';
		}
		
		$conditions = array_merge($conditions, $this->AllianceProduct->free_sale_conditions);
		
		$this->AllianceProduct->virtualFields['vat'] = 'IF(AllianceProduct.all_vat = 19, 21, IF(AllianceProduct.all_vat = 9, 10, AllianceProduct.all_vat))';
		$products = $this->AllianceProduct->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
//			'limit' => 50
		));
		
		// vypustim z feedu ty produkty, ktere uz mam v obchodu zavedene i bez feedu (podle eanu)
		App::import('Model', 'Product');
		$this->AllianceProduct->Product = &new Product;
		foreach ($products as $index => $product) {
			if (!empty($product['AllianceProduct']['ean'])) {
				$theProduct = $this->AllianceProduct->Product->find('first', array(
					'conditions' => array(
						'Product.ean' => $product['AllianceProduct']['ean'],
						'Product.supplier_id NOT IN (4, 5)'
					),
					'contain' => array()
				));
				if (!empty($theProduct)) {
					unset($products[$index]);
				}
			}
		}

		$this->set('products', $products);
		$this->layout = 'xml/heureka';
	}
	
	function save_feeds() {
		for ($i = 1; $i <= 2; $i++) {
			$file_name = 'files/uploads/alliance' . $i . '.xml';
			$content = download_url_like_browser('http://' . $_SERVER['HTTP_HOST'] . '/alliance_products/feed/' . $i);
			if (file_put_contents($file_name, $content) === false) {
				echo 'nepodarilo se ulozit XML feed vygenerovany z alliance a PD dat';
			}
		}

		die('OK');
	}
}
?>