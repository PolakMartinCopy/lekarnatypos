<?php
class AvailabilitiesController extends AppController {

	var $name = 'Availabilities';

	/**
	 * Zobrazi seznam dostupnosti.
	 *
	 */
	function admin_index(){
		$availabilities = $this->Availability->find('all', array(
			'conditions' => array('Availability.active' => true),
			'contain' => array(),
			'order' => array('Availability.order' => 'asc')
		));
		
		$this->set('availabilities', $availabilities);
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if ( isset($this->data) ){
			if ( $this->Availability->save($this->data) ){
				$this->Session->setFlash('Dostupnost produktu byla uložena!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'availabilities', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání dostupnosti produktu, zkontrolujte prosím všechna pole!', REDESIGN_PATH . 'flash_failure');
			}
		}
	
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	/**
	 * Edituje dostupnost.
	 *
	 * @param int $id
	 */
	function admin_edit($id = null){
		if (!$id){
			$this->Session->setFlash('Není definováno ID dostupnosti.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'availabilities', 'action' => 'index'), null, true);
		}
		
		$availability = $this->Availability->find('first', array(
			'conditions' => array('Availability.id' => $id),
			'contain' => array()
		));
		
		if (empty($availability)) {
			$this->Session->setFlash('Neexistující dostupnost!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'availabilities', 'action' => 'index'), null, true);
		}
		
		if (isset($this->data)) {
			if ( $this->Availability->save($this->data) ){
				$this->Session->setFlash('Dostupnost byla upravena!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'availabilities', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Dostupnost se nepodařilo uložit, zkuste to prosím znovu!', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $availability;
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID dostupnosti.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'availabilities', 'action' => 'index'));
		}
		if ($this->Availability->moveUp($id)) {
			$this->Session->setFlash('Dostupnost byla posunuta nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Dostupnost se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'availabilities', 'action' => 'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID dostupnosti.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'availabilities', 'action' => 'index'));
		}
		if ($this->Availability->moveUp($id)) {
			$this->Session->setFlash('Dostupnost byla posunuta nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Dostupnost se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'availabilities', 'action' => 'index'));
	}
	
	// soft delete
	function admin_delete($id = null) {
		if ( !isset($id) ){
			$this->Session->setFlash('Neexistující dostupnost!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'availabilities', 'action' => 'index'), null, true);
		}
		
		if ($this->Availability->delete($id)) {
			$this->Session->setFlash('Dostupnost byla vymazána!', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Dostupnost se nepodařilo odstranit!', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'availabilities', 'action' => 'index'), null, true);
	}
	
	function import() {
		$this->Availability->import();
		die('here');
	}
	
	function update() {
		$this->Availability->update();
		die('here');
	}
}
?>