<?php 
class CustomerTypesController extends AppController {
	var $name = 'CustomerTypes';
	
	function admin_index() {
		$customer_types = $this->CustomerType->find('all', array(
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'customer_types',
					'alias' => 'CustomerTypeSubstitution',
					'type' => 'LEFT',
					'conditions' => array('CustomerTypeSubstitution.id = CustomerType.substitute_id')		
				)	
			),
			'fields' => array('CustomerType.id', 'CustomerType.name', 'CustomerTypeSubstitution.name'),
		));
		
		$this->set('customer_types', $customer_types);
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá cenová kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customer_types', 'action' => 'index'));
		}
		
		$customer_type = $this->CustomerType->find('first', array(
			'conditions' => array('CustomerType.id' => $id),
			'contain' => array()
		));
		
		if (empty($customer_type)) {
			$this->Session->setFlash('Neexistující cenová kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'customer_types', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->CustomerType->save($this->data)) {
				$this->Session->setFlash('Cenová kategorie byla upravena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'customer_types', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Cenovou kategorii se nepodařilo upravit.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $customer_type;
		}
		
		$customer_type_substitutions = $this->CustomerType->find('list');
		$this->set('customer_type_substitutions', $customer_type_substitutions);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function import() {
		$this->CustomerType->import();
		die('here');
	}
	
	function update() {
		$this->CustomerType->update();
		die('here');
	}
}
?>