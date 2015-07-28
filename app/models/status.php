<?
class Status extends AppModel {

	var $name = 'Status';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => false
		)
	);

	var $hasMany = array('Order');
	
	var $belongsTo = array('MailTemplate', 'SMSTemplate');
	

	var $validate = array(
		'name' => array(
			'minLength' => array(
				'rule' => array('minLength', 1),
				'required' => true,
				'message' => 'Vyplňte prosím název statusu.'
			),
			'isUnique' => array(
				'rule' => array('isUnique', 'name'),
				'required' => true,
				'message' => 'Tento status již existuje! Zvolte prosím jiný název stavu.'
			)
		)
	);

	function change_notification($order_id, $status_id){
		// nejdrive overim, jestli ma dany status nadefinovany nejaky template
		$this->recursive = -1;
		$status = $this->read(null, $status_id);
		
		if ( !empty($status) && $status['Status']['mail_template_id'] != 0 ){
			$mail_template_conditions = array('MailTemplate.id' => $status['Status']['mail_template_id']);
			$mail_template = $this->MailTemplate->find('first', array(
				'conditions' => $mail_template_conditions,
				'contain' => array(),
				'fields' => array('MailTemplate.id')
			));

			if (empty($mail_template)) {
				return false;
			} else {
				$mail_template = $this->MailTemplate->process($mail_template['MailTemplate']['id'], $order_id);

				// mam sestaveny mail template s daty
				// musim ho odeslat, nactu si detaily z objednavky
				$order = $this->Order->find('first', array(
						'conditions' => array('Order.id' => $order_id),
						'contain' => array()
				));
					
				// natahnu si mailovaci skript
				App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
				$ppm = &new phpmailer;
				$ppm->CharSet = 'utf-8';
				$ppm->Hostname = CUST_ROOT;
				$ppm->Sender = CUST_MAIL;
				$ppm->From = CUST_MAIL;
				$ppm->FromName = CUST_NAME;
				$ppm->ReplyTo = CUST_MAIL;
					
				$ppm->Body = $mail_template['MailTemplate']['content'];
				$ppm->Subject = $mail_template['MailTemplate']['subject'];
				$ppm->AddAddress($order['Order']['customer_email'], $order['Order']['customer_name']);
				$ppm->AddBCC('brko11@gmail.com');
				$ppm->IsHtml(true);

				return $ppm->Send();	
			}
		} else {
			return false;
		}
	}

	function has_requested($status_id){
		$return = false;
		
		$status = $this->find('first', array(
			'conditions' => array('Status.id' => $status_id),
			'fields' => array('Status.id', 'Status.requested_fields'),
			'recursive' => -1
		));
		
		$rfs = array();
		if ( !empty($status['Status']['requested_fields']) ){
			$rf = explode("\n", $status['Status']['requested_fields']);
			$count = count($rf);
			for( $i =0; $i < $count; $i = $i + 2 ){
				$rfs[trim($rf[$i])] = $rf[$i + 1]; 
			}
			$return = $rfs;
		}
		return $return;
	}
}
?>
