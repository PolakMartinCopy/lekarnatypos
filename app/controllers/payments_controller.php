<?php
class PaymentsController extends AppController {
	var $name = 'Payments';
	
	function admin_index() {
		$payments = $this->Payment->find('all', array(
			'conditions' => array('Payment.active' => true),
			'contain' => array(),
			'order' => array('Payment.order' => 'asc')
		));
		
		$this->set('payments', $payments);
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if ( isset($this->data) ){
			if ( $this->Payment->save($this->data) ){
				$this->Session->setFlash('Způsob platby byl uložen!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání způsobu platby, zkontrolujte prosím všechna pole!', REDESIGN_PATH . 'flash_failure');
			}
		}
		
		$paymentTypes = $this->Payment->PaymentType->find('list');
		$this->set('paymentTypes', $paymentTypes);
		
		$this->set('tiny_mce_elements', 'PaymentDescription');
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID způsobu platby.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
		}
		
		$payment = $this->Payment->find('first', array(
			'conditions' => array('Payment.id' => $id),
			'contain' => array()
		));
		
		if (empty($payment)) {
			$this->Session->setFlash('Neexistující způsob platby!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
		}
		
		if (isset($this->data)) {
			if ( $this->Payment->save($this->data) ){
				$this->Session->setFlash('Způsob platby byl upraven!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Způsob platby se nepodařilo uložit, zkuste to prosím znovu!', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $payment;
		}
		
		$paymentTypes = $this->Payment->PaymentType->find('list');
		$this->set('paymentTypes', $paymentTypes);
		
		$this->set('tiny_mce_elements', 'PaymentDescription');
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID způsobu platby.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'payments', 'action' => 'index'));
		}
		if ($this->Payment->moveUp($id)) {
			$this->Session->setFlash('Způsob platby byl posunut nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Způsob platby se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'payments', 'action' => 'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID způsobu platby.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'payments', 'action' => 'index'));
		}
		if ($this->Payment->moveDown($id)) {
			$this->Session->setFlash('Způsob platby byl posunut dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Způsob platby se nepodařilo přesunout dolů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'payments', 'action' => 'index'));
	}
	
	//soft delete
	function admin_delete($id = null) {
		if ( !isset($id) ){
			$this->Session->setFlash('Neexistující způsob platby!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
		}
		
		if ($this->Payment->delete($id)) {
			$this->Session->setFlash('Způsob platby byl vymazán!', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Způsob platby se nepodařilo odstranit!', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'payments', 'action' => 'index'), null, true);
	}
}
?>