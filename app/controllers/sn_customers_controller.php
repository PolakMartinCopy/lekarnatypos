<?php
class SnCustomersController extends AppController {
	var $name = 'SnCustomers';
	
	function parse($count = 10) {
		$url_base = 'http://www.sportnutrition.cz/admin/uzivatele:5/upravit/';
		// zjistim sn_id posledniho vyparsovaneho zakaznika
		$last_sn_parsed = $this->SnCustomer->find('first', array(
			'contain' => array(),
			'fields' => array('SnCustomer.id'),
			'order' => array('SnCustomer.id' => 'desc')	
		));
		
		if (!empty($last_sn_parsed)) {
			// prihlasim se do administrace SN
			if ($this->SnCustomer->sn_connect()) {
				// nastavim si id dalsiho zakaznika, ktereho chci vyparsovat
				$customer_to_parse = $last_sn_parsed['SnCustomer']['id'] + 1;
				// budu parsovat po $count zakaznicich
				for ($i = 0; $i < $count; $i++) {
					// sestavim url s detailem zakaznika pro stazeni
					$sn_id = $customer_to_parse + $i;
					$url = $url_base . $sn_id;
					// stahnu stranku
					$result = $this->SnCustomer->sn_download($url);

					// vyparsuju data
					$data = $this->SnCustomer->parse($result);

					// preskladam si stazena data
					if ($data['name']) {
						$data = array('SnCustomer' => $data);
						// idcko zakaznika necham shodne s idckem na sportnutrition
						$data['SnCustomer']['id'] = $sn_id;
						// ulozim data
						if (!$this->SnCustomer->save($data)) {
							debug($data);
							die('neulozilo se');
						} else {
							debug('Zakaznik ' . $sn_id . ' byl natazen.');
						}
					} else {
						debug('Zakaznik ' . $sn_id . ' neexistuje');
					}
				}
				$this->SnCustomer->sn_disconnect();
			}
		}

		die();
	}
	
