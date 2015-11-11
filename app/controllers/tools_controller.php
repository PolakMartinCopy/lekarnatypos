<?php 
class ToolsController extends AppController {
	var $name = 'Tools';
	
	// zapise do sesny zvoleny tab v login boxu
	function login_box_tab() {
		if (isset($_POST['tab'])) {
			$this->Session->write('login_box_tab', $_POST['tab']);
		}
		die();
	}
	
	function phpinfo() {
		phpinfo();
		die();
	}
	
	// zapise do sesny zvoleny tab (kategorie / priznaky)
	function categories_bothers_tab() {
		if (isset($_POST['tab'])) {
			$this->Session->write('categories_bothers_tab', $_POST['tab']);
		}
		die();
	}
	
	function ajax_we_call_you_request() {
		$result = array(
			'success' => false,
			'message' => ''
		);
		
		if (!isset($_POST['contact'])) {
			$result['message'] = 'Nejsou zadána všechna požadovaná pole';
		} else {
			$contact = $_POST['contact'];
			
			// notifikacni email prodejci
			// vytvorim tridu pro mailer
			App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
			$mail = new phpmailer();
			
			// uvodni nastaveni
			$mail->CharSet = 'utf-8';
			$mail->Hostname = CUST_ROOT;
			$mail->Sender = CUST_MAIL;
			
			// nastavim adresu, od koho se poslal email
			$mail->From     = CUST_MAIL;
			$mail->FromName = CUST_NAME;
			
			$mail->AddAddress(CUST_MAIL, CUST_NAME);
			$mail->AddAddress('brko11@gmail.com', 'Martin Polák');
			$mail->AddAddress('martin@drdla.eu', 'Martin Drdla');
			
			$mail->Subject = 'Nový požadavek na kontakt z www.lekarnatypos.cz';
			$mail->Body = 'Právě byla přijat nový požadavek na kontaktování.' . "\n";
			$mail->Body .= 'Zadané kontaktní údaje: ' . $contact . "\n\n";
			// zmenit na false. adminum nechci posilat grafiku

			$result['success'] = $mail->Send();
			$result['message'] = 'Kontaktní údaje se nepodařilo zpracovat, opakujte prosím akci';
			if ($result['success']) {
				$result['message'] = 'Brzy Vás budeme kontaktovat, děkujeme za Vaši důvěru.';
			}
		}
		echo json_encode($result);
		die();
	}
}
?>
