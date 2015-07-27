<?php
class SMSTemplatesController extends AppController {
	var $name = 'SMSTemplates';
	
	function admin_index() {
		$templates = $this->SMSTemplate->find('all');
		$this->set('templates', $templates);
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if ( isset($this->data) ){
			if ( $this->SMSTemplate->save($this->data) ){
				$this->Session->setFlash('Šablona byla uložena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 's_m_s_templates', 'action' => 'index'));
			}
			$this->Session->setFlash('Šablona nebyla uložena kvuli chybám ve formuláři, zkontrolujte prosím data.', REDESIGN_PATH . 'flash_failure');
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou šablonu chcete upravit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 's_m_s_templates', 'action' => 'index'));
		}
		
		$template = $this->SMSTemplate->find('first', array(
			'conditions' => array('SMSTemplate.id' => $id),
			'contain' => array(),
		));
		
		if (empty($template)) {
			$this->Session->setFlash('Šablona, kterou chcete upravit, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 's_m_s_templates', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->SMSTemplate->save($this->data)) {
				$this->Session->setFlash('Šablona byla upravena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 's_m_s_templates', 'action' => 'index'));
			}
			$this->Session->setFlash('Šablona nebyla uložena kvuli chybám ve formuláři, zkontrolujte prosím data.', REDESIGN_PATH . 'flash_failure');
			
		} else {
			$this->data = $template;
				
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_delete($id = null) {
		$this->Session->setFlash('Šablona nemohla být vymazána.', REDESIGN_PATH . 'flash_failure');
		if ($this->SMSTemplate->delete($id)) {
			$this->Session->setFlash('Šablona byla vymazána.', REDESIGN_PATH . 'flash_success');
		}
		$this->redirect(array('controller' => 's_m_s_templates', 'action' => 'index'));
	}
}