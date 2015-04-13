<?php
class SnCustomer extends AppModel {
	var $name = 'SnCustomer';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('SnOrder');

	function settings() {
		$settings = $this->query('
			SELECT *
			FROM parser_settings
			WHERE
				name="customer_to_parse" OR
				name="customers_total" OR
				name="customers_step"	
		');
		
		$settings = Set::combine($settings, '{n}.parser_settings.name', '{n}.parser_settings.value');
		return $settings;
	}
	
	function parse($content) {
//debug(htmlspecialchars($content)); die();
		// abych mohl poskladat DOM strom
		$dom = new DOMDocument('1.0');
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		libxml_use_internal_errors(true);
		if (!$dom->loadHTML($content)) {
			return false;
		}
		$domXPath = new DOMXPath($dom);
		// aktivni?
		$active = $domXPath->query('//input[@name="form_admin_active"]/@checked');
		if ($active->length == 0) {
			$active = false;
		} elseif ($active->length == 1) {
			$active = ($active->item(0)->nodeValue  == 'checked');
		} else {
			return false;
		}
		// cenova kategorie
		$price_category = $domXPath->query('//select[@name="form_admin_kategorie"]/option[@selected]');
		if ($price_category->length == 0) {
			$price_category = '';
		} elseif ($price_category->length == 1) {
			$price_category = $price_category->item(0)->nodeValue;
		} else {
			return false;
		}
		// firma
		$company_name = $domXPath->query('//input[@name="form_admin_firma"]/@value');
		if ($company_name->length == 0) {
			$company_name = null;
		} elseif ($company_name->length == 1) {
			$company_name = $company_name->item(0)->nodeValue;
		} else {
			return false;
		}
		// ico
		$company_ico = $domXPath->query('//input[@name="form_admin_ic"]/@value');
		if ($company_ico->length == 0) {
			$company_ico = null;
		} elseif ($company_ico->length == 1) {
			$company_ico = $company_ico->item(0)->nodeValue;
		} else {
			return false;
		}
		// dic
		$company_dic = $domXPath->query('//input[@name="form_admin_dic"]/@value');
		if ($company_dic->length == 0) {
			$company_dic = null;
		} elseif ($company_dic->length == 1) {
			$company_dic = $company_dic->item(0)->value;
		} else {
			return false;
		}
		// jmeno
		$name = $domXPath->query('//input[@name="form_admin_jmeno"]/@value');
		if ($name->length != 1) {
			return false;
		} else {
			$name = $name->item(0)->nodeValue;
		}
		// ulice
		$street = $domXPath->query('//input[@name="form_admin_uliceacp"]/@value');
		if ($street->length == 0) {
			$street = '';
		} elseif ($street->length == 1) {
			$street = $street->item(0)->nodeValue;
		} else {
			return false;
		}
		// mesto
		$city = $domXPath->query('//input[@name="form_admin_mesto"]/@value');
		if ($city->length == 0) {
			$city = '';
		} elseif ($city->length == 1) {
			$city = $city->item(0)->nodeValue;
		} else {
			return false;
		}
		// PSC
		$zip = $domXPath->query('//input[@name="form_admin_psc"]/@value');
		if ($zip->length == 0) {
			$zip = '';
		} elseif ($zip->length == 1) {
			$zip = $zip->item(0)->nodeValue;
		} else {
			return false;
		}
		// stat
		$state = $domXPath->query('//select[@name="form_admin_stat"]/option[@selected]');
		if ($state->length == 0) {
			$state = '';
		} elseif ($state->length == 1) {
			$state = $state->item(0)->nodeValue;
		} else {
			return false;
		}
		// telefon
		$phone = $domXPath->query('//input[@name="form_admin_telefon"]/@value');
		if ($phone->length == 0) {
			$phone = 0;
		} elseif ($phone->length == 1) {
			$phone = $phone->item(0)->nodeValue;
		} else {
			return false;
		}
		// email
		$email = $domXPath->query('//input[@name="form_admin_email"]/@value');
		if ($email->length == 0) {
			$email = '';
		} elseif ($email->length == 1) {
			$email = $email->item(0)->nodeValue;
		} else {
			return false;
		}
		// sleva
		$discount = $domXPath->query('//input[@name="form_admin_sleva"]/@value');
		if ($discount->length == 0) {
			$discount = 0;
		} elseif ($discount->length == 1) {
			$discount = $discount->item(0)->nodeValue;
		} else {
			return false;
		}
		// heslo
		$password = $domXPath->query('//input[@name="form_admin_heslo"]/@value');
		if ($password->length != 1) {
			return false;
		}
		$password = $password->item(0)->nodeValue;
		// newsletter
		$newsletter = $domXPath->query('//input[@name="form_admin_zasilat_novinky"]/@checked');
		if ($newsletter->length == 0) {
			$newsletter = false;
		} elseif ($newsletter->length == 1) {
			$newsletter = ($newsletter->item(0)->nodeValue == 'checked');
		} else {
			return false;
		}
		// login
		$login = $domXPath->query('//table[@class="tabulkaedit"]//tr[16]/td[2]');
		if ($login->length != 1) {
			return false;
		}
		$login = $login->item(0)->nodeValue;
		// pocet prihlaseni
		$login_count = $domXPath->query('//table[@class="tabulkaedit"]//tr[17]/td[2]');
		if ($login_count->length != 1) {
			return false;
		}
		$login_count = $login_count->item(0)->nodeValue;
		// posledni prihlaseni
		$last_login = $domXPath->query('//table[@class="tabulkaedit"]//tr[18]/td[2]');
		if ($last_login->length != 1) {
			return false;
		}
		$last_login = $last_login->item(0)->nodeValue;
		$last_login = cz2db_datetime($last_login);
		
		return compact('active', 'price_category', 'company_name', 'company_ico', 'company_dic', 'name', 'street', 'city', 'zip', 'state', 'phone', 'email', 'discount', 'password', 'newsletter', 'login', 'login_count', 'last_login');
	}
}
?>