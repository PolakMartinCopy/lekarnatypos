<?php 
class Recommendation extends AppModel {
	var $name = 'Recommendation';
	
	var $useTable = false;
	
	var $validate = array(
		'source_email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Zadejte Vaši emailovou adresu',
				'allowEmpty' => false,
				'required' => true
			)
		),
		'target_email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Zadejte emailovou adresu, kam si přejete poslat doporučení',
				'allowEmpty' => false,
				'required' => true
			)
		)
	);
	
	function send($source_name, $source_email, $target_email, $backtrace_uri) {
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$email = new PHPMailer;
		
		// na localhostu nastavim odesilani posty pres gmail smtp
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			$email->IsSMTP();
			// set smtp debug
			$email->SMTPDebug = 2;
			//Whether to use SMTP authentication
			$email->SMTPAuth = true;
			//enable this if you are using gmail smtp, for mandrill app it is not required
			$email->SMTPSecure = 'ssl';
			//Set the hostname of the mail server
			$email->Host = "smtp.gmail.com";
			//Set the SMTP port number - likely to be 25, 465 or 587
			$email->Port = 465;
			//Username to use for SMTP authentication
			$email->Username = "brko11@gmail.com";
			//Password to use for SMTP authentication
			$email->Password = "Maruska_11";
		}
		
		$email->CharSet = 'utf-8';
		$email->Hostname = CUST_ROOT;
		$email->Sender = CUST_MAIL;
		$email->From = CUST_MAIL;
		$email->FromName = CUST_NAME;
		$email->ReplyTo = CUST_MAIL;
			
		$email->Subject = 'Navštivte ' . CUST_NAME;
		$email->Body = 'Dobrý den,

návštěvník webu ' . CUST_NAME . ($source_name ? ' ' . $source_name . ',' : '') . ' ' . $source_email . ' Vám doporučuje navštívit http://www.' . CUST_ROOT . $backtrace_uri . '

S pozdravem tým ' . CUST_NAME . '.';

		$email->AddAddress($target_email);

		return $email->Send();
	}
}
?>