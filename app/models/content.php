<?php
class Content extends AppModel {
	var $name = 'Content';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'path' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cestu.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Webstránka s danou cestou existuje, zadejte jinou cestu.'
			)
		),
		'heading' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte nadpis.'
			)
		),
		'content' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte text.'
			)
		)
	);
	
	function afterSave($created) {
		if ($created) {
			if (array_key_exists('title', $this->data['Content']) && empty($this->data['Content']['title']) && isset($this->data['Content']['heading'])) {
				$this->data['Content']['title'] = $this->data['Content']['heading'];
			}
			if (array_key_exists('description', $this->data['Content']) && empty($this->data['Content']['description']) && isset($this->data['Content']['heading'])) {
				$this->data['Content']['description'] = 'Stránka ' . $this->data['Content']['heading'] . ' na webu ' . CUST_ROOT . '.';
			}
			$this->save($this->data);
		}
	}
	
	function redirect_url($url) {
		$redirect_url = '/';
		$old2new = array(
			'/website/kontakt/' => 'firma.htm',
			'/website/clanky/' => null,
			'/website/jak-nakupovat/' => 'jak-nakupovat.htm',
			'/website/mapa-webu/' => null,
			'/website/reklamacni-rad/' => 'reklamacni-rad.htm',
			'/website/zapomenute-heslo/' => 'obnova-hesla',
			'/website/obchodni-podminky/' => 'obchodni-podminky.htm',
			'/website/garance-nejnizsi-ceny/' => 'garance-nejnizsi-ceny.htm',
			'/website/provozovna-prodejna-v-olomouci/' => 'firma.htm'	
		);

		if (array_key_exists($url, $old2new)) {
			$redirect_url = $old2new[$url];
		}

		return $redirect_url;
	}
}
?>