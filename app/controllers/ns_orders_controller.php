<?php 
class NsOrdersController extends AppController {
	var $name = 'NsOrders';
	
	function transform() {
		// zjistim objednavky, ktere nejsou naparovane na objednavky ve spolecne tabulce
		// chci id sparovanych objednavek
		App::import('Model', 'Order');
		$this->Order = new Order;
		// nejprve chci objednavky, kteri jsou naparovani na nutrishop
		$paired_orders = $this->Order->find('all', array(
			'conditions' => array('Order.ns_id IS NOT NULL'),
			'contain' => array(),
			'fields' => array('Order.ns_id')
		));
		$paired_orders = Set::extract('/Order/ns_id', $paired_orders);
		// pak z nutrishpu vytahnu vsechny ostatni
		$unpaired_ns_orders = $this->NsOrder->find('all', array(
			'conditions' => array('NsOrder.id NOT IN (' . implode(',', $paired_orders) . ')'),
			'contain' => array(
				'NsOrderedProduct' => array(
					'NsOrderedProductsAttribute'
				)
			),
		));

		foreach ($unpaired_ns_orders as $ns_order) {
			// odnastavim idcka a vztahy z puvodni ns db
			$ns_order['NsOrder']['ns_id'] = $ns_order['NsOrder']['id'];
			unset($ns_order['NsOrder']['id']);
			// data u ns objednavky musim prevest z cp1250 do utf-8
			foreach ($ns_order['NsOrder'] as &$info) {
				$info = iconv('cp1250', 'utf-8', $info);
			}
			foreach ($ns_order['NsOrderedProduct'] as &$ordered_product) {
				unset($ordered_product['id']);
				unset($ordered_product['order_id']);
				foreach ($ordered_product['NsOrderedProductsAttribute'] as &$opa) {
					unset($opa['id']);
					unset($opa['ordered_product_id']);
				}
			}
			
			// ZACATEK TRANSAKCE
			$c_dataSource = $this->Order->getDataSource();
			$c_dataSource->begin($this->Order);
			try {
				$order_save['Order'] = $ns_order['NsOrder'];
				// k objednavce najdu uzivatele
				$customer = $this->Order->Customer->find('first', array(
					'conditions' => array('Customer.ns_id' => $order_save['Order']['customer_id']),
					'contain' => array(),
					'fields' => array('Customer.id')	
				));
				if (empty($customer)) {
					debug($order);
					throw new Exception('NEEXISTUJE UZIVATEL S TIMTO ID');
				}
				$order['Order']['customer_id'] = $customer['Customer']['id'];
				$this->Order->create();
				// nemuzu ulozit vsechno saveAllem, protoze hloubka je moc velka, proto jedu pres transakci, ulozim si samotnou objednavku, zjistim jeji idcko a pak navkladam saveallama produkty v objednavce
				if (!$this->Order->save($order_save)) {
					debug($ns_order);
					throw new Exception('NEPODARILO SE ULOZIT OBJEDNAVKU');
				}
				$order_id = $this->Order->id;
				foreach ($ns_order['NsOrderedProduct'] as $ordered_product) {
					$op_save = array(
						'OrderedProduct' => $ordered_product,
						'OrderedProductsAttribute' => $ordered_product['NsOrderedProductsAttribute']
					);
					$op_save['OrderedProduct']['order_id'] = $order_id;
					
					$this->Order->OrderedProduct->create();
					$this->Order->OrderedProduct->OrderedProductsAttribute->create();
						
					if (!$this->Order->OrderedProduct->saveAll($op_save)) {
						debug($op_save);
						throw new Exception('NEPODARILO SE ULOZIT POLOZKY OBJEDNAVKY');
					}
				}
			} catch (Exception $e) {
				// nekde je chyba
				$c_dataSource->rollback($this->Order);
				// vypisu hlasku
				echo 'SELHALO ULOZENI OBJEDNAVKY' . "<br/>\n";
				echo $e->getMessage() . "<br/>\n";
			}
					
			// KONEC TRANSAKCE
			$c_dataSource->commit($this->Order);
		}
		die();
	}
	
	/*
	 * updatuje status objednavek (v podminkach je omezeni na objednavky vytvorene v roce 2013)
	 */
	function update_status() {
		$ns_orders = $this->NsOrder->find('all', array(
			'conditions' => array('NsOrder.created >' => '2013-01-01 00:00:00'),
			'contain' => array(),
			'fields' => array('NsOrder.id', 'NsOrder.status_id')
		));
		
		App::import('Model', 'Order');
		$this->Order = new Order;
		$orders = array();
		
		foreach ($ns_orders as $ns_order) {
			try {
				$order = $this->Order->find('first', array(
					'conditions' => array('Order.ns_id' => $ns_order['NsOrder']['id']),
					'contain' => array(),
					'fields' => array('Order.id')	
				));
				
				if (empty($order)) {
					debug($ns_order);
					throw new Exception('K OBJEDNAVCE Z NS NEEXISTUJE EKVIVALENT');
				}
				
				$orders[] = array(
					'id' => $order['Order']['id'],
					'status_id' => $ns_order['NsOrder']['status_id']
				);
			} catch (Exception $e) {
				debug($e->getMessage());
			}
		}
		
		if (!$this->Order->saveAll($orders)) {
			debug($orders);
			echo 'NEPODARILO SE UPDATOVAT STAV U NS OBJEDNAVEK';
		}
		die();
	}
}
?>