<?php
class TaxClassesController extends AppController {

	var $name = 'TaxClasses';
	var $helpers = array('Html', 'Form', 'Javascript' );


	function admin_index(){
		$tax_classes = $this->TaxClass->find('all', array(
			'conditions' => array('TaxClass.active'),
			'contain' => array(),
			'order' => array('TaxClass.order' => 'asc')
		));
		
		$this->set('tax_classes', $tax_classes);
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if (!empty($this->data)) {
			if ( $this->TaxClass->save($this->data) ) {
				$this->Session->setFlash('Daňová třída byla uložena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action'=>'index'), null, true);
			} else {
				$this->Session->setFlash('Daňová třída nemohla být uložena, vyplňte prosím správně všechna pole.', REDESIGN_PATH . 'flash_failure');
			}
		}
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id = null){
		if (!$id) {
			$this->Session->setFlash('Neexistující daňová třída.', REDESIGN_PATH . 'flass_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		$tax_class = $this->TaxClass->find('first', array(
			'conditions' => array('TaxClass.id' => $id),
			'contain' => array()
		));
		
		if (empty($tax_class)) {
			$this->Session->setFlash('Neexistující daňová třída.', REDESIGN_PATH . 'flass_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		$this->set('tax_class', $tax_class);
		
		if (!empty($this->data)) {
			if ($this->TaxClass->save($this->data)) {
				$this->Session->setFlash('Daňová třída byla uložena.', REDESIGN_PATH . 'flass_success');
				$this->redirect(array('action'=>'index'), null, true);
			} else {
				$this->Session->setFlash('Daňová třída nemohla být uložena, vyplňte prosím správně všechna pole.', REDESIGN_PATH . 'flass_failure');
			}
		} else {
			$this->data = $tax_class;
		}
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující daňová třída.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		if ($this->TaxClass->moveUp($id)) {
			$this->Session->setFlash('Daňová třída byla přesunuta nahoru.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Daňovou třídu se nepodařilo přesunout nahoru.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action'=>'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující daňová třída.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
	
		if ($this->TaxClass->moveDown($id)) {
			$this->Session->setFlash('Daňová třída byla přesunuta dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Daňovou třídu se nepodařilo přesunout dolů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action'=>'index'));
	}

	function admin_delete($id = null){
		if (!$id) {
			$this->Session->setFlash('Neexistující daňová třída.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		if ($this->TaxClass->delete($id)){
			$this->Session->setFlash('Daňová třída byla smazána.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Daňovou třídu se nepodařilo smazat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action'=>'index'), null, true);
	}
}
?>