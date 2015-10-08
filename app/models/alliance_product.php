<?php
class AllianceProduct extends AppModel {
	var $name = 'AllianceProduct';
	
	var $actsAs = array('Containable');
	
	// adresar s ciselniky z alliance
	var $allianceDir = 'files/alliance/';
	
	// soubor s ciselnikem ze suklu - Seznam cen a uhrad
	var $suklSCAUFile = 'files/SCAU150901.txt';
	
	// soubor s ciselnikem ze suklu Seznam léčiv nehrazených ze zdravotního pojištění
	var $suklSCAUBezUhradFile = 'files/SCAU150901_BEZ_UHRAD.txt';
	
	var $pdk_id="maevita";
	
	var $pdk_server_script="http://www.pharmdata.cz/fetch_strict.php";
	
	// volne prodejne jsou ty leky, ktere nemaji kod sukl, nebo ho maji a priznak z ciselniku je bud F nebo V
	var $free_sale_conditions = array(
		'OR' => array(
			array('AllianceProduct.all_code_sukl' => ''),
			array('AllianceProduct.scau_sign' => array('F', 'V')),
			array('AllianceProduct.scau_bez_uhrad_sign' => array('F', 'V')),
		)
	);
	
	function beforeSave() {
		if (isset($this->data['AllianceProduct']['all_price'])) {
			$this->data['AllianceProduct']['all_price'] = str_replace(',', '.', $this->data['AllianceProduct']['all_price']);
		}
		if (isset($this->data['AllianceProduct']['all_uhrada_vzp'])) {
			$this->data['AllianceProduct']['all_uhrada_vzp'] = str_replace(',', '.', $this->data['AllianceProduct']['all_uhrada_vzp']);
		}
		return true;
	}
	
	// vrati soubor s ciselnikem z Alliance
	function getAllianceFile() {
		$latest_file = latest_file($this->allianceDir);
		return $this->allianceDir . $latest_file;
	}
	
	function findByPdk($pdk) {
		$product = $this->find('first', array(
			'conditions' => array('AllianceProduct.all_code_pdk' => $pdk),
			'contain' => array()
		));
		
		return $product;
	}
	
	function findBySukl($sukl) {
		$product = $this->find('first', array(
			'conditions' => array('AllianceProduct.all_code_sukl' => $sukl),
			'contain' => array()
		));
	
		return $product;
	}
	
	function hasPdk($pdk) {
		$hasPdk = $this->find('count', array(
			'conditions' => array('AllianceProduct.all_code_pdk' => $pdk),
			'contain' => array()
		));
		
		return $hasPdk;
	}
	
	function allianceParseProduct($feed_product) {
		$attributes = $feed_product->attributes();
		
		$code_int = $attributes['code_int']->__toString();
		$code_sukl = $attributes['code_sukl']->__toString();
		$code_pdk = $attributes['code_pdk']->__toString();
		$producer = $attributes['producer']->__toString();
		$atc = $attributes['atc']->__toString();
		$prod_title = $attributes['prod_title']->__toString();
		$price = $attributes['price']->__toString();
		$vat = $attributes['vat']->__toString();
		$qty1 = $attributes['qty1']->__toString();
		$qty2 = $attributes['qty2']->__toString();
		$uhrada_vzp = $this->getAllUhradaVZP($feed_product);
		
		// nechci tam produkty bez kodu PDK
		if ($code_pdk == '') {
			return false;
		} 
		
		$product = $this->findByPdk($code_pdk);
		
		$resProduct = array(
			'AllianceProduct' => array(
				'all_code_int' => $code_int,
				'all_code_sukl' => $code_sukl,
				'all_code_pdk' => $code_pdk,
				'all_producer' => $producer,
				'all_atc' => $atc,
				'all_prod_title' => $prod_title,
				'all_price' => $price,
				'all_vat' => $vat,
				'all_qty1' => $qty1,
				'all_qty2' => $qty2,
				'all_uhrada_vzp' => $uhrada_vzp,
			)
		);
		
		if (!empty($product)) {
			$resProduct['AllianceProduct']['id'] = $product['AllianceProduct']['id'];
		}

		return $resProduct;
	}
	
	function getAllUhradaVZP($feed_product) {
		$attributes = $feed_product->attributes();
		$price = $attributes['uhrada_vzp']->__toString();
		if (empty($price)) {
			$price = 0;
		}
		return $price;
	}
	
	function download_pdk_short_description($pdk) {
		// samotné zjištění, co je k danému dokumentu k dispozici
		$info = $this->pd_rozkoduj($pdk, 'pdk');

		// nejprve ověříme, zda bylo vůbec něco nalezenno
		if(empty($info)){
			return false;
			// a pokud ano, zobrazíme seznam nalezených informací
		} else {
			if(!empty($info["kratky_text"])) {
				if ($info['kratky_text'] == 'Neexistující PDK kód') {
					return false;
				}
				return $info["kratky_text"];
			}
		}
		return false;
	}
	
	function download_pdk_image($pdk) {
		$typ = 'pdk';
		$url = $this->pdk_server_script."?id=$this->pdk_id&info=obr&$typ=$pdk";
		return $url;
	}
	
	function download_pdk_description($pdk, $format = 'jpg') {
		/* 	Pokud chcete omezit zobrazeni pouze na vybrane radky, nastavte patricnou
		 bitovou masku, budto jako predvolbu pro vsechny zobrazene soubory odkomentovanim
		 nasledujiciho prikazu, nebo	pridanim parametru maska do URL.
		 POZOR: do URL lze vkladat masku pouze decimalne!
		 */
		// $pdk_maska= 0x5;
		if($format == "jpg") $format_str = "jpeg"; else $format_str = $format; // pro zasl�n� spr�vn� hlavi�ky
		
		$typ = 'pdk'; // apa m� v�t�� prioritu
		
		$pdk_url_maska="";
		
		$url = $this->pdk_server_script."?id=$this->pdk_id&info=kratky&$typ=$pdk&format=$format&sirka=770$pdk_url_maska";
		return $url;
	}
	
	function pd_rozkoduj($key, $typ = 'pdk') {
		if(!in_array($typ,array("apa","pdk"))){
			return false;
		}
		
		$file = download_url_like_browser($this->pdk_server_script."?id=$this->pdk_id&info=info&$typ=$key");
		$file = explode("\n", $file);
		$umi=explode("|",trim($file[0]));
		$umi_ar=array();
		$strana_ar=array();
		
		foreach ($umi as $val){
			if (empty($val)) continue;
			$val = explode("/",$val);
			$info = $val[0];
			$strana = null;
			if (count($val) == 2) {
				$strana = $val[1];
			}
			if(!empty($strana)){
				$strana_ar[$info][]="&amp;strana=$strana";
			} else {
				$strana_ar[$info][]="";
			}
		}
		
		if(isset($strana_ar["obr"]) && isset($strana_ar["kratky"])) $strana_ar["kratky_obr"][]="";
		
		// pridani prvni kapitoly kratke textove informace formou textu
		array_shift($file);
		$strana_ar["kratky_text"] = implode("\n",$file);
		return($strana_ar);
	}
	
	function getManufacturerName($pdk) {
		App::import('Model', 'PDKRelation');
		$this->PDKRelation = &new PDKRelation;
		
		$manufacturer = $this->PDKRelation->getManufacturer($pdk);

		return $manufacturer;
	}
	
	function getAtcName($pdk) {
		App::import('Model', 'PDKRelation');
		$this->PDKRelation = &new PDKRelation;
		
		$atc = $this->PDKRelation->getAtc($pdk);
		
		return $atc;
	}
}
?>