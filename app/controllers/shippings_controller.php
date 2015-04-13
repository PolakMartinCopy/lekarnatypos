<?php
class ShippingsController extends AppController {

	var $name = 'Shippings';
	
	function admin_index(){
		$shippings = $this->Shipping->find('all', array(
			'conditions' => array('Shipping.active' => true),
			'contain' => array(),
			'order' => array('Shipping.order' => 'asc')
		));
		$this->set('shippings', $shippings);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	

	function admin_add(){
		if ( isset($this->data) ){
			if ( $this->Shipping->save($this->data) ){
				$this->Session->setFlash('Způsob dopravy byl uložen!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Chyba při ukládání způsobu dopravy, zkontrolujte prosím všechna pole!', REDESIGN_PATH . 'flash_failure');
			}
		}
		
		$taxClasses = $this->Shipping->TaxClass->find('list');
		$this->set('taxClasses', $taxClasses);
		
		$this->set('tiny_mce_elements', 'ShippingDescription');
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id = null){
		if (!$id){
			$this->Session->setFlash('Není definováno ID způsobu dopravy.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
		}
		
		$shipping = $this->Shipping->find('first', array(
			'conditions' => array('Shipping.id' => $id),
			'contain' => array()
		));
		
		if (empty($shipping)) {
			$this->Session->setFlash('Neexistující způsob dopravy!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
		}
		
		if (isset($this->data)) {
			if ( $this->Shipping->save($this->data) ){
				$this->Session->setFlash('Způsob dopravy byl upraven!', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
			} else {
				$this->Session->setFlash('Způsob dopravy se nepodařilo uložit, zkuste to prosím znovu!', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $shipping;
		}
		
		$taxClasses = $this->Shipping->TaxClass->find('list');
		$this->set('taxClasses', $taxClasses);
		
		$this->set('tiny_mce_elements', 'ShippingDescription');
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID způsobu dopravy.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'));
		}
		if ($this->Shipping->moveUp($id)) {
			$this->Session->setFlash('Způsob dopravy byl posunut nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Způsob dopravy se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'shippings', 'action' => 'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id){
			$this->Session->setFlash('Není definováno ID způsobu dopravy.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'));
		}
		if ($this->Shipping->moveDown($id)) {
			$this->Session->setFlash('Způsob dopravy byl posunut dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Způsob dopravy se nepodařilo přesunout dolů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'shippings', 'action' => 'index'));
	}
	
	//soft delete
	function admin_delete($id = null){
		if ( !isset($id) ){
			$this->Session->setFlash('Neexistující způsob dopravy!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
		}
		
		if ($this->Shipping->delete($id)) {
			$this->Session->setFlash('Způsob dopravy byl vymazán!', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Způsob dopravy se nepodařilo odstranit!', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'shippings', 'action' => 'index'), null, true);
	}
	
}
?>