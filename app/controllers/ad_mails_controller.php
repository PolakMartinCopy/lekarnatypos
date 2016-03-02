<?php
class AdMailsController extends AppController {
	var $name = 'AdMails';
	
	function send_batch($notificateAdmins = true) {
		$model = $this->modelNames[0];
		// zjistim uzivatele, kterym chci poslat email (nebyli u nas dele nez definovany interval a neposlal se jim po danem intervalu email
		$customers = $this->$model->getRecipients();
		foreach ($customers as $customer) {
			// ulozim odeslani emailu
			if ($this->$model->init($customer['Customer']['id'])) {
				$email = $customer['Customer']['email'];
				$subject = $this->$model->subject();
				$body = $this->$model->body($customer['Customer']['id']);
				$bodyAlternative = $this->$model->bodyAlternative();
	
				// TODO - odstranit moji emailovou adresu
				$email = 'brko11@gmail.com';

				if ($this->$model->sendMail($subject, $body, $bodyAlternative, $email)) {
					$this->$model->setSent($this->$model->id);
					
					if ($notificateAdmins) {
						// a pro kontrolu jeste sobe, MD a LN (adresy adminu definovane v metode v bootstrapu)
						$adminSubject = 'Newsletter pro ' . $email;
						notificate_admins($adminSubject, $body);
					}
				}
			}
		}
		die('here');
	}
	
	function is_opened($cryptId, $cryptEmail = null) {
		echo $this->$model->isOpened($cryptId, $cryptEmail);
		die();
	}
}