<?php
class SnOrderItem extends AppModel {
	var $name = 'SnOrderItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('SnOrder');
	
	function parse($order_segment) {
		preg_match('/<table class=\'tabulka\'.*<\/table>/', $order_segment, $order_items_table);
		if (empty($order_items_table)) {
			debug($order_segment);
			die('ERROR: Nevyparsovala se tabulka s polozkami objednavky');
		} else {
			$order_items_table = $order_items_table[0];

			$dom = new DOMDocument('1.0');
			$dom->formatOutput = true;
			$dom->preserveWhiteSpace = false;
			libxml_use_internal_errors(true);
			if (!$dom->loadHTML('<?xml encoding="UTF-8">' . $order_items_table)) {
				debug($order_items_table);
				die('nenatahla se tabulka do stromu');
			}
			$domXPath = new DOMXPath($dom);
			$quantities = $domXPath->query('//table/tr/td[1]');
			$names = $domXPath->query('//table/tr/td[2]');
			$prices = $domXPath->query('//table/tr/td[3]');
			$prices_vat = $domXPath->query('//table/tr/td[4]');

			if ($quantities->length == $names->length &&  $quantities->length == $prices->length && $quantities->length == $prices_vat->length) {
				$order_items = array();
				for ($i = 0; $i < $names->length; $i++) {
					$product_id = null;
					$name_html_content = $names->item($i)->ownerDocument->saveXML($names->item($i));
					if (preg_match('/<a href=".*:(\d+)(?:\/)?"/', $name_html_content, $product_id)) {
						$product_id = $product_id[1];
					}
					$product_id = ($product_id ? $product_id : null);
					$order_items[] = array(
						'quantity' => str_replace('x', '', $quantities->item($i)->nodeValue),
						'name' => $names->item($i)->nodeValue,
						'product_id' => $product_id,
						'price' => $prices->item($i)->nodeValue,
						'price_vat' => $prices_vat->item($i)->nodeValue
					);
				}
				return $order_items;
			} else {
				debug($order_segment);
				debug($quantities->length);
				debug($names->length);
				debug($prices->length);
				debug($prices_vat->length);
				debug($product_hrefs->length);
				die('nesedi pocty polozek ve vyparsovanych detailech polozek objednavky');
			}
		}
		return false;
	}
}