<?php
class CartsController extends AppController {
	var $name = 'Carts';

	var $helpers = array('Html', 'Form', 'Javascript');

	function get_id() {
		return $this->Cart->get_id();
	}
	
	// vysypani kosiku
	function dump() {
		$cart_id = $this->Cart->get_id();
		$success = $this->Cart->CartsProduct->deleteAll(array('cart_id' => $cart_id));
		if ($success) {
			$this->Session->setFlash('Produkty z košíku byly odstraněny.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkty z košíku se nepodařilo odstranit.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'carts_products', 'action' => 'index'));
	}
	
	// vysypani kosiku ajaxem
	function ajax_dump() {
		$result = array(
				'success' => false,
				'message' => ''
		);
	
		$cart_id = $this->Cart->get_id();
		$success = $this->Cart->CartsProduct->deleteAll(array('cart_id' => $cart_id));
		if ($success) {
			$result['success'] = true;
			$result['message'] = 'Produkty z košíku byly odstraněny.';
		} else {
			$result['message'] = 'Produkty z košíku se nepodařilo odstranit.';
		}
	
		echo json_encode($result);
		die();
	}
	
	// slouzi pri prichodu zakaznika z automatizovaneho emailu o zapomenutem kosiku
	// zakaznik kliknutim na odkaz v emailu prijde do shopu a naplni se mu aktualni kosik stejnymi polozkami, jako mel v puvodnim
	// id kosiku je zadano cryptovane, at si nemuze zakaznik testovat kosiky
	function re_build($cryptId = null) {
		$this->Session->setFlash('Omlouváme se, ale obnovení košíku se nezdařilo. Pokračujte prosím ve Vašem nákupu.', REDESIGN_PATH . 'flash_failure');
		if ($cryptId) {
			// vytahnu si idcko kosiku, ze ktereho budu chtit zjisit produkty pro vlozeni do stavajiciho
			$oldId = Security::cipher(urldecode($cryptId), Configure::read('Security.salt'));
			
			// id stavajiciho kosiku
			$newId = $this->Cart->get_id();
			// vysypu kosik
			$this->Cart->dump($newId);
			// naklonuju obsah puvodniho kosiku do noveho
			if ($oldId != $newId && $this->Cart->copy($oldId, $newId)) {
				$this->Session->setFlash('Vítejte zpět! Nyní můžete pokračovat ve Vašem nákupu.', REDESIGN_PATH . 'flash_success');
			}
		}
		$redirect = array('controller' => 'orders', 'action' => 'one_step_order');
		$getParams = utm_parameters_string($this->params['url']);
		if (!empty($getParams)) {
			$redirect['?'] = $getParams;
		}
		
		$this->redirect($redirect);
	}
}
?>
