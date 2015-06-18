<?php
class CustomersController extends AppController {
	var $name = 'Customers';

	var $helpers = array('Form', 'Html');

	function beforeFilter(){
		parent::beforeFilter();
		// testuju zda je uzivatel prihlasen
		// pokud neni, presmeruju na stranku
		// s prihlasovacim formularem
		
		$allowed_actions = array(
			'add',
			'order_personal_info',
			'login',
			'password',
			'confirm_hash',
			'import',
			'repair'
		);
		
		if ( !$this->Session->check('Customer') && !in_array($this->params['action'], $allowed_actions) && !eregi("admin_", $this->params['action'])  ){
			$this->Session->setFlash('Pro zobrazení této stránky se musíte přihlásit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'login'), null, true);
		}
	}
	
	function admin_index() {
		$customers = null;
		
		if (isset($this->params['named']['query'])) {
			$this->data['Customer']['query'] = $this->params['named']['query'];
		}
		if (isset($this->params['named']['orders_amount'])) {
			$this->data['Customer']['orders_amount'] = $this->params['named']['orders_amount'];
		}
		

		$conditions = array('Customer.active' => true);
			
		if (isset($this->data['Customer']['query']) && !empty($this->data['Customer']['query'])) {
			$conditions[] = array(
				'OR' => array(
					array('Customer.first_name LIKE "%%' . $this->data['Customer']['query'] . '%%"'),
					array('Customer.last_name LIKE "%%' . $this->data['Customer']['query'] . '%%"'),
					array('Customer.email LIKE "%%' . $this->data['Customer']['query'] . '%%"'),
					array('Customer.phone LIKE "%%' . $this->data['Customer']['query'] . '%%"')
				)	
			);
		}
			
		$this->Customer->virtualFields['orders_amount'] = 'SUM(Order.subtotal_with_dph + Order.shipping_cost)';
		$this->Customer->virtualFields['orders_count'] = 'COUNT(*)';
			
		$group = 'Customer.id';
		if (isset($this->data['Customer']['orders_amount']) && !empty($this->data['Customer']['orders_amount'])) {
			$group .= ' HAVING (' . $this->Customer->virtualFields['orders_amount'] . ' >= ' . $this->data['Customer']['orders_amount'] . ')';
		}
		
			
		$paginate = array(
			'conditions' => $conditions,
			'contain' => array(
				'CustomerType' => array(
					'fields' => array('CustomerType.id', 'CustomerType.name')
				),
				'Address' => array(
					'conditions' => array('Address.type' => 'f'),
					'fields' => array(
						'Address.id',
						'Address.street',
						'Address.street_no',
						'Address.city',
						'Address.zip'
					)
				)
			),
			'joins' => array(
				array(
					'table' => 'orders',
					'alias' => 'Order',
					'type' => 'LEFT',
					'conditions' => array('Customer.id = Order.customer_id')
				)
			),
			'group' => array($group),
			'fields' => array(
				'Customer.id',
				'Customer.company_name',
				'Customer.name',
				'Customer.email',
				'Customer.phone',
				'Customer.login_count',
				'Customer.login_date',
				'Customer.orders_amount',
				'Customer.orders_count'
			),
			'order' => array('Customer.name' => 'asc')
		);
		
		$this->paginate = array_merge($paginate, $this->paginate);
		
		// pokud chci vypsat csv
		if (isset($this->data['Customer']['csv']) && $this->data['Customer']['csv'] == true) {
			// chci tam vsechny vysledky (ne strankovani)
			unset($this->paginate['limit']);
			$this->paginate['show'] = 'all';
		}

		$customers = $this->paginate();
		
		unset($this->Customer->virtualFields['orders_amount']);
		unset($this->Customer->virtualFields['orders_count']);

		$this->set('customers', $customers);
		
		$this->layout = REDESIGN_PATH . 'admin';
		// pokud chci vypsat csv
		if (isset($this->data['Customer']['csv']) && $this->data['Customer']['csv'] == true) {
			// nastaveim pohled
			$this->Customer->csv_export($customers);
			$this->redirect('/' . $this->Customer->export_file);
		}
	}
	
	function admin_view($id = null){
		if (!$id) {
			$this->Session->setFlash('Neznámý zákazník.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		$customer = $this->Customer->find('first', array(
			'conditions' => array(
				'Customer.id' => $id
			),
			'contain' => array(
				'Address' => array(
					'fields' => array('Address.id', 'Address.type', 'Address.name', 'Address.street', 'Address.street_no', 'Address.zip', 'Address.city', )
				),
				'Order' => array(
					'fields' => array('Order.id', 'Order.subtotal_with_dph', 'Order.shipping_cost')
				),
				'CustomerType' => array(
					'fields' => array('CustomerType.id', 'CustomerType.name')
				),
				'CustomerLogin' => array(
					'fields' => array('CustomerLogin.id', 'CustomerLogin.login')
				)
			),
			'fields' => array(
				'Customer.id',
				'Customer.first_name',
				'Customer.last_name',
				'Customer.created',
				'Customer.registration_source',
				'Customer.confirmed',
				'Customer.phone',
				'Customer.email',
				'Customer.newsletter',
				'Customer.company_name',
				'Customer.company_ico',
				'Customer.company_dic',
				'Customer.customer_type_id'
			)
		));
	
		if (isset($this->data)) {
			if ($this->Customer->saveAll($this->data)) {
				$this->Session->setFlash('Zákazník byl upraven', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Zákazníka se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $customer;
		}
	
		$customer_types = $this->Customer->CustomerType->find('list');
		$this->set('customer_types', $customer_types);
	
		$this->set('customer', $customer);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_newsletter_emails() {
		// mam zadany soubor s blacklistem?
		if (isset($this->data)) {
			// nahraju a zpracuju soubor s blacklistem
			if (is_uploaded_file($this->data['Customer']['newsletter_file']['tmp_name'])) {
				$content = file_get_contents($this->data['Customer']['newsletter_file']['tmp_name']);
				$emails = explode("\n", $content);
				// na prvni radku je nejakej text, kterej neni emailova adresa
				unset($emails[0]);
				$emails = array_map(function($item) {return trim($item);}, $emails);
				$customers = $this->Customer->find('all', array(
					'conditions' => array('Customer.email' => $emails, 'Customer.newsletter' => true),
					'contain' => array(),
					'fields' => array('Customer.id')
				));

				$customer_save = array();
				foreach ($customers as $customer) {
					$customer['Customer']['newsletter'] = false;
					$customer_save[] = $customer['Customer'];
				}
				if ($this->Customer->saveAll($customer_save)) {
					$this->Session->setFlash('Soubor s blacklistem byl zpracován', REDESIGN_PATH . 'flash_success');
				} else {
					$this->Session->setFlash('Soubor s blacklistem se nepodařilo zpracovat', REDESIGN_PATH . 'flash_failure');
				}
			} else {
				$this->Session->setFlash('Soubor s blacklistem se nepodařilo nahrát', REDESIGN_PATH . 'flash_failure');
			}
		}
		
		$emails = $this->Customer->find('all', array(
			'conditions' => array('Customer.newsletter' => true, 'Customer.active' => true),
			'contain' => array(),
			'fields' => array('Customer.id', 'Customer.email'),
			'order' => array('Customer.email' => 'asc')	
		));
		
		$this->set('emails', $emails);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if (isset($this->data)) {
			$address = $this->data['Address'][0];
			$address['type'] = 'f';
			$this->data['Address'][] = $address;
			
			if (array_key_exists('login', $this->data['CustomerLogin'][0]) && empty($this->data['CustomerLogin'][0]['login'])) {
				$this->data['CustomerLogin'][0]['login'] = $this->Customer->generateLogin($this->data['Customer']);
			}
			
			if (isset($this->data['CustomerLogin'][0]['password'])) {
				$this->data['CustomerLogin'][0]['password'] = md5($this->data['CustomerLogin'][0]['password']);
			}
			
			if ($this->Customer->saveAll($this->data)) {
				$this->Session->setFlash('Uživatel byl uložen.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'));
			} else {
				unset($this->data['CustomerLogin'][0]['password']);
				$this->Session->setFlash('Uivatele se nepodařilo uložit, opravte chyby ve formuláři a uložte jej prosím znovu.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data['Customer']['active'] = true;
			$this->data['Address'][0]['state'] = 'Česká republika';
			$this->data['Customer']['newsletter'] = true;
		}
		
		$customerTypes = $this->Customer->CustomerType->find('list');
		$this->set('customerTypes', $customerTypes);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_send_login($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý zákazník.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $id),
			'contain' => array('CustomerLogin')
		));
		
		if (empty($customer)) {
			$this->Session->setFlash('Neexistující zákazník.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}

		$password = $this->Customer->generatePassword($customer['Customer']);
		$customer_login = array();
		// zakaznik nema zadne pristupove udaje - nemelo by se stat
		if (empty($customer['CustomerLogin'])) {
			$login = $this->Customer->generateLogin($customer['Customer']);
			$customer_login = array(
				'CustomerLogin' => array(
					'customer_id' => $customer['Customer']['id'],
					'login' => $login,
					'password' => md5($password),
				)
			);
		} else {
			$login = $customer['CustomerLogin'][0]['login'];
			$customer_login['CustomerLogin'] = $customer['CustomerLogin'][0];
			$customer_login['CustomerLogin']['password'] = md5($password);
		}
		
		if ($this->Customer->CustomerLogin->save($customer_login)) {
			$customer_login['CustomerLogin']['password'] = $password;
			$customer['CustomerLogin'][0] = $customer_login['CustomerLogin'];
			if ($this->Customer->notify_account_created($customer)) {
				$this->Session->setFlash('Přístupové údaje byly odeslány.', REDESIGN_PATH . 'flash_success');
			} else {
				$this->Session->setFlash('Přístupové údaje se nepodařilo odeslat.', REDESIGN_PATH . 'flash_failure');
			}			
		} else {
			$this->Session->setFlash('Nepodařilo se uložit nové přístupové údaje.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'customers', 'action' => 'index'));
	}
	
	// soft delete
	function admin_delete($id = null){
		if ( empty($id) ){
			$this->Session->setFlash('Není definováno ID zákazníka, kterého chcete smazat!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}

		// nactu si data k zakaznikovi a smazu je
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $id),
			'contain' => array()
		));

		if ( empty($customer) ){
			$this->Session->setFlash('Zákazník, kterého chcete smazat, neexistuje', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		$customer['Customer']['active'] = false;
		
		// smazu zakaznika
		if ($this->Customer->save($customer)) {
			$this->Session->setFlash('Zákazník byl úspěšně deaktivován!', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Nepodailo se smazat záznam o zákazníkovi, zkuste to prosím znovu!', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'customers', 'action' => 'index'));
	}
	
	/**
	 * Seznam zákazníků registrovaných v obchodě.
	 * @param string $id - zacatecni pismeno jmena
	 */
	function admin_list($id = null){
		if ( !isset($id) ){
			$id = 'a';
		}
		
		$customers = $this->Customer->find('all', array(
			'conditions' => array("Customer.last_name LIKE '" . $id . "%%' OR Customer.last_name LIKE '" . strtoupper($id) . "%%' "),
			'recursive' => -1,
			'order' => 'Customer.last_name'
		));
		
		$count = count($customers);
		for ( $i = 0; $i < $count; $i++ ){
			// vytazeni objednavek zakaznika
			$customers[$i]['Customer']['orders'] = $this->Customer->Order->find('all', array(
				'conditions' => array('customer_id' => $customers[$i]['Customer']['id']),
				'fields' => array('id', 'subtotal_with_dph', 'shipping_cost'),
				'recursive' => -1
			));
			
			// vytazeni adres zakaznika
			$customers[$i]['Customer']['addresses'] = $this->Customer->Address->find('all', array(
				'conditions' => array('customer_id' => $customers[$i]['Customer']['id']),
				'fields' => array('id', 'name'),
				'recursive' => -1
			));
		}
		
		$this->set('customers', $customers);
		$this->set('id', $id);
		$this->set('alphabet', array(0 => 'a', 'á', 'b', 'c', 'č', 'd', 'ď', 'e', 'é', 'f', 'g', 'h', 'i', 'í', 'j', 'k', 'l', 'm', 'n', 'ň', 'o', 'ó', 'p', 'q', 'r', 'ř', 's', 'š', 't', 'ť', 'u', 'ú', 'v', 'w', 'x', 'y', 'z', 'ž'));
	}
	
	/*
	 * @description			Registrace noveho uzivatele do systemu.
	 */
	function add(){
		// kontrola, jestli se nesnazi o registraci, i kdyz je prihlaseny
		if ( $this->Session->check('Customer.id') ){
			$this->Session->setFlash('Jste již přihlášen(a) ke svému účtu, chcete-li zaregistrovat nový účet, nejprve se odhlašte.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
		}
		
		// nastavim si meta udaje
		$this->set('page_heading', 'Registrace nového účtu');
		$this->set('_title', 'registrace nového uživatele');
		$this->set('_description	', 'Zaregistrujte se a získáte přehled o výhodnějších cenách pro registrované uživatele.');
		$breadcrumbs = array(array('anchor' => 'Registrace účtu', 'href' => '/registrace'));
		$this->set('breadcrumbs', $breadcrumbs);
		
		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		
		// formular byl vyplnen
		if ( isset($this->data) ){
			// pro zakaznika si preddefinuju login a heslo
			$this->data['CustomerLogin'][0]['login'] = $this->Customer->generateLogin($this->data['Customer']);

			// kvuli notifikacnimu mailu potrebuju vedet nekryptovane heslo, ulozim si ho proto bokem
			$password_not_md5 = $this->Customer->generatePassword($this->data['Customer']);
			// do databaze musi jit heslo kryptovane
			$this->data['CustomerLogin'][0]['password'] = md5($password_not_md5); 

			$this->data['Customer']['confirmed'] = true;
			$this->data['Customer']['active'] = true;
			$this->data['Customer']['registration_source'] = 'eshop - registrace';
			
			if (
				empty($this->data['Address'][0]['street']) &&
				empty($this->data['Address'][0]['street_no']) &&
				empty($this->data['Address'][0]['zip']) &&
				empty($this->data['Address'][0]['city'])
			) {
				unset($this->data['Address']);
			} else {
				// dogeneruju si jmeno k adrese
				$this->data['Address'][0]['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
				// podle zadane adresy pridam i fakturacni
				$this->data['Address'][1] = $this->data['Address'][0];
				$this->data['Address'][1]['type'] = 'f';
			}

			// ukladam zakaznika (spolu s adresou a udaji o prihlaseni
			if ($this->Customer->saveAll($this->data)) {
				// ulozeni probehlo v poradku, naimportuju mailer class a odeslu zakaznikovi mail,
				// ze jeho ucet byl vytvoren, do dat o zakaznikovi si musim ale vratit nekryptovane heslo
				$this->data['CustomerLogin'][0]['password'] = $password_not_md5;

				if ($this->Customer->notify_account_created($this->data)) {
					// vsechno je ulozene, presmeruju na prihlasovaci stranku
					// a vypisu hlasku o registraci
					$this->Session->setFlash('Váš účet na ' . CUST_NAME . ' byl nyní vytvořen a přihlašovací údaje byly odeslány na email <strong>' . $this->data['Customer']['email'] . '</strong>.', REDESIGN_PATH . 'flash_success');					
				} else {
					$this->Session->setFlash('Váš účet na ' . CUST_NAME . ' byl vytvořen, ale nepodařilo se poslat přihlašovací údaje', REDESIGN_PATH . 'flash_failure');
				} 
		
				$this->redirect(array('controller' => 'customers', 'action' => 'login', 'reg-success'), null, true);
			} else {
				$this->Session->setFlash('Registrace nebyla úspěšná. Opravte chyby ve formuláři a uložte jej znovu', REDESIGN_PATH . 'flash_failure');
			}
		}
	}
	
	function order_personal_info() {
		$customer = null;
		// podivam se, jestli je zakaznik prihlaseny
		if ($this->Session->check('Customer.id')) {
			// pokud ano, predvyplnim formular jeho udaji
			$customer = $this->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->Session->read('Customer.id')),
				'contain' => array(
					'Address' => array(
						// seradim adresy tak, aby prvni byla fakturacni
						'order' => array('FIELD (Address.type, "f", "d")')
					)
				)
			));
		}

		// data ukladam do sesny, po potvrzeni rekapitulace ukladam k objednavce		
		if (isset($this->data)) {
			// nechci kontrolovat, jestli je zakaznikuv email unikatni (aby i zakaznik, ktery neni prihlaseny, ale jeho email
			// je v systemu, mohl dokoncit objednavku
			if (isset($this->data['Customer']['id']) && empty($this->data['Customer']['id'])) {
				unset($this->data['Customer']['id']);
			}
			unset($this->Customer->validate['email']['isUnique']);		
			if ($this->Customer->saveAll($this->data, array('validate' => 'only'))) {
				// jestli neni zakaznik prihlaseny a zaroven existuje zakaznik se zadanou emailovou adresou
				if (!$this->Session->check('Customer.id')) {
					$customer = $this->Customer->find('first', array(
						'conditions' => array('Customer.email' => $this->data['Customer']['email']),
						'contain' => array(),
						'fields' => array('Customer.id')
					));
					// pokud existuje, priradim k objednavce zakaznikovo idcko (at nezakladam noveho a nevznikaji mi ucty
					// s duplicitnim emailem
					if (!empty($customer)) {
						$this->Session->setFlash('Váš email je již v systému zaregistrován. Prosím příště se přihlašte, abychom Vám mohli nabídnout zboží za akční ceny.', REDESIGN_PATH . 'flash_failure');
						$this->data['Customer']['id'] = $customer['Customer']['id'];
					}
					// pamatuju si, ze zakaznik neni prihlaseny v objednavce (protoze to vsude testuju z historickych duvodu
					// pres customer id v sesne a to je mi ted na nic
					$this->data['Customer']['noreg'] = true;
				}
				$this->data['Address'][0]['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];
				$this->data['Address'][1]['name'] = $this->data['Customer']['first_name'] . ' ' . $this->data['Customer']['last_name'];

				$this->Session->write('Customer', $this->data['Customer']);
				$this->Session->write('Address', $this->data['Address'][1]);
				$this->Session->write('Address_payment', $this->data['Address'][0]);
				
				$this->redirect(array('controller' => 'orders', 'action' => 'set_payment_and_shipping'));
			} else {
				$this->Session->setFlash('Vložené údaje obsahují chybu, opravte ji prosím a formulář uložte znovu.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			if (isset($customer)) {
				$this->data = $customer;
			}
		}
		$this->set('customer',  $customer);
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Objednávka - vložení osobních údajů', 'href' => '/' . $this->params['url']['url'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// pokud ne, musi mi udaje do formulare nejdrive vyplnit
		$this->layout = REDESIGN_PATH . 'content';
	}

	function address_edit(){
		// nastavim nadpis
		$this->set('page_heading', ($this->params['named']['type'] == 'd' ? 'Doručovací' : 'Fakturační' ) . ' adresa');

		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		$breadcrumbs = array(
			array('anchor' => 'Zákaznický panel', 'href' => '/customers'),
			array('anchor' => 'Upravit adresu', 'href' => '/' . $this->params['url']['url'])	
		);
		$this->set('breadcrumbs', $breadcrumbs);
				
		$address = $this->Customer->Address->find(array('Address.customer_id' => $this->Session->read('Customer.id'), 'Address.type' => $this->params['named']['type']));
		if ( empty($this->data) ){
			$this->data = $address;
			if ( empty($address) ){
				$this->data['Address']['type'] = $this->params['named']['type'];
			}
		} else {
			// znamena to, ze clovek ma uz adresu daneho typu v db,
			// jen ji chce upravit
			if ( !empty($address) ){
				$this->data['Address']['id'] = $address['Address']['id'];
			}
			
			$this->data['Address']['customer_id'] = $this->Session->read('Customer.id');
			if ( $this->Customer->Address->save($this->data) ){
				$this->Session->setFlash('Adresa byla uložena.');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Adresa nebyla vyplněna správně, zkontrolujte prosím formulář.');
			}
		}
	}
	
	function edit(){
		// nastavim nadpis
		$this->set('page_heading', 'Editace zákazníka');

		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';

		$breadcrumbs = array(
			array('anchor' => 'Zákaznický panel', 'href' => '/customers'),
			array('anchor' => 'Upravit zákazníka', 'href' => '/customers/edit')
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// vytahnu data o zakaznikovi
		$this->Customer->recursive = 0;
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $this->Session->read('Customer.id')),
			'contain' => array(
				'CustomerLogin' => array(
					'fields' => array('CustomerLogin.id', 'CustomerLogin.login', 'CustomerLogin.password')
				)
			),
			'fields' => array('Customer.id', 'Customer.first_name', 'Customer.last_name', 'Customer.phone', 'Customer.email')
		));
		
		// pokud se mi nepodari vytahnout zakaznika, zakaznik neexistuje
		if ( empty($customer) ){
			$this->Session->setFlash('Neexistující zákazník!');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
		}

		$this->set('customer', $customer);

		if ( isset($this->data) ){
			// prochazim pristupove udaje
			foreach ($this->data['CustomerLogin'] as $index => &$customer_login) {
				// pokud nemam vyplnene pole pro zmenu hesla, tak je unsetnu, abych je nezpracovaval
				if (empty($customer_login['old_password']) && empty($customer_login['new_password']) && empty($customer_login['new_password_rep'])) {
					unset($customer_login['password']);
					unset($customer_login['old_password']);
					unset($customer_login['new_password']);
					unset($customer_login['new_password_rep']);
				} else {
					// pokud se shoduje stare heslo s tim, co mam v db a pokud je nove heslo v obou polich shodne, ulozim zmenu hesla
					if (md5($customer_login['old_password']) != $customer_login['password']) {
						$this->Session->setFlash('Vyplnil(a) jste špatně původní heslo! Zkuste to prosím znovu.');
						$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
					}
					
					if ($customer_login['new_password'] != $customer_login['new_password_rep']) {
						$this->Session->setFlash('Pole pro nové heslo a zopakování hesla jsou rozdílná! Zkuste to prosím znovu.');
						$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
					}
					
					if ($customer_login['new_password'] == '') {
						$this->Session->setFlash('Nové heslo nesmí zůstat prázdné, zadejte nové heslo');
						$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
					}

					$customer_login['password'] = md5($customer_login['new_password']);
				}

				if ($this->Customer->saveAll($this->data)) {
					$this->Session->setFlash('Vaše údaje byly upraveny.');
					$this->redirect(array('controller' => 'customers', 'action' => 'edit'), null, true);
				} else {
					$this->Session->setFlash('Vaše údaje se nepodařilo upravit. Opravte chyby ve formuláři a opakujte prosím akci');
				}
			}
		} else {
			$this->data = $customer;
		}
	}

	function edit_address($id){
		// nastavim nadpis
		$this->set('page_heading', 'Editace adresy');

		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';

		if ( !isset($this->data) ){
			$this->Customer->Address->recursive = -1;
			$this->data = $this->Customer->Address->find(array('Address.id' => $id,'Address.customer_id' => $this->Session->read('Customer.id')));
			if ( empty($this->data) ){
				$this->Session->setFlash('Neexistující adresa!');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
			}
			// else je obslouzeny ve view
		} else {
			$this->Customer->Address->recursive = -1;
			$address = $this->Customer->Address->find(array('Address.id' => $id,'Address.customer_id' => $this->Session->read('Customer.id')));
			if ( empty( $address ) ){
				$this->Session->setFlash('Neexistující adresa!');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'), null, true);
			} else {
				$this->Customer->Address->id = $address['Address']['id'];
				if ( $this->Customer->Address->save($this->data) ){
					$this->Session->setFlash('Adresa byla uložena!');
					$this->redirect(array('controller' => 'customers', 'action' => 'edit_address', $id), null, true);
				} else {
					$this->Session->setFlash('Adresu se nepodařilo uložit!');
				}
			}
		}
	}
	
	function index(){
		// nastavim nadpis
		$this->set('page_heading', 'Zákaznický panel');

		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Zákaznický panel', 'href' => '/customers')
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $this->Session->read('Customer.id')),
			'contain' => array(
				'Address' => array(
					'fields' => array('Address.id', 'Address.type', 'Address.name', 'Address.street', 'Address.street_no', 'Address.zip', 'Address.city', 'Address.state')
				),
				'Order' => array(
					'fields' => array('Order.id', 'Order.created', 'Order.subtotal_with_dph', 'Order.shipping_cost'),
					'order' => array('Order.created' => 'DESC'),
					'limit' => 3,
					'Status' => array(
						'fields' => array('Status.id', 'Status.color', 'Status.name')
					)
				),
				'CustomerLogin' => array(
					'fields' => array('CustomerLogin.id', 'CustomerLogin.login', 'CustomerLogin.password')
				)
			),
			'fields' => array('Customer.id', 'Customer.first_name', 'Customer.last_name', 'Customer.phone', 'Customer.email', '')
		));

		$this->set('customer', $customer);
	}
	
	/*
	 * @desription				Prihlasovani zakaznika.
	 */
	function login() {
		// nastavim nadpis
		$this->set('page_heading', 'Přihlášení zákazníka');

		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		
		// sestavim breadcrumbs
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Přihlášení do už. účtu', 'href' => $_SERVER['REQUEST_URI'])
		);
		$this->set('breadcrumbs', $breadcrumbs);

		if ( isset($this->data) ){

			// zakladni nastaveni pro presmerovani
			$backtrace_url = array(
				'controller' => 'customers',
				'action' => 'index'
			);
			// pokud je nadefinovano url, kam se ma presmerovat,
			// prepisu zakladni nastaveni
			if ( isset( $this->data['Customer']['backtrace_url'] ) ){
				$backtrace_url = $this->data['Customer']['backtrace_url'];
			}

			$conditions = array(
				'CustomerLogin.login' => $this->data['Customer']['login'],
				'CustomerLogin.password' => md5($this->data['Customer']['password']),
				'Customer.active' => true
			);
			
			// pokus o zalogovani podle SNV - existuje v SNV pro dane prihlasovaci udaje zakaznik?
			$customer = $this->Customer->CustomerLogin->find('first', array(
				'conditions' => $conditions,
				'contain' => array('Customer'),
			));

			if (empty($customer)){
				$this->Session->setFlash('Neplatný login nebo heslo!', REDESIGN_PATH . 'flash_failure');
			} else {
				// ulozim si info o zakaznikovi do session
				$this->Session->write('Customer', $customer['Customer']);
				
				// ze session odstranim data o objednavce,
				// pokud se snazil zakaznik pred prihlasenim neco
				// vyplnovat v objednavce, delalo by mi to bordel
				$this->Session->delete('Order');
				
				// na pocitadle si inkrementuju pocet prihlaseni
				$customer_update = array(
					'Customer' => array(
						'id' => $customer['Customer']['id'],
						'login_count' => $customer['Customer']['login_count'] + 1,
						'login_date' => date('Y-m-d H:i:s')
					)
				);
				$this->Customer->save($customer_update);
				
				// presmeruju
				$this->Session->setFlash('Jste přihlášen(a) jako ' . $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'] . '.', REDESIGN_PATH . 'flash_success');
				$this->redirect($backtrace_url, null, true);
			}
		}
	}

	function logout(){
		$this->Session->delete('Customer');
		$this->Session->setFlash('Jste úspěšně odhlášen(a)!', REDESIGN_PATH . 'flash_success');
		
		$backtrace_url = array('controller' => 'customers', 'action' => 'login');
		if (isset($_SERVER['HTTP_REFERER'])) {
			$backtrace_url = $_SERVER['HTTP_REFERER'];
		}
		$this->redirect($backtrace_url, null, true); 
	}
	
	function orders_list(){
		// nastavim nadpis
		$this->set('page_heading', 'Zákazníkovy objednávky');

		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Zákaznický panel', 'href' => '/customers'),
			array('anchor' => 'Objednávky', 'href' => '/customers/orders_list')
		);
		$this->set('breadcrumbs', $breadcrumbs);

		$orders = $this->Customer->Order->find('all', array(
			'conditions' => array('customer_id' => $this->Session->read('Customer.id')),
			'order' => array('Order.created' => 'desc')
		));
		$this->set('orders', $orders);
	}
	

	function order_detail($id){
		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		
		$order = $this->Customer->Order->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => array(
				'OrderedProduct' => array(
					'fields' => array('OrderedProduct.id', 'OrderedProduct.product_quantity', 'OrderedProduct.product_price_with_dph'),
					'OrderedProductsAttribute' => array(
						'Attribute' => array(
							'Option' => array(
								'fields' => array('Option.id', 'Option.name')
							),
							'fields' => array('Attribute.id', 'Attribute.value')
						)
					),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.url')
					)
				),
				'Shipping' => array(
					'fields' => array('Shipping.id', 'Shipping.name')
				),
				'Payment' => array(
					'fields' => array('Payment.id', 'Payment.name')
				)
			),
			'fields' => array(
				'Order.id',
				'Order.subtotal_with_dph',
				'Order.shipping_cost',
				'Order.customer_name',
				'Order.customer_street',
				'Order.customer_zip',
				'Order.customer_city',
				'Order.customer_state',
				'Order.delivery_name',
				'Order.delivery_street',
				'Order.delivery_zip',
				'Order.delivery_city',
				'Order.delivery_state',
				'Order.comments'
			)
		));

		if ( empty($order) ){
			$this->Session->setFlash('Neexistující objednávka!');
			$this->redirect(array('controller' => 'customers', 'action' => 'orders_list'));
		} else {
			$this->set('order', $order);

			// nastavim nadpis
			$this->set('page_heading', 'Detaily objednávky číslo ' . $order['Order']['id']);

			$breadcrumbs = array(
				array('anchor' => 'Domů', 'href' => '/'),
				array('anchor' => 'Zákaznický panel', 'href' => '/customers'),
				array('anchor' => 'Objednávky', 'href' => '/customers/orders_list'),
				array('anchor' => 'Objednávka č. ' . $order['Order']['id'], 'href' => '/customers/order_detail/' . $order['Order']['id'])
			);
			$this->set('breadcrumbs', $breadcrumbs);
		}
		
		$_title = 'Detail objednávky č. ' . $order['Order']['id'];
		$this->set('_title', $_title);
		
		$_description = $_title;
		$this->set('_description', $_description);
	}

	function password() {
		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		
		// nastavim nadpis
		$this->set('page_heading', 'Vyžádání hesla');
		
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Obnova hesla', 'href' => '/obnova-hesla')
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		$back = urlencode(base64_encode('/obnova-hesla'));
		if (isset($_GET['back'])) {
			$back = $_GET['back'];
		}
		$this->set('back', $back);
		
		if (isset($this->data)) {
			$back = $this->params['url']['url'];
			if (isset($this->data['Customer']['back'])) {
				$back = $this->data['Customer']['back'];
			}
			if (empty($this->data['Customer']['email'])) {
				$this->Session->setFlash('Zadejte Vaši emailovou adresu', REDESIGN_PATH . 'flash_failure');
				$this->redirect('/obnova-hesla?back=' . $back);
			} else {
				$customer = $this->Customer->find('first', array(
					'conditions' => array('Customer.email' => $this->data['Customer']['email']),
					'contain' => array(
						'CustomerLogin' => array(
							'fields' => array('CustomerLogin.id', 'CustomerLogin.login', 'CustomerLogin.password'),
							'limit' => 1
						)
					),
					'fields' => array('Customer.id', 'Customer.email', 'Customer.first_name', 'Customer.last_name')
				));
	
				if (empty($customer)) {
					$this->Session->setFlash('Účet s takovou emailovou adresou neexistuje.', REDESIGN_PATH . 'flash_failure');
				} else {
					$this->Customer->changePassword($customer, $back);
					$this->Session->setFlash('Vaše žádost byla úspěšně odeslána ke zpracování. Zkontrolujte prosím Vaši emailovou schránku.', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('controller' => 'customers', 'action' => 'login')); 
				}
			}
		}
	}
	
	function confirm_hash() {
		if (!isset($this->params['named']['hash']) || !isset($this->params['named']['customer_id'])) {
			$this->Session->setFlash('Chyba při ověření požadavku. Zkontrolujte prosím, zda je adresa pro ověření zadána správně.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('url' => 'customers', 'action' => 'password'));
		}
		
		$hash = $this->params['named']['hash'];
		$hash = urldecode($hash);
		$customer_id = $this->params['named']['customer_id'];
		
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $customer_id),
			'contain' => array(),
		));
		
		if (empty($customer)) {
			$this->Session->setFlash('Chyba při ověření požadavku. Zkontrolujte prosím, zda je adresa pro ověření zadána správně.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'password'));
		}
		
		if (isset($this->data)) {
			// pokud zakaznik heslo nezadal, vygeneruju mu ho a poslu emailem
			if (empty($this->data['Customer']['password'])) {
				$password = $this->Customer->generatePassword($customer);
				$md5_password = md5($password);
			// jinak nastavim nove heslo
			} else {
				$password = $this->data['Customer']['password'];
				$md5_password = md5($this->data['Customer']['password']);
			}
			
			$customer_logins = $this->Customer->CustomerLogin->find('all', array(
				'conditions' => array('CustomerLogin.customer_id' => $customer_id),
				'contain' => array(),
				'fields' => array('CustomerLogin.id', 'CustomerLogin.login')
			));
			
			if (empty($customer_logins)) {
				$customer_logins = array(
					'CustomerLogin' => array(
						'login' => $customer['Customer']['email'],
						'customer_id' => $customer_id 
					)
				);
			}
			
			$save = array();
			$login = false;
			foreach ($customer_logins as $login) {
				$save_item = array('password' => $md5_password);
				if (isset($login['CustomerLogin']['id'])) {
					$save_item['id'] = $login['CustomerLogin']['id'];
				}
				$save[] = $save_item;
			}
			if (!empty($save)) {
				if ($this->Customer->CustomerLogin->saveAll($save)) {
					// poslu email s novymi prihlasovacimi udaji
					$this->Customer->passwordRecoveryMail($customer_id, $customer_logins[0]['CustomerLogin']['login'], $password);
						
					// automaticky ho prihlasim
					$this->Session->write('Customer', $customer['Customer']);
					$this->Session->setFlash('Vaše přístupové údaje Vám byly odeslány na emailovou adresu.<br/>Byl jste úspěšně přihlášen', REDESIGN_PATH . 'flash_success');
					// PRESMERUJU TAM, ODKUD PRISEL
					$url = '/prihlaseni';
					if (isset($this->params['named']['back'])) {
						$url = urldecode(base64_decode($this->params['named']['back']));
					}
					$this->redirect($url);
				} else {
					$this->Session->setFlash('Nepodařilo se změnit heslo.', REDESIGN_PATH . 'flash_failure');
					$this->redirect(array('controller' => 'customers', 'action' => 'confirm_hash') + $this->passedArgs);
				}
			}
		}
		
		$db_hash = $this->Customer->passwordRecoveryHash($customer['Customer']['email']);
		
		if ($hash != $db_hash) {
			$this->Session->setFlash('Chyba při ověření požadavku. Zkontrolujte prosím, zda je adresa pro ověření zadána správně.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customers', 'action' => 'password'));
		}
		
		$breadcrumbs = array(
			array('anchor' => 'Domů', 'href' => '/'),
			array('anchor' => 'Obnova hesla', 'href' => '/obnova-hesla'),
			array('anchor' => 'Zadání nového hesla', 'href' => $this->params['url']['url'])
		);
		$this->set('breadcrumbs', $breadcrumbs);
		
		// nastavim layout
		$this->layout = REDESIGN_PATH . 'content';
		$this->set('customer_id', $customer_id);
	}
	
	/**
	 * otestuje, zda je prihlasen nejaky administrator
	 * pouzivam v javascriptove metode, abych pred vykonanim nejake funkce vedel, ze je admin prihlaseny a ma opravneni
	 * funkci vykonec
	 */
	function check_admin_logged() {
		$data = array(
			'success' => true,
			'message' => null
		);

		if (!$this->Session->check('Administrator')) {
			$data['message'] = 'Nejste přihlášen(a). Přihlašte se prosím znovu!';
		}
		echo json_encode($data);
		die();
	}
	
	function admin_import() {
		$this->Customer->import();
		die('here');
	}
}
?>
