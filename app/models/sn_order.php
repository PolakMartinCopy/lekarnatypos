<?php 
class SnOrder extends AppModel {
	var $name = 'SnOrder';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array(
		'SnOrderItem' => array(
			'dependent' => true
		)
	);
	
	var $folder = 'files/sn_orders';
	
	function types() {
		return array(
			array('type' => 'nova', 'url' => 'http://www.sportnutrition.cz/admin/objednavky:13/nova/'),
			array('type' => 'vyrizuje-se', 'url' => 'http://www.sportnutrition.cz/admin/objednavky:14/vyrizuje-se/'),
			array('type' => 'pripravena-k-expedici', 'url' => 'http://www.sportnutrition.cz/admin/objednavky:15/pripravena-k-expedici/'),
			array('type' => 'ceka-na-vyzvednuti', 'url' => 'http://www.sportnutrition.cz/admin/objednavky:16/ceka-na-vyzvednuti/'),
			array('type' => 'vyrizena', 'url' => 'http://www.sportnutrition.cz/admin/objednavky:17/vyrizena/'),
			array('type' => 'zrusena', 'url' => 'http://www.sportnutrition.cz/admin/objednavky:18/zrusena/'),
			array('type' => 'archiv', 'url' => 'http://www.sportnutrition.cz/admin/objednavky:19/archiv/')	
		);
	}
	
	function settings() {
		$settings = $this->query('
			SELECT *
			FROM parser_settings
			WHERE
				name="order_file_last_processed"
		');
	
		$settings = Set::combine($settings, '{n}.parser_settings.name', '{n}.parser_settings.value');
		return $settings;
	}
	
	// vyparsuje z html pole objednavek
	function parse($content) {
		// rozdelim dokument do pole, kde kazdy prvek bude segment o jedne objednavce
		preg_match_all('/<td align=\'center\'><input type="checkbox.*<\/form><\/table><\/td><\/tr>/Us', $content, $order_segments);
		$order_segments = $order_segments[0];
		if (count($order_segments) != 50) {
			debug('WARNING: Mozna se nevyparsovaly vsechny objednavky na strance, pocet vyparsovanych objednavek je ' . count($order_segments));
		}
		
		$orders = array();
		foreach ($order_segments as $order_segment) {
			$orders[] = $this->__parse_order($order_segment);
		}

		return $orders;
	}
	
	// z html struktury zjisti data o objednavce
	function __parse_order($order_segment) {
		// id objednavky
		preg_match('/name="objednavky_vyber\[\]" value="(\d+)"><\/td>/', $order_segment, $id);
		if (empty($id)) {
			debug($order_segment);
			debug('ERROR: Nevyparsovalo se id objednavky');
		} else {
			$id = $id[1];
		}
		// datum objednavky
		preg_match('/EFORM<\/a><\/td><td>(.*)<br \/>(.*)<br \/><img src=/U', $order_segment, $date);
		if (empty($date) || count($date) != 3) {
			debug($order_segment);
			debug('ERROR: Nevyparsovalo se datum objednavky');
		} else {
			$date = cz2db_datetime($date[1] . ' ' . $date[2]);
		}
		// email uzivatele
		preg_match('/e-mail: <a href=\'mailto:(.*)\'>/U', $order_segment, $email);
		if (empty($email)) {
			debug($order_segment);
			debug('ERROR: Nevyparsoval se email zakaznika');
		} else {
			$email = $email[1];
		}
		// cena objednavky
		preg_match('/<td align=\'right\' style=\'white-space: nowrap;\'>(\d+) CZK/', $order_segment, $price);
		if (empty($price)) {
			debug($order_segment);
			debug('ERROR: Nevyparsovala se cena objednavky');
		} else {
			$price = $price[1];
		}
		// poznamka - NENI POVINNA
		preg_match('/Poznámka: <font color=\'#ff0000\'>(.*)<\/font>/Us', $order_segment, $note);
		if (empty($note)) {
			$note = '';
		} else {
			$note = $note[1];
		}
		// interni poznamka - NENI POVINNA
		preg_match('/Interní poznámka: <font color=\'#0000ff\'>(.*)<\/font>/Us', $order_segment, $internal_note);
		if (!empty($internal_note)) {
			$internal_note = $internal_note[1];
		}
		preg_match('/<select class=\'textboxbez\' name=\'form_stav\'>.*<option selected value=\'.*\'>(.*)<\/option>/Us', $order_segment, $state);
		if (empty($state)) {
			debug($order_segment);
			debug('ERROR: Nevyparsoval se stav objednavky');
		} else {
			$state = $state[1];
		}
		
		$order = compact('id', 'date', 'email', 'price', 'note', 'internal_note', 'state');
		
		$order_items = $this->SnOrderItem->parse($order_segment);
		
		$order = array(
			'SnOrder' => $order,
			'SnOrderItem' => $order_items
		);
		return $order;
	}
	
	// zjisti index posledni stranky ve strankovani
	function get_last_pagination_index($content) {
		$last_index = 1;

		$dom = new DOMDocument('1.0');
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		libxml_use_internal_errors(true);
		if (!$dom->loadHTML($content)) {
			return false;
		}
		$domXPath = new DOMXPath($dom);
		
		$index = $domXPath->query('//div[@class="strankovani"]/a[last()]');
		if ($index->length) {
			$last_index = $index->item(0)->nodeValue;
		}
		return $last_index;
	}
	
	/**
	 * zjisti nazev souboru, ktery nasleduje v adresari za danym nazvem na vstupu
	 * 
	 * @param string $file
	 */
	function get_actual_file($file) {
		// nactu soubory se seznamy objednavek
		$files = scandir($this->folder);
		// zahodim . a ..
		unset($files[0]);
		unset($files[1]);
		// pokud neni seznam souboru prazdny
		if (!empty($files)) {
			// a pokud je vstup prazdny
			if (!$file) {
				// vratim hned prvni soubor ze slozky
				return $files[2];
			} else {
				// prochazim seznam souboru ve slozce
				for ($i = 2; $i < count($files); $i++) {
					// a hledam ten, ktery se jmenuje stejne, jako je vstup
					if ($files[$i] == $file && isset($files[$i+1])) {
						// kdyz ho najdu, vratim nasledujici (pokud existuje)
						return $files[$i+1];
					}
				}
			}
		}
		return false;
	}
}
?>