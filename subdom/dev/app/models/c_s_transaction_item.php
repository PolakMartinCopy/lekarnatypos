<?php 
class CSTransactionItem extends AppModel {
	var $name = 'CSTransactionItem';

	var $actsAs = array('Containable');
	
	var $belongsTo = array('CSProduct', 'CSInvoice', 'CSCreditNote', 'CSStoring');
	
	var $validate = array(
		'c_s_product_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název zboží'
			)	
		),
		'quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte množství zboží'
			)
		),
		'price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu zboží'
			)
		)
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function beforeValidate() {
		$tax_class['TaxClass']['value'] = 15;
		if (isset($this->data['CSTransactionItem']['price'])) {
			// nahrazeni desetinne carky za tecku v cene
			$this->data['CSTransactionItem']['price'] = str_replace(',', '.', $this->data['CSTransactionItem']['price']);
			// najdu danovou tridu pro produkt
			if (isset($this->data['CSTransactionItem']['c_s_product_id']) && !empty($this->data['CSTransactionItem']['c_s_product_id'])) {
				// najdu si produkt, abych u nej mohl menit ceny a mnozstvi
				$tax_class = $this->CSProduct->find('first', array(
					'conditions' => array('CSProduct.id' => $this->data['CSTransactionItem']['c_s_product_id']),
					'contain' => array('TaxClass'),
					'fields' => array('TaxClass.id', 'TaxClass.value')
				));
			}
		}
		$this->data['CSTransactionItem']['price_vat'] = $this->data['CSTransactionItem']['price'] + ($this->data['CSTransactionItem']['price'] * $tax_class['TaxClass']['value'] / 100);
		return true;
	}
	
	function afterSave() {
		if (isset($this->data['CSTransactionItem']['c_s_product_id']) && !empty($this->data['CSTransactionItem']['c_s_product_id'])) {
			// najdu si produkt, abych u nej mohl menit ceny a mnozstvi
			$c_s_product = $this->CSProduct->find('first', array(
				'conditions' => array('CSProduct.id' => $this->data['CSTransactionItem']['c_s_product_id']),
				'contain' => array(),
				'fields' => array('CSProduct.id', 'CSProduct.store_price', 'CSProduct.quantity')
			));
			
			if (!empty($c_s_product)) {
				// pokud ukladam naskladneni
				if (isset($this->data['CSTransactionItem']['c_s_storing_id'])) {
					// chci u produktu prepocitat mnozstvi a skladovou cenu
					$quantity = $c_s_product['CSProduct']['quantity'] + $this->data['CSTransactionItem']['quantity'];
					$store_price = (($c_s_product['CSProduct']['store_price'] * $c_s_product['CSProduct']['quantity']) + ($this->data['CSTransactionItem']['price'] * $this->data['CSTransactionItem']['quantity'])) / $quantity;
						
					$c_s_product['CSProduct']['quantity'] = $quantity;
					$c_s_product['CSProduct']['store_price'] = $store_price;
				// pokud ukladam fakturu
				} elseif (isset($this->data['CSTransactionItem']['c_s_invoice_id'])) {
					// chci prepocitat mnozstvi
					$quantity = $c_s_product['CSProduct']['quantity'] - $this->data['CSTransactionItem']['quantity'];
					$c_s_product['CSProduct']['quantity'] = $quantity;
				} elseif (isset($this->data['CSTransactionItem']['c_s_credit_note_id'])) {
					// chci prepocitat mnozstvi
					$quantity = $c_s_product['CSProduct']['quantity'] + $this->data['CSTransactionItem']['quantity'];
					$c_s_product['CSProduct']['quantity'] = $quantity;
				}
				$this->CSProduct->save($c_s_product);
			}
		}
		
		$this->active[] = $this->id;
	}
	
	// musim si zapamatovat, co mazu, abych to mohl po smazani odecist ze skladu
	function beforeDelete() {
		$this->deleted = $this->find('first', array(
			'conditions' => array('CSTransactionItem.id' => $this->id),
			'contain' => array(
				'CSProduct' => array(
					'fields' => array('CSProduct.id', 'CSProduct.quantity', 'CSProduct.store_price'),
				)
			)
		));
		
		return true;
	}
	
	function afterDelete() {
		// inicializace
		$quantity = 0;
		$store_price = 0;

		// pokud mazu naskladneni
		if (isset($this->deleted['CSTransactionItem']['c_s_storing_id']) && $this->deleted['CSTransactionItem']['c_s_storing_id']) {
			if (isset($this->deleted['CSTransactionItem']['c_s_product_id'])) {
				// ze skladu odectu, co jsem smazal
				$quantity = $this->deleted['CSProduct']['quantity'] - $this->deleted['CSTransactionItem']['quantity'];
				if ($quantity != 0) {
					$store_price = (($this->deleted['CSProduct']['store_price'] * $this->deleted['CSProduct']['quantity']) - ($this->deleted['CSTransactionItem']['price'] * $this->deleted['CSTransactionItem']['quantity'])) / $quantity;
				}
			}
		// pokud mazu polozku z faktury
		} elseif (isset($this->deleted['CSTransactionItem']['c_s_invoice_id']) && $this->deleted['CSTransactionItem']['c_s_invoice_id']) {
			if (isset($this->deleted['CSTransactionItem']['c_s_product_id'])) {
				// do skladu opet prictu, co bylo na fakture
				$quantity = $this->deleted['CSProduct']['quantity'] + $this->deleted['CSTransactionItem']['quantity'];
				$store_price = $this->deleted['CSProduct']['store_price'];
			}
		// mazu polozku z dobropisu
		} elseif (isset($this->deleted['CSTransactionItem']['c_s_credit_note_id']) && $this->deleted['CSTransactionItem']['c_s_credit_note_id']) {
			if (isset($this->deleted['CSTransactionItem']['c_s_product_id'])) {
				// ze skladu odectu, co bylo na dobropisu
				$quantity = $this->deleted['CSProduct']['quantity'] - $this->deleted['CSTransactionItem']['quantity'];
				$store_price = $this->deleted['CSProduct']['store_price'];
			}
		}
			
		if (isset($this->deleted['CSTransactionItem']['c_s_product_id'])) {
			$product = array(
				'CSProduct' => array(
					'id' => $this->deleted['CSProduct']['id'],
					'store_price' => $store_price,
					'quantity' => $quantity
				)
			);
				
			return $this->CSProduct->save($product);
		}
		return true;
	}
}
?>