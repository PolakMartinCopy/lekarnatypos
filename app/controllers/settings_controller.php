<?php 
class SettingsController extends AppController {
	var $name = 'Settings';

	function admin_index() {
		$res = array();
		foreach ($this->Setting->shop_keys as $key) {
			$res[] = array('name' => $key, 'value' => $this->Setting->findValue($key), 'id' => $this->Setting->findId($key));
		}
		$res['Setting'] = $res;
		
		if (isset($this->data)) {
			if ($this->Setting->saveAll($this->data['Setting'])) {
				$this->Session->setFlash('Nastavení obchodu bylo upraveno.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'settings', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Nastavení obchodu se nepodařilo upravit. Opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $res;
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
}
?>