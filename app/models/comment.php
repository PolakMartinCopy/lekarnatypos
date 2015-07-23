<?php
class Comment extends AppModel {
	var $name = "Comment";
	
	var $actsAs = array('Containable');

	var $belongsTo = array('Product', 'Administrator');

	var $validate = array(
		'author' => array(
			'rule' => array('minLength', 1),
			'message' => 'Zadejte vaše jméno, nebo přezdívku.'
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Vyplňte prosím existující emailovou adresu, abychom Vám mohli odeslat odpověď na mail.',
			)
		),
		'subject' => array(
			'rule' => array('minLength', 1),
			'message' => 'Zadejte předmět komentáře / dotazu.'
		),
		'body' => array(
			'rule' => array('minLength', 1),
			'message' => 'Zadejte tělo komentáře / dotazu.'
		),
		'personal_email' => array(
			'rule' => array('inList', array(''))
		),
		'work_email' => array(
			'rule' => array('inList', array('jan.novak@necoxyz.com'))
		)
	);

	/**
	 * Rozeznava retezce, ktere indikuji, ze dany komentar je SPAM.
	 * 
	 * @return boolean
	 */
	function is_spam($content){
		// predpoklad, ze komentar neni spam
		$result = false;
		
		// retezce, ktere indikuji SPAM
		$patterns = array(
			0 => "\[\/url\]",
			"\[\/link\]",
			"\[url=(.*)\]",
			"\[link=(.*)\]",
			"cialis",
			"penis",
			"phentermine",
			"levitra",
			"adipex",
			"acomplia",
			"viagra",
			"reductil",
			"klonopin",
			"lasix",
			"potassium",
			"insurance",
			"propecia",
			"aciphex",
			"xanax",
			"tramadol",
			"pharmacy"
		);

		// zjistim, zda se jedna o SPAM
		for ( $i = 0; $i < count($patterns); $i++ ){
			if ( eregi($patterns[$i], $content) ){
				$result = true;
			}
		}
		return $result;
	}
	
	/**
	 * Notifikace administratoru o novem dotazu v obchode.
	 *
	 * @return unknown
	 */
	function notify_new_comment($id){
		// nactu si comment
		$comment = $this->find('first', array(
			'conditions' => array('Comment.id' => $id),
			'contain' => array()
		));
		// natahnu si mailovaci skript
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = new PHPMailer();
		
		// uvodni nastaveni
		$mail->CharSet = 'utf-8';
		$mail->Hostname = CUST_ROOT;
		$mail->Sender = 'no-reply@sportnutrition.cz';
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = 'no-reply@sportnutrition.cz';
		$mail->FromName = "sportnutrition.cz";
		
//		$mail->AddReplyTo(CUST_MAIL, CUST_NAME);

		$mail->AddAddress(CUST_MAIL, CUST_NAME);
//		$mail->AddBCC("vlado@tovarnak.com", "Vlado Tovarnak");
		
		$mail->Subject = 'E-SHOP (' . CUST_ROOT . ') - NOVÝ DOTAZ';
		$mail->Body = 'Právě byl položen nový dotaz.' . "\n\n";
		$mail->Body .= $comment['Comment']['subject'] . "\n";
		$mail->Body .= $comment['Comment']['author'] . ' - ' . $comment['Comment']['email'] . "\n";
		$mail->Body .= $comment['Comment']['body'] . "\n\n";
		$mail->Body .= 'Spravovat jej můžete zde: http://www.' . CUST_ROOT . '/admin/comments/edit/' . $id . "\n";

		return $mail->Send();
	}

	/**
	 * Notifikace o odpovedi na dotaz zakaznika.
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	function notify_answer($comment){
		if (is_numeric($comment)) {
			$comment = $this->find('first', array(
				'conditions' => array('Comment.id' => $comment),
				'contain' => array()	
			));
		}

		if (isset($comment['Comment']['email'])) {
			// vytvorim si objekt mailu
			// mam definovanou sablonu pro email s odpovedi na komentar?
			$mail_template_conditions = false;
			if (defined('COMMENT_ANSWER_MAIL_TEMPLATE_ID')) {
				$mail_template_conditions = array('MailTemplate.id' => COMMENT_ANSWER_MAIL_TEMPLATE_ID);
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
					$customer_mail = $this->MailTemplate->process($mail_template['MailTemplate']['id'], $comment['Comment']['id']);
				}
			} else {
				// vytvorim si emailovou zpravu
				$customer_mail = 'Dobrý den,' . "\n";
				$customer_mail .= 'Váš dotaz v následujícím znění:' . "\n\n";
				$customer_mail .= $comment['Comment']['body']. "\n\n";
				$customer_mail .= 'byl zodpovězen, odpověď naleznete níže:' . "\n\n";
				$customer_mail .= $comment['Comment']['reply']. "\n\n";
				
				$customer_mail .= 's pozdravem' . "\n" . 'team internetového obchodu ' . CUST_NAME;
			}
			
			App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
			$mail = new phpmailer();

			// uvodni nastaveni
			$mail->CharSet = 'utf-8';
			$mail->Hostname = CUST_ROOT;
			$mail->Sender = CUST_MAIL;
			$mail->IsHtml(true);
			
			// nastavim adresu, od koho se poslal email
			$mail->From     = CUST_MAIL;
			$mail->FromName = CUST_NAME;
			$mail->AddReplyTo(CUST_MAIL, CUST_NAME);

			$mail->AddAddress($comment['Comment']['email'] , $comment['Comment']['author']);
			$mail->AddBCC('brko11@gmail.com');
	
			$mail->Subject = $comment['Comment']['subject'] . " - odpověď na váš dotaz";
			if (is_array($customer_mail)) {
				$mail->Subject = $customer_mail['MailTemplate']['subject'];
				$customer_mail = $customer_mail['MailTemplate']['content'];
			}
			$mail->Body = $customer_mail;
			return $mail->Send();
		}
		return false;
	}
}
?>
