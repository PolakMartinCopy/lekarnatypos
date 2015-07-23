<?php
class Customer extends AppModel {
	var $name = 'Customer';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('CustomerType');

	var $hasMany = array(
		'Order',
		'Address' => array(
			'dependent' => true
		),
		'CustomerLogin' => array(
			'dependent' => true
		)
	);

 	var $validate = array(
		'first_name' => array(
			'rule' => array('minLength', 3),
			'message' => 'Vyplňte prosím vaše jméno.'
		),
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím vaše příjmení.'
			)
		),
		'phone' => array(
			'minLength' => array(
				'rule' => array('minLength', 8),
				'message' => 'Vyplňte prosím správně vaše telefonní číslo.',
				'last' => true
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Vyplňte prosím existující emailovou adresu.',
				'last' => true
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Uživatel s touto emailovou adresou již existuje. Zvolte jinou emailovou adresu, nebo se přihlašte.'
			)
		)
	);
 	
 	var $virtualFields = array(
 		'name' => 'CONCAT(Customer.last_name, " ", Customer.first_name)'	
 	);
 	
 	var $export_file = 'files/customers.csv';
 	
 	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
 		$parameters = compact('conditions');
 		$this->recursive = $recursive;
 		$count = $this->find('count', array_merge($parameters, $extra));
 		if (isset($extra['group'])) {
 			$count = $this->getAffectedRows();
 		}
 		return $count;
 	}
 	

	function assignPassword($customer_login_id, $email){
		$start = rand(0, 23);
		$password = md5($email . Configure::read('Security.salt'));
		$password = substr($password, $start, 8);
		$password = strtolower($password);
	
		$customer_login = array(
			'CustomerLogin' => array(
				'id' => $customer_login_id,
				'password' => md5($password)
			)
		);

		$this->CustomerLogin->save($customer_login, false);
		return $password;
	}
	
	function passwordRecoveryHash($email) {
		return md5($email . Configure::read('Security.salt'));
	}
	
	private function passwordRecoveryUrl($email, $customer_id, $back = null) {
		$hash = $this->passwordRecoveryHash($email);
		$url = 'http://www.' . CUST_ROOT . '/customers/confirm_hash/hash:' . urlencode($hash) . '/customer_id:' . $customer_id;
		if ($back) {
			$url .= '/back:' . $back;
		}
		return $url;
	}
	
	function passwordRecoveryMail($customer_id, $login, $password) {
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = &new PHPMailer;
		
		$customer = $this->find('first', array(
			'conditions' => array('Customer.id' => $customer_id),
			'contain' => array(),
			'fields' => array('Customer.first_name', 'Customer.last_name', 'Customer.email')
		));
		
		if (empty($customer)) {
			return false;
		}
		
		$mail->CharSet = $this->CharSet = 'utf-8';
		$mail->Hostname = $this->Hostname = CUST_ROOT;
		$mail->Sender = $this->Sender = CUST_MAIL;
		$mail->From = $this->From = CUST_MAIL;
		$mail->FromName = $this->FromName = CUST_NAME;
		$mail->ReplyTo = $this->ReplyTo = CUST_MAIL;
		
		$mail->AddAddress($customer['Customer']['email'], $customer['Customer']['first_name'] . " " . $customer['Customer']['last_name']);
		$mail->Subject = 'změna hesla pro přístup do www.' . CUST_ROOT;
		$mail->Body = "Dobrý den,\n\n";
		$mail->Body .= "Váš požadavek na změnu hesla byl vykonán, pro přihlášení k účtu,
		použijte následující údaje: \n";
		$mail->Body .= "login: " . $login . "\n";
		$mail->Body .= "heslo: " . $password . "\n";
		$mail->Body .= "team " . CUST_NAME . "\n";

		$mail->Send();
	}
	
	function changePassword($customer, $back) {
		include 'class.phpmailer.php';

		$mail = &new phpmailer;

		$mail->CharSet = $this->CharSet = 'utf-8';
		$mail->Hostname = $this->Hostname = CUST_ROOT;
		$mail->Sender = $this->Sender = CUST_MAIL;
		$mail->From = $this->From = CUST_MAIL;
		$mail->FromName = $this->FromName = CUST_NAME;
		$mail->ReplyTo = $this->ReplyTo = CUST_MAIL;
		
		$mail->AddAddress($customer['Customer']['email'], $customer['Customer']['first_name'] . " " . $customer['Customer']['last_name']);
		$mail->Subject = 'Zapomenuté heslo pro přístup do www.' . CUST_ROOT;
		$mail->Body = "Dobrý den,\n\n";
		$mail->Body .= "na základě žádosti odeslané z www." . CUST_ROOT . " Vám zasíláme odkaz pro obnovu hesla k Vašemu účtu.
		Pro změnu hesla prosím klikněte na níže uvedený odkaz \n\n";
		$mail->Body .= $this->passwordRecoveryUrl($customer['Customer']['email'], $customer['Customer']['id'], $back) . "\n";
		$mail->Body .= "team " . CUST_NAME . "\n";
		$mail->Body .= "--\n";
		$mail->Body .= "emailová adresa " . $customer['Customer']['email'] . " byla použita pro vyžádání změny hesla pro přístup\n";
		$mail->Body .= "na " . CUST_ROOT . " Jste-li majitelem emailové schránky a neprováděl(a) jste žádnou žádost o změnu,\n";
		$mail->Body .= "můžete tuto zprávu ignorovat, nedošlo k žádným změnám na vašem účtu.";

		$mail->Send();
	}
	
	function changeNSPassword($customer, $back) {
		$customer_login = $customer['NSCustomer']['login'];
		$start = rand(0, 23);
		$customer_password = md5($customer['NSCustomer']['email'] . Configure::read('Security.salt'));
		$customer_password = substr($customer_password, $start, 8);
		$customer_password = strtolower($customer_password);
		
		$customer['NSCustomer']['password'] = md5($customer_password);

		// updatuju hodnoty v tabulce ns_customers
		$this->query('
			UPDATE ns_customers
			SET password="' . $customer['NSCustomer']['password'] . '"
			WHERE id=' . $customer['NSCustomer']['id'] . '
		');
		
		include 'class.phpmailer.php';
		
		$mail = &new phpmailer;
		
		$mail->CharSet = $this->CharSet = 'utf-8';
		$mail->Hostname = $this->Hostname = CUST_ROOT;
		$mail->Sender = $this->Sender = CUST_MAIL;
		$mail->From = $this->From = CUST_MAIL;
		$mail->FromName = $this->FromName = CUST_NAME;
		$mail->ReplyTo = $this->ReplyTo = CUST_MAIL;
		
		$mail->AddAddress($customer['NSCustomer']['email'], $customer['NSCustomer']['first_name'] . " " . $customer['NSCustomer']['last_name']);
		$mail->Subject = 'Zapomenuté heslo pro přístup do www.' . CUST_ROOT;
		$mail->Body = "Dobrý den,\n\n";
		$mail->Body .= "na základě žádosti odeslané z www." . CUST_ROOT . " Vám zasíláme odkaz pro obnovu hesla k Vašemu účtu.
		Pro změnu hesla prosím klikněte na níže uvedený odkaz \n\n";
		$mail->Body .= $this->passwordRecoveryUrl($customer['Customer']['email'], $customer['Customer']['id'], $back) . "\n";
		$mail->Body .= "team " . CUST_NAME . "\n";
		$mail->Body .= "--\n";
		$mail->Body .= "emailová adresa " . $customer['Customer']['email'] . " byla použita pro vyžádání změny hesla pro přístup\n";
		$mail->Body .= "na " . CUST_ROOT . " Jste-li majitelem emailové schránky a neprováděl(a) jste žádnou žádost o změnu,\n";
		$mail->Body .= "můžete tuto zprávu ignorovat, nedošlo k žádným změnám na vašem účtu.";

		$mail->Send();
	}
	
	
	function loginExists($login){
		$condition = array('CustomerLogin.login' => $login);
		return $this->CustomerLogin->hasAny($condition);
	}

	
	function generateLogin($customer){
		// vygeneruje nahodne login
		do{
			// vytahnu si osm znaku z md5ky s nahodnym startem
			$start = rand(0, 23);
			$login = md5($customer['last_name'] . date("Y-m-d"));
			$login = substr($login, $start, 8);
			// dam si login do uppercase
			$login = strtoupper($login);
		} while ( $this->loginExists($login) === true );
		
		return $login;
	}
	
	
	function generatePassword($customer){
		// vytahnu si osm znaku z md5ky,
		// s nahodnym startem
		$start = rand(0, 23);
		$password = md5($customer['last_name']);
		$password = substr($password, $start, 8);
		$password = strtolower($password);
		return $password;
	}
	
	function notify_account_created($customer) {
		// musim zjistit, zda zakaznik uvedl
		// emailovou adresu, jinak nebudu mail posilat
		if (isset($customer['Customer']['email']) && !empty($customer['Customer']['email'])) {
			// mam definovanou sablonu pro email s informacemi o registraci?
			$mail_template_conditions = false;
			if (defined('NEW_CUSTOMER_MAIL_TEMPLATE_ID')) {
				$mail_template_conditions = array('MailTemplate.id' => NEW_CUSTOMER_MAIL_TEMPLATE_ID);
			}
			
			App::import('Model', 'MailTemplate');
			$this->MailTemplate = &new MailTemplate;
			
			if ($mail_template_conditions && $this->MailTemplate->hasAny($mail_template_conditions)) {
				$mail_template = $this->MailTemplate->find('first', array(
					'conditions' => $mail_template_conditions,
					'contain' => array(),
					'fields' => array('MailTemplate.id')
				));
					
				if (empty($mail_template)) {
					return false;
				} else {
					$options = array(
						'login' => $customer['CustomerLogin'][0]['login'],
						'password' => $customer['CustomerLogin'][0]['password']
					);
					$customer_mail = $this->MailTemplate->process($mail_template['MailTemplate']['id'], null, $options);

				}
			} else {
				// vytvorim si emailovou zpravu
				$customer_mail = 'Vážená(ý) ' . $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name'] . "\n\n";
				$customer_mail .= 'Tento email byl automaticky vygenerován a odeslán, abychom potvrdili Vaši registraci' .
						' v online obchodě http://www.' . CUST_ROOT . '/' . "\n";
				$customer_mail .= 'Váš účet byl vytvořen s těmito přihlašovacími údaji:' . "\n";
				$customer_mail .= 'LOGIN: ' . $customer['CustomerLogin'][0]['login'] . "\n";
				$customer_mail .= 'HESLO: ' . $customer['CustomerLogin'][0]['password'] . "\n";
				$customer_mail .= 'Pro přihlášení k Vašemu uživatelskému účtu použijte prosím přihlašovací formulář, který' .
						' najdete na adrese http://www.' . CUST_ROOT . '/customers/login ' . "\n";
				$customer_mail .= 'Pomocí Vašeho uživatelského účtu můžete operovat s uskutečněnými objednávkami, sledovat' .
						' jejich stav a vytvářet objednávky nové.' . "\n\n";
				$customer_mail .= 'Velmi si vážíme Vaší důvěry, děkujeme.' . "\n";
			}
			
			// vytvorim si objekt mailu
			App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
			$mail = new phpmailer();
			
			// uvodni nastaveni maileru
			$mail->CharSet = 'utf-8';
			$mail->Hostname = CUST_ROOT;
			$mail->Sender = CUST_MAIL;
			
			// nastavim adresu, od koho se poslal email
			$mail->From     = CUST_MAIL;
			$mail->FromName = "Automatické potvrzení";
			$mail->AddReplyTo(CUST_MAIL, CUST_NAME);
			
			// nastavim kam se posila email
			$mail->AddAddress($customer['Customer']['email'], $customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name']);
			$mail->Subject = 'Vytvoření zákaznického účtu na ' . CUST_ROOT;
			
			if (is_array($customer_mail)) {
				$mail->Subject = $customer_mail['MailTemplate']['subject'];
				$customer_mail = $customer_mail['MailTemplate']['content'];
			}

			$mail->Body = $customer_mail;
			return $mail->Send();
		}
		return false;
	}
	
	function orders_count($id) {
		return $this->Order->find('count', array(
			'conditions' => array('Order.customer_id' => $id)	
		));
	}
	
	function orders_amount($id) {
		$amount_field = 'SUM(Order.shipping_cost + Order.subtotal_with_dph)';
		$this->Order->virtualFields['amount'] = $amount_field;
		
		$amount = $this->Order->find('all', array(
			'conditions' => array('Order.customer_id' => $id),
			'fields' => array('Order.amount'),
			'contain' => array(),
			'group' => array('Order.customer_id')
		));
		
		return (empty($amount) ? 0 : $amount[0]['Order']['amount']);
	}
	
	// je uzivatel typu VOC? (customer_type_id == 6)
	function is_voc($id) {
		$customer = $this->find('first', array(
			'conditions' => array('Customer.id' => $id, 'Customer.customer_type_id' => 6),
			'contain' => array()
		));
		
		return !empty($customer);
	}
	
	function import() {
		$this->truncate();
		$this->CustomerLogin->truncate();

		$snCustomers = $this->findAllSn();
		// pro kazdou tridu adres
		foreach ($snCustomers as $snCustomer) {
			$customer = $this->transformSn($snCustomer);
			$this->create();
			if (!$this->saveAll($customer)) {
				debug($customer);
				debug($this->validationErrors);
				$this->saveAll($customer, array('validate' => false));
			}
		}


		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM customers AS SnCustomer
		';
		
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}

		$snCustomers = $this->query($query);
		$this->setDataSource('default');
		return $snCustomers;
	}
	
	function transformSn($snCustomer) {
		$customer = array(
			'Customer' => array(
				'id' => $snCustomer['SnCustomer']['id'],
				'first_name' => $snCustomer['SnCustomer']['first_name'],
				'last_name' => $snCustomer['SnCustomer']['last_name'],
				'phone' => $snCustomer['SnCustomer']['phone'],
				'email' => $snCustomer['SnCustomer']['email'],
				'company_name' => $snCustomer['SnCustomer']['company_name'],
				'company_ico' => $snCustomer['SnCustomer']['company_ico'],
				'company_dic' => $snCustomer['SnCustomer']['company_dic'],
				'registration_source' => $snCustomer['SnCustomer']['registration_source'],
				'confirmed' => $snCustomer['SnCustomer']['confirmed'],
				'newsletter' => $snCustomer['SnCustomer']['newsletter'],
				'customer_type_id' => 1,
				'active' => 1,
			),
		);
		
		if ($snCustomer['SnCustomer']['login']) {
			$customer['CustomerLogin'][] = array(
				'login' => $snCustomer['SnCustomer']['login'],
				'password' => $snCustomer['SnCustomer']['password']
			);
		}
		return $customer;
	}
	
	function estimateFirstName($snName) {
		$customerFirstName = '';
		if (!empty($snName)) {
			$customerName = explode(' ', $snName);
			if (count($customerName) > 1) {
				$customerFirstName = $customerName[0];
			}
		}
		return $customerFirstName;
	}
	
	function estimateLastName($snName) {
		$customerLastName = '';
		if (!empty($snName)) {
			$customerName = explode(' ', $snName);
			if (count($customerName) > 1) {
				unset($customerName[0]);
				$customerLastName = implode(' ', $customerName);
			} else {
				$customerLastName = $customerName[0];
			}
		}
		return $customerLastName;
	}
	
	function estimateStreetName($streetInfo) {
		$streetName = $streetInfo;
		if (preg_match('/(.*) (([1-9][0-9]*)\/)?([1-9][0-9]*[a-cA-C]?)/', $streetInfo, $matches)) {
			$streetName = $matches[1];
		}
		return $streetName;
	}
	
	function estimateStreetNumber($streetInfo) {
		$streetNumber = '';
		if (preg_match('/.* ((([1-9][0-9]*)\/)?([1-9][0-9]*[a-cA-C]?))/', $streetInfo, $matches)) {
			$streetNumber = $matches[1];
		}
		return $streetNumber;
	}
	
	function csv_export($customers) {
		$file = fopen($this->export_file, 'w');
		
		$lines = array(
//			0 => array('ID', 'Jmeno', 'Email', 'Telefon', 'Ulice', 'Mesto', 'PSC')
		);
		
		foreach ($customers as $customer) {
			$customer_street = '';
			if (!empty($customer['Address'])) {
				$customer_street = $customer['Address'][0]['street'];
				if (!empty($customer_street) && !empty($customer['Address'][0]['street_no'])) {
					$customer_street .= ' ' . $customer['Address'][0]['street_no'];
				}
			}
	
			$customer_city = (empty($customer['Address'][0]['city']) ? '' : $customer['Address'][0]['city']);
			$customer_zip = (empty($customer['Address'][0]['zip']) ? '' : $customer['Address'][0]['zip']);
	
			$lines[] = array(
				$customer['Customer']['id'],
				$customer['Customer']['name'],
				$customer['Customer']['email'],
				$customer['Customer']['phone'],
				$customer_street,
				$customer_city,
				$customer_zip
			);
		}
		
		foreach ($lines as $line) {
			$row = implode(';', $line);
			fwrite($file, iconv('utf-8', 'windows-1250', $row . "\r\n"));
		}
	
		fclose($file);
		return true;
	}
}
?>
