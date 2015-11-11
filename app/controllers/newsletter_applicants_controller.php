<?php
class NewsletterApplicantsController extends AppController {
	var $name = 'NewsletterApplicants';
	
	function ajax_add() {
		$result = array(
			'success' => false,
			'message' => ''
		);
		
		if (!isset($_POST['firstName']) || !isset($_POST['lastName']) || !isset($_POST['email']) || !isset($_POST['modalWindowIdentifier'])) {
			$result['message'] = 'Nejsou zadána všechna požadovaná pole';
		} else {
			$first_name = $_POST['firstName'];
			$last_name = $_POST['lastName'];
			$email = $_POST['email'];
			$modal_window_identifier = $_POST['modalWindowIdentifier'];
			$save = array(
				'NewsletterApplicant' => array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $email,
					'modal_window_identifier' => $modal_window_identifier
				)
			);
			
			if ($this->NewsletterApplicant->save($save)) {
				$result['success'] = true;
				$result['message'] = 'Vaše údaje byly uloženy. Děkujeme.';
			} else {
				$result['message'] = $this->NewsletterApplicant->validationErrors;
			}
		}
		echo json_encode($result);
		die();
	}
}