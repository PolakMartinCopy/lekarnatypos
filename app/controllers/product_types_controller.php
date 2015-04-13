<?php 
class ProductTypesController extends AppController {
	var $name = 'ProductTypes';
	
	function admin_index() {
		$product_types = $this->ProductType->find('all', array(
			'conditions' => array('ProductType.active' => true),
			'contain' => array(),
			'order' => array('ProductType.order' => 'asc')	
		));
		
		$this->set('product_types', $product_types);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if (isset($this->data)) {
			if ($this->ProductType->save($this->data)) {
				$this->Session->setFlash('Typ produktu byl uložen.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Typ produktu se nepodailo uložit. Opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}
		}
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý typ produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
		}
		
		$product_type = $this->ProductType->find('first', array(
			'conditions' => array('ProductType.id' => $id),
			'contain' => array(),
		));
		
		if (empty($product_type)) {
			$this->Session->setFlash('Neexistující typ produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
		}
		
		$this->set('product_type', $product_type);
		
		if (isset($this->data)) {
			if ($this->ProductType->save($this->data)) {
				$this->Session->setFlash('Typ produktu byl upraven.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Typ produktu se nepodařilo upravit.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $product_type;
		}
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý typ produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
		}
		
		if ($this->ProductType->moveUp($id)) {
			$this->Session->setFlash('Produkt byl přesunut nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo přesunout.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámý typ produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
		}
		
		if ($this->ProductType->moveDown($id)) {
			$this->Session->setFlash('Produkt byl přesunut dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo přesunout.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'product_types', 'action' => 'index'));
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neexistující typ produktu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		if ($this->ProductType->delete($id)){
			$this->Session->setFlash('Typ produktu byl smazán.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Typ produktu se nepodařilo smazat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action'=>'index'), null, true);
	}
}
?>