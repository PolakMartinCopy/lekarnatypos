<?php
class StatusesController extends AppController {
	var $name = 'Statuses';

	function beforeFilter(){
		// musim si zavolat puvodni beforeFilter
		parent::beforeFilter();
		
		$this->Status->MailTemplate->recursive = -1;
		$mail_templates = $this->Status->MailTemplate->find('all');
		$mail_templates = Set::combine($mail_templates, '{n}.MailTemplate.id', '{n}.MailTemplate.subject');
		$this->set('mail_templates', $mail_templates);
	}

	function admin_index(){
		$statuses = $this->Status->find('all', array(
			'contain' => array('MailTemplate'),
			'order' => array('Status.order' => 'asc')	
		));
		$this->set('statuses', $statuses);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id){
		if ( !isset($this->data) ){
			$this->Status->recursive = -1;
			$this->data = $this->Status->read(null, $id);
			if ( empty($this->data) ){
				$this->Session->setFlash('Neexistující status!', REDESIGN_PATH . 'flash_failure');
				$this->redirect(array('action' => 'index'), null, true);
			}
		} else {
			if ( $this->Status->save($this->data) ){
				$this->Session->setFlash('Status byl upraven!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'edit', $this->Status->id), null, true);
			} else {
				$this->Session->setFlash('Chyba při úpravě statusu!', REDESIGN_PATH . 'flash_failure');
			}
		}
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý stav.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'statuses', 'action' => 'index'));
		}
		
		if ($this->Status->moveUp($id)) {
			$this->Session->setFlash('Stav byl posunut nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Stav se nepodařilo posunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'statuses', 'action' => 'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý stav.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'statuses', 'action' => 'index'));
		}
	
		if ($this->Status->moveDown($id)) {
			$this->Session->setFlash('Stav byl posunut dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Stav se nepodařilo posunout dolů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'statuses', 'action' => 'index'));
	}

	function admin_delete($id){
		if ( !isset($id) ){
			$this->Session->setFlash('Neexistující status!');
			$this->redirect(array('action' => 'index'), null, true);
		} else {
			if ( $this->Status->Order->findCount(array('status_id' => $id)) != 0 ){
				$this->Session->setFlash('Tento status je přiřazen k některým existujícím objednávkám, nelze jej proto vymazat!');
			} else {
				$this->Status->delete($id);
				$this->Session->setFlash('Status byl vymazán!');
			}
			$this->redirect(array('action' => 'index'), null, true);
		}
	}

	function admin_add(){
		if ( isset($this->data) ){
			if ( $this->Status->save($this->data) ){
				$this->Session->setFlash('Status byl uložen!');
				$this->redirect(array('action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání statusu, zkontrolujte prosím všechna pole!');
			}
		}
	}
}
?>