<?php
class OptionsController extends AppController {

	var $name = 'Options';
	var $helpers = array('Html', 'Form', 'Javascript' );

	function admin_index() {
		$this->Option->recursive = 0;
		$this->paginate = array(
			'conditions' => array('Option.active' => true),
			'contain' => array(),
			'show_all' => true
		);
		
		$options = $this->paginate();

		$this->set('options', $options);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_add() {
		if (!empty($this->data)) {
			if ( $this->Option->hasAny(array('name' => $this->data['Option']['name'])) ){
				$this->Session->setFlash('Hodnota "' . $this->data['Option']['name'] . '" již v databázi figuruje.');
			} else {
				$this->Option->create();
				if ($this->Option->save($this->data)) {
					$this->Session->setFlash('Třída atributů byla uložena.', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('action'=>'index'), null, true);
				} else {
					$this->Session->setFlash('Třída atributů nebyla uložena, vyplňte prosím správně všechna pole.', REDESIGN_PATH . 'flash_failure');
				}
			}
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána třída atributů, kterou chcete upravit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		$option = $this->Option->find('first', array(
			'conditions' => array('Option.id' => $id),
			'contain' => array()
		));
		
		if (empty($option)) {
			$this->Session->setFlash('Třída atributů, kterou chcete upravit, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		if (!empty($this->data)) {
			if ($this->Option->save($this->data)) {
				$this->Session->setFlash('Třída atributů byla upravena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action'=>'index'), null, true);
			} else {
				$this->Session->setFlash('Název nemohl být uložen, vyplňte prosím správně všechna pole.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $option;
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující název.');
			$this->redirect(array('action'=>'index'), null, true);
		}

		if ($this->Option->delete($id)) {
			$this->Session->setFlash('Název byl vymazán.');
			$this->redirect(array('action'=>'index'), null, true);
		}
	}

	function list_opt(){
		$options = $this->Option->generateList(null, array('name' => 'asc'), null, '{n}.Option.id', '{n}.Option.name');
		return $options;
	}
	
	function import() {
		$this->Option->import();
		die('here');
	}
}
?>
