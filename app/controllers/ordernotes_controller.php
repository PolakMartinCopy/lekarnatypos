<?php
class OrdernotesController extends AppController {

	var $name = 'Ordernotes';
	
	function admin_add() {
		if (isset($this->data)) {
			if ($this->Ordernote->save($this->data)) {
				$this->Session->setFlash('Poznámka byla uložena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(base64_decode($this->params['named']['backtrace_url']));
			} else {
				$this->Session->setFlash('Poznámku se nepodařilo uložit, opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}
		}
		
		if (isset($this->params['named']['order_id'])) {
			$order_id = $this->params['named']['order_id'];
			$this->set('order_id', $order_id);
			$order = $this->Ordernote->Order->find('first', array(
				'conditions' => array('Order.id' => $order_id),
				'contain' => array(),
				'fields' => array('Order.id', 'Order.status_id')
			));
			$status_id = 0;
			if (!empty($order)) {
				$status_id = $order['Order']['status_id'];
			}
			$this->set('status_id', $status_id);
		} else {
			$this->Session->setFlash('Není zadáno, ke které objednávce chcete přidat poznámku.');
		}
		
		$backtrace_url = '/admin/orders';
		if (isset($this->params['named']['backtrace_url'])) {
			$backtrace_url = base64_decode($this->params['named']['backtrace_url']);
		}
		$this->set('backtrace_url', $backtrace_url);
		
		$administrator_id = $this->Session->read('Administrator.id');
		$this->set('administrator_id', $administrator_id);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

}
?>