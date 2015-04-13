<?php 
class NsCustomersController extends AppController {
	var $name = 'NsCustomers';
	
	function transform() {
		// zjistim uzivatele, kteri nejsou naparovani na uzivatele ve spolecne tabulce
		// chci id sparovanych uzivatelu
		App::import('Model', 'Customer');
		$this->Customer = new Customer;
		// nejprve chci uzivatele, kteri jsou naparovani na nutrishop
		$paired_customers = $this->Customer->find('all', array(
			'conditions' => array('Customer.ns_id IS NOT NULL'),
			'contain' => array(),
			'fields' => array('Customer.ns_id')
		));

		$paired_customers = Set::extract('/Customer/ns_id', $paired_customers);
		// pak z nutrishpu vytahnu vsechny ostatni
		$unpaired_ns_customers = $this->NsCustomer->find('all', array(
			'conditions' => array('NsCustomer.id NOT IN (' . implode(',', $paired_customers) . ')'),
			'contain' => array(
				'NsAddress'
				// nema smysl tady tahat i objednavky, protoze hloubka stromu je uz moc velka na saveAll
			),
		));

		foreach ($unpaired_ns_customers as $ns_customer) {
			// odnastavim idcka a vztahy z puvodni ns db
			$ns_customer['NsCustomer']['ns_id'] = $ns_customer['NsCustomer']['id'];
			$ns_customer['NsCustomer']['customer_type_id'] = 1;
			unset($ns_customer['NsCustomer']['id']);
			// data u ns uzivatele musim prevest z cp1250 do utf-8
			foreach ($ns_customer['NsCustomer'] as &$info) {
				$info = iconv('cp1250', 'utf-8', $info);
			}
			foreach ($ns_customer['NsAddress'] as &$address) {
				unset($address['id']);
				unset($address['customer_id']);
				foreach ($address as &$info) {
					$info = iconv('cp1250', 'utf-8', $info);
				}
			}

			// podivam se, jestli v systemu mam uzivatele s danou emailovou adresou, ktery neni naparovany na ns
			if ($this->Customer->hasAny(array('Customer.email' => $ns_customer['NsCustomer']['email'], 'Customer.ns_id IS NULL'))) {
				// znamena, ze jsem ho nahral
				$customer = $this->Customer->find('first', array(
					'conditions' => array(
						'Customer.email' => $ns_customer['NsCustomer']['email'],
						'Customer.ns_id IS NULL'
					),
					'contain' => array(),
					'fields' => array('Customer.id')
				));

				$customer['Customer']['ns_id'] = $ns_customer['NsCustomer']['ns_id'];
				$this->Customer->save($customer);
				
				continue;
			}
			
			// poskladam data pro vlozeni do spolecne tabulky customeru pro sn i ns
			$customer = array(
				'Customer' => $ns_customer['NsCustomer'],
				'CustomerLogin' => array(
					'login' => $ns_customer['NsCustomer']['login'],
					'password' => $ns_customer['NsCustomer']['password']
				),
				'Address' => $ns_customer['NsAddress']
			);

			$this->Customer->create();
			$this->Customer->Address->create();
			if (!$this->Customer->save($customer)) {
				debug($ns_customer);
				debug($customer);
				echo 'NEPODARILO SE VLOZIT UZIVATELE';
			}
		}
		die();
	}
	
}
?>