	/*
	 * transformace zakazniku z tabulky ze sportnutrition do naseho systemu
	 */
	function transform($count = 10) {
		// vytahnu si $count zakazniku ze SN, ktere jeste nemam nahrane v NS (transformed -> false), ale jsou jiz normalizovani (normalized -> true)
		$customers = $this->SnCustomer->find('all', array(
			'conditions' => array('SnCustomer.transformed' => false),//, 'SnCustomer.normalized' => true),
			'contain' => array(),
			'limit' => $count
		));

		// napojim model se zakazniky
		App::import('Model', 'Customer');
		$this->Customer = new Customer;
		// vytahnu si typy uzivatelu
		$customer_types = $this->Customer->CustomerType->find('all', array(
			'contain' => array(),
			'fields' => array('CustomerType.id', 'CustomerType.sn_name')
		));
		// preskladam je, aby indexem pole byl sn nazev typu zakaznika
		$customer_types = Set::combine($customer_types, '{n}.CustomerType.sn_name', '{n}.CustomerType.id');

		// prochazim zakazniky ze SN
		foreach ($customers as $customer) {
			$c_dataSource = $this->Customer->getDataSource();
			$c_dataSource->begin($this->Customer);
			try {
				// chci zachovat unikatnost emailove adresy a proto se podivam, jestli uz nemam v NS zaznam s danym emailem
				if ($this->Customer->hasAny(array('Customer.email' => $customer['SnCustomer']['email']))) {
					// pokud jsem takovy zaznam nasel, znamena to, ze pro danou emailovou adresu mam pristupove udaje ve spojene db i nove v sn
					// nactu pristupove udaje ve spojene db
					$customer_login = $this->Customer->CustomerLogin->find('first', array(
						'conditions' => array('Customer.email' => $customer['SnCustomer']['email']),
						'contain' => array('Customer'),
						'fields' => array('CustomerLogin.id', 'CustomerLogin.login', 'CustomerLogin.password', 'Customer.id')
					));
					if (!($customer_login['CustomerLogin']['login'] == $customer['SnCustomer']['login'] && $customer_login['CustomerLogin']['password'] == md5($customer['SnCustomer']['password']))) {
						// pokud jsou pristupove udaje ruzne
						if (!empty($customer['SnCustomer']['login'])) {
							// a pokud neni login zakaznika ze SN prazdny, musim danemu zakaznikovi pridat pristupove udaje ze SN (zakaznik tak bude mit nekolikery pristupove udaje)
							$new_customer_login = array(
								'CustomerLogin' => array(
									'customer_id' => $customer_login['Customer']['id'],
									'login' => $customer['SnCustomer']['login'],
									'password' => md5($customer['SnCustomer']['password'])
								)
							);
							$this->Customer->CustomerLogin->create();
							if (!$this->Customer->CustomerLogin->save($new_customer_login)) {
								debug($new_customer_login);
								throw new Exception('CustomerLogin se nepodarilo pridat k zakaznikovi, ktery jiz existuje z NS');
							}
						}
					}

					// u zakazniku kteri jsou v NS a SN si musim zapamatovat jednoznacny zdroj (SN i NS), abych ho mohl pri prvnim prihlaseni upozornit,
					// aby si zkontrolovat kontaktni udaje (adresa, telefon atd)
					// zaroven nastavim typ zakaznika podle typu na SN, protoze na NS mam pouze zakazniky se slevou pro prihlasene
					$update_customer = array(
						'Customer' => array(
							'id' => $customer_login['Customer']['id'],
							'registration_source' => 'sportnutrition i nutrishop',
							'customer_type_id' => ($customer['SnCustomer']['price_category'] ? $customer_types[$customer['SnCustomer']['price_category']] : null),
							'login_count' => $customer['SnCustomer']['login_count'],
							'last_login' => $customer['SnCustomer']['last_login'],
							'sn_id' => $customer['SnCustomer']['id']
						)	
					);

					if (!$this->Customer->save($update_customer)) {
						throw new Exception('Nepodarilo se updatovat zdroj a typ zakaznika, ktery jiz existuje z NS');
					}
				} else {
					// pokud takovy zaznam nemam, znamena to, ze zakaznik je pro NS novy a muzu ho bez problemu vlozit
					$customer_name = '';
					$customer_first_name = '';
					$customer_last_name = '';
					// sestavim jmeno zakaznika
					if (!empty($customer['SnCustomer']['name'])) {
						$customer_name = explode(' ', $customer['SnCustomer']['name']);
						if (count($customer_name) > 1) {
							$customer_first_name = $customer_name[0];
							unset($customer_name[0]);
							$customer_last_name = implode(' ', $customer_name);
						} else {
							$customer_last_name = $customer_name[0];
						}
					}
					// udaje o ulici a cisle popisnem
					$customer_street = $customer['SnCustomer']['street'];
					$customer_street_no = '';
					if (!empty($customer['SnCustomer']['street'])) {
						if (preg_match('/(.*) (\d+(?:\/\d+)?(?:a-zA-Z)?)/', $customer_street, $matches)) {
							$customer_street = $matches[1];
							$customer_street_no = $matches[2];
						}
					}
					// poskladam zakaznika
					$new_customer = array(
						'Customer' => array(
							'first_name' => $customer_first_name,
							'last_name' => $customer_last_name,
							'phone' => $customer['SnCustomer']['phone'],
							'email' => $customer['SnCustomer']['email'],
							'company_name' => $customer['SnCustomer']['company_name'],
							'company_ico' => $customer['SnCustomer']['company_ico'],
							'company_dic' => $customer['SnCustomer']['company_dic'],
							'registration_source' => 'sportnutrition',
							'confirmed' => true,
							'newsletter' => $customer['SnCustomer']['newsletter'],
							'customer_type_id' => ($customer['SnCustomer']['price_category'] ? $customer_types[$customer['SnCustomer']['price_category']] : null),
							'discount' => $customer['SnCustomer']['discount'],
							'login_count' => $customer['SnCustomer']['login_count'],
							'last_login' => $customer['SnCustomer']['last_login'],
							'sn_id' => $customer['SnCustomer']['id']
						),
						'CustomerLogin' => array(
							array(
								'login' => $customer['SnCustomer']['login'],
								'password' => md5($customer['SnCustomer']['password'])
							)
						),
						'Address' => array(
							array(
								'name' => $customer['SnCustomer']['name'],
								'street' => $customer_street,
								'street_no' => $customer_street_no,
								'zip' => $customer['SnCustomer']['zip'],
								'city' => $customer['SnCustomer']['city'],
								'state' => $customer['SnCustomer']['state'],
								'is_main' => true,
								'type' => 'f'
							),
							array(
								'name' => $customer['SnCustomer']['name'],
								'street' => $customer_street,
								'street_no' => $customer_street_no,
								'zip' => $customer['SnCustomer']['zip'],
								'city' => $customer['SnCustomer']['city'],
								'state' => $customer['SnCustomer']['state'],
								'is_main' => false,
								'type' => 'd'
							)
						)
					);

					// pokud ulozim customera
					$this->Customer->create();
					$this->Customer->Address->create();
					$this->Customer->CustomerLogin->create();
					if (!$this->Customer->saveAll($new_customer, array('validate' => false))) {
						throw new Exception('Nepodarilo se vlozit zakaznika ze SN');
					}
				}
				
				// oznacim sn_customera, ze transformed = true
				$customer['SnCustomer']['transformed'] = true;
				if (!$this->SnCustomer->save($customer)) {
					throw new Exception('Nepodarilo se zakaznika oznacit jako transformovaneho');
				}
			} catch (Exception $e) {
				$c_dataSource->rollback($this->Customer);
				debug($customer);
				echo 'SELHALO ULOZENI ZAKAZNIKA' . "<br/>\n";
				echo $e->getMessage() . "<br/>\n";
			}
			$c_dataSource->commit($this->Customer);
		}

		die();
	}
	
