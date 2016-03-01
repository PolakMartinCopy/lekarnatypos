<?php
class MissYouAdMailsController extends AppController {
	function send_batch() {
		// zjistim uzivatele, kterym chci poslat email (nebyli u nas dele nez definovany interval a neposlal se jim po danem intervalu email
		$customers = $this->MissYouAdMail->getRecipients();
		foreach ($customers as $customer) {
			// ulozim odeslani emailu
			if ($this->MissYouAdMail->init($customer['Customer']['id'])) {
				$email = $customer['Customer']['email'];
				$subject = $this->MissYouAdMail->subject($customer['Customer']['id']);
				$body = $this->MissYouAdMail->body($customer['Customer']['id']);
				$bodyAlternative = $this->MissYouAdMail->bodyAlternative($customer['Customer']['id']);

				// TODO - odstranit moji emailovou adresu
				$email = 'brko11@gmail.com';
				
				if ($this->MissYouAdMail->sendMail($subject, $body, $bodyAlternative, $email)) {
					$this->MissYouAdMail->setSent($this->MissYouAdMail->id);
				}
			}
		}
		die('here');
	}
	
	function is_opened($cryptId, $cryptEmail = null) {
		echo $this->MissYouAdMail->isOpened($cryptId, $cryptEmail);
		die();
	}
}