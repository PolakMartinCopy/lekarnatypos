<?php
class AdMailsController extends AppController {
	var $name = 'AdMails';
	
	function send_batch($notificateAdmins = true, $test = true, $date = null) {
		if (!$date) {
			$date = date('Y-m-d');
		}
		$model = $this->modelNames[0];
		// zjistim uzivatele, kterym chci poslat email
		$customers = $this->$model->getRecipients($date);
		foreach ($customers as $customer) {
			// ulozim odeslani emailu
			if ($this->$model->init($customer['Customer']['id'])) {
				$email = $customer['Customer']['email'];
				$subject = $this->$model->subject();
				if (!$body = $this->$model->body($customer['Customer']['id'], $date, $this->$model->campaignName)) {
					continue;
				}
debug($customer);
debug($body);continue;
				$bodyAlternative = $this->$model->bodyAlternative();
				
				if ($test) {
					$email = 'brko11@gmail.com';
				}

				if ($this->$model->sendMail($subject, $body, $bodyAlternative, $email)) {
					$this->$model->setSent($this->$model->id);
					// posilam notifikace administratorum?
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