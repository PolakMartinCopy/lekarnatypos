<?php 
class MostSoldProductsController extends AppController {
	var $name = 'MostSoldProducts';
	
	function admin_index() {
		$most_sold = $this->MostSoldProduct->find('all', array(
			'contain' => array(
				'Product' => array(
					'Availability' => array(
						'fields' => array('Availability.id', 'Availability.cart_allowed')
					),
 					'fields' => array(
						'Product.id',
						'Product.name',
						'Product.active',
						'Product.url',
						'Product.retail_price_with_dph'
					)
				)
			),
		));

		$this->set('most_sold', $most_sold);
		$this->set('limit', $this->MostSoldProduct->limit);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		$result = array(
			'success' => false,
			'message' => ''
		);
		
		if (!empty($_POST) && isset($_POST['product_id'])) {
			$product_id = $_POST['product_id'];
			if ($this->MostSoldProduct->isMaxReached()) {
				$result['message'] = 'Produkt se nepodařilo označit jako nejprodávanější. V systému může být maximálně ' . $this->MostSoldProduct->limit . ' nejprodávanějších produktů.';
			} elseif ($this->MostSoldProduct->isIncluded($product_id)) {
				$result['message'] = 'Produkt se nepodařilo označit jako nejprodávanější, protože už je tak označený.';
			} else {
				$data = array(
					'MostSoldProduct' => array(
						'product_id' => $product_id
					)
				);
				if ($this->MostSoldProduct->save($data)) {
					$result['success'] = true;
				} else {
					$result['message'] = 'Produkt se nepodařilo označit jako nejprodávanější.';
				}
			}
		} else {
			$result['message'] = 'POST data nejsou správně nastavena';
		}
		
		echo json_encode_result($result);
		die();
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který produkt chcete odstranit z nejprodávanějších.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->MostSoldProduct->delete($id)) {
			$this->Session->setFlash('Produkt byl úspěšně odstraněn z nejprodávanějších.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo odstranit z nejprodávanějších.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function admin_sort() {
		$result = array(
			'success' => false,
			'message' => ''
		);
		
		if (!empty($_POST) && isset($_POST['movedId'])) {
			$moved_id = $_POST['movedId'];
			$order = 1;
			
			if (isset($_POST['prevId'])) {
				$rec = $this->MostSoldProduct->find('first', array(
					'conditions' => array('MostSoldProduct.id' => $_POST['prevId']),
					'contain' => array(),
					'fields' => array('MostSoldProduct.id', 'MostSoldProduct.order')
				));
				
				if (!empty($rec)) {
					$order = $rec['MostSoldProduct']['order'];
				}
			}

			if ($this->MostSoldProduct->moveto($moved_id, $order)) {
				$result['success'] = true;
			} else {
				$result['message'] = 'Nepodařilo se přesunout uzel ' . $moved_id . ' na pozici ' . $order . '.';
			}
		}
		echo json_encode_result($result);
		die();
	}
	
	function admin_import() {
		$this->MostSoldProduct->import();
		die('here');
	}
}
?>