<?php
class ProductPropertiesController extends AppController {
	var $name = 'ProductProperties';
	
	function admin_index() {
		$properties = $this->ProductProperty->find('all', array(
			'conditions' => array(),
			'contain' => array(),
			'fields' => array('*')
		));
		
		$this->set('properties', $properties);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if ( isset($this->data) ){
			if ( $this->ProductProperty->save($this->data) ){
				$this->Session->setFlash('Vlastnost produktu byl uložen!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'product_properties', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání vlastnosti produktu, zkontrolujte prosím všechna pole!', REDESIGN_PATH . 'flash_failure');
			}
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID vlastnosti produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_properties', 'action' => 'index'), null, true);
		}
		
		$property = $this->ProductProperty->find('first', array(
			'conditions' => array('ProductProperty.id' => $id),
			'contain' => array()
		));
		
		if (empty($property)) {
			$this->Session->setFlash('Neexistující vlastnost produktu!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_properties', 'action' => 'index'), null, true);
		}
		
		if (isset($this->data)) {
			if ( $this->ProductProperty->save($this->data) ){
				$this->Session->setFlash('Vlastnost produktu byla upravena!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'product_properties', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Vlastnost produktu se nepodařilo uložit, zkuste to prosím znovu!', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $property;
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID vlastnosti produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_properties', 'action' => 'index'));
		}
		if ($this->ProductProperty->moveUp($id)) {
			$this->Session->setFlash('Vlastnost produktu byla posunuta nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Vlastnost produktu se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'product_properties', 'action' => 'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID vlastnosti produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_properties', 'action' => 'index'));
		}
		if ($this->ProductProperty->moveDown($id)) {
			$this->Session->setFlash('Vlastnost produktu byla posunuta nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Vlastnost produktu se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'product_properties', 'action' => 'index'));
	}

	function admin_delete($id = null) {
		if (!isset($id)) {
			$this->Session->setFlash('Neexistující vlastnost produktu!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_properties', 'action' => 'index'), null, true);
		}
		
		if ($this->ProductProperty->delete($id)) {
			$this->Session->setFlash('Vlastnost produktu byla vymazána!', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Vlastnost produktu se nepodařilo odstranit!', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'product_properties', 'action' => 'index'), null, true);
	}
}