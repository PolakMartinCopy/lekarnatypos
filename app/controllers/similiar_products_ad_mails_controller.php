<?php
class SimilarProductsAdMailsController extends AppController {
	function send_batch() {
		// zjistim uzivatele, kterym chci poslat email (nebyli u nas dele nez definovany interval a neposlal se jim po danem intervalu email
		$customers = $this->SimilarProductsAdMails->getRecipients();
		foreach ($customers as $customer) {
			// ulozim odeslani emailu
			if ($this->SimilarProductsAdMails->init($customer['Customer']['id'])) {
				$email = $customer['Customer']['email'];
				$subject = $this->SimilarProductsAdMails->subject($customer['Customer']['id']);
				$body = $this->SimilarProductsAdMails->body($customer['Customer']['id']);
				$bodyAlternative = $this->SimilarProductsAdMails->bodyAlternative($customer['Customer']['id']);

				// TODO - odstranit moji emailovou adresu
				$email = 'brko11@gmail.com';
				
				if ($this->SimilarProductsAdMails->sendMail($subject, $body, $bodyAlternative, $email)) {
					$this->SimilarProductsAdMails->setSent($this->MissYouAdMail->id);
				}
			}
		}
		die('here');
	}
	
	function is_opened($cryptId, $cryptEmail = null) {
		echo $this->SimilarProductsAdMails->isOpened($cryptId, $cryptEmail);
		die();
	}
}