	/**
	 * v tabulce zakazniku ze sportnutrition neni email unikatni udaj. My ho ale unikatni potrebujeme (obnoveni hesla atd). Vetsinou i pri vicenasobnych vyskytech emailove adresy u vice
	 * zaznamu existuje jeden, ktery je hlavni (ma login, login_count vyssi nez 0 atd). chceme rozlisit hlavni ucet a ucty ostatni (abych pripadne mohl nahrat pouze hlavni ucty) 
	 * 
	 * do toho v ramci jedne tridy zaznamu (se stejnym emailem) existuji zaznamy napr. s ruznym tel. cislem
	 */
	function normalize($count = 100) {
		// zjistim emaily, pro ktere existuje vice uctu
		$emails = $this->SnCustomer->find('all', array(
			'joins' => array(
				array(
					'table' => 'sn_customers',
					'alias' => 'SnCustomer2',
					'type' => 'inner',
					'conditions' => array('SnCustomer.email = SnCustomer2.email')
				)
			),
			'conditions' => array(
				// nechci, aby se mi sparovali stejni zakaznici
				'SnCustomer.id != SnCustomer2.id',
				// zaroven chci parovat jen zakazniky s emailem
				'SnCustomer.email !=' => '',
				// a hledam jen v nenormalizovanych zakaznicich (rychlejsi)
				'SnCustomer.normalized' => false
			),
			'contain' => array(),
			'fields' => array('DISTINCT SnCustomer.email'),
			'limit' => $count
		));

		foreach ($emails as $email) {
			// pro kazdy z tech emailu zjistim zaznam, ktery ma neprazdny login a pocet prihlaseni vyssi nez 0
			$customer = $this->SnCustomer->find('all', array(
				'conditions' => array(
					'SnCustomer.login !=' => '',
					'SnCustomer.email' => $email['SnCustomer']['email']
				),
				'contain' => array(),
				'fields' => array('SnCustomer.id', 'SnCustomer.email', 'SnCustomer.login', 'SnCustomer.login_count', 'SnCustomer.last_login'),
				'order' => array(
					'SnCustomer.login_count' => 'desc',
					'SnCustomer.last_login' => 'desc'
				)
			));

			$pivot = null;

			// jestlize zadny zaznam s neprazdnym loginem, pak proste vyberu prvni jakykoli zaznam jako pivota a ostatni smazu
			if (empty($customer)) {
				$pivot = $this->SnCustomer->find('first', array(
					'conditions' => array('SnCustomer.email' => $email['SnCustomer']['email']),
					'contain' => array(),
					'fields' => array('SnCustomer.id', 'SnCustomer.email')
				));
			} elseif (count($customer) == 1) {
				// zaznam je pivot -> ostatni zaznamy s timto emailem smazu
				$pivot = $customer[0];
			} else {
				// zaznamu s loginem je vice, zkontroluju, jestli ma z tech zaznamu pouze jeden nenulovy login_count a pokud ano, je to pivot
				$pivot = null;
				foreach ($customer as $item) {
					if ($item['SnCustomer']['login_count']) {
						if (!$pivot) {
							$pivot = $item;
						} else {
							$pivot = null;
							break;
						}
					}
				}
			}

			if ($pivot) {
				// smazu vsechny zaznamy s danym emailem, ktere nejsou pivot
				$conditions = array(
					'SnCustomer.email' => $pivot['SnCustomer']['email'],
					'SnCustomer.id !=' => $pivot['SnCustomer']['id']
				);
				$this->SnCustomer->deleteAll($conditions);
				
				$pivot['SnCustomer']['normalized'] = true;
				$this->SnCustomer->save($pivot);
			} else {
				// pokud existuje vice zaznamu s loginem a bud je vice s nenulovym login_countem, nebo maji vsichni nulovy login_count
				// vypisu ucty, kde nejsem schopen jednoduse rozhodnout, ktery ucet je pivot
				debug('NEJSEM SCHOPNY ROZPOZNAT PIVOTA');
				debug($customer);
			}
		}
		
		die();
	}
	
	// test, jestli v customers a sn_customers existuje nejaka duplicita na loginu a heslu
	function check() {
		$query = '
		SELECT *
		FROM (
			SELECT CustomerLogin.login, CustomerLogin.password
			FROM customer_logins AS CustomerLogin
			UNION ALL
			SELECT SnCustomer.login, MD5(SnCustomer.password)
			FROM sn_customers AS SnCustomer
		) AS CustomerTogether
		WHERE CustomerTogether.login != ""
		GROUP BY CustomerTogether.login, CustomerTogether.password
		HAVING COUNT(*) > 1
		LIMIT 10
		';
		$customers = $this->SnCustomer->query($query);
		debug($customers); die();
	}
}
?>