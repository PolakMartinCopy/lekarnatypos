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

		if ( isset( $comment['Comment']['email'] ) ){
			// natahnu si mailovaci skript
			include 'class.phpmailer.php';
			
			// notifikacni email zakaznikovi
			// o dotazu
			$mail = new phpmailer();

			// uvodni nastaveni
			$mail->CharSet = 'utf-8';
			$mail->Hostname = CUST_ROOT;
			$mail->Sender = CUST_MAIL;
			
			// nastavim adresu, od koho se poslal email
			$mail->From     = CUST_MAIL;
			$mail->FromName = "Automatické potvrzení";
			
			$mail->AddReplyTo(CUST_MAIL, CUST_NAME);

			$mail->AddAddress($comment['Comment']['email'] , $comment['Comment']['author']);
			$mail->AddBCC("vlado@tovarnak.com", "Vlado Tovarnak");
	
			$mail->Subject = $comment['Comment']['subject'] . " - odpověď na váš dotaz";
			$mail->Body = 'Dobrý den,' . "\n";
			$mail->Body .= 'Váš dotaz v následujícím znění:' . "\n\n";
			$mail->Body .= $comment['Comment']['body']. "\n\n";
			$mail->Body .= 'byl zodpovězen, odpověď naleznete níže:' . "\n\n";
			$mail->Body .= $comment['Comment']['reply']. "\n\n";
			
			$mail->Body .= 's pozdravem' . "\n" . 'team internetového obchodu ' . CUST_NAME;

			return $mail->Send();
		}
		return false;
	}
	
	/*
	 * Natahne sportnutrition data
	 */
	function import() {
		$this->truncate();
		$snComments = $this->findAllSn();
		foreach ($snComments as $snComment) {
			$comment = $this->transformSn($snComment);
			$this->create();
			if (!$this->save($comment)) {
				debug($comment);
				debug($this->validationErrors);
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM comments AS SnComment
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snComments = $this->query($query);
		$this->setDataSource('default');
		return $snComments;
	}
	
	function transformSn($snComment) {
		$comment = array(
			'Comment' => array(
				'id' => $snComment['SnComment']['id'],
				'product_id' => $snComment['SnComment']['product_id'],
				'author' => $snComment['SnComment']['author'],
				'email' => $snComment['SnComment']['email'],
				'subject' => $snComment['SnComment']['subject'],
				'body' => $snComment['SnComment']['body'],
				'reply' => $snComment['SnComment']['reply'],
				'administrator_id' => $snComment['SnComment']['administrator_id'],
				'confirmed' => $snComment['SnComment']['confirmed'],
				'sent' => $snComment['SnComment']['sent'],
			)
		);
	
		return $comment;
	}
}
?>