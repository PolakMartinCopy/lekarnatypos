<?php 
class DiscountedProductsController extends AppController {
	var $name = 'DiscountedProducts';
	
	function admin_index() {
		$discounted = $this->DiscountedProduct->find('all', array(
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

		$this->set('discounted', $discounted);
		$this->set('limit', $this->DiscountedProduct->limit);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		$result = array(
			'success' => false,
			'message' => ''
		);
		
		if (!empty($_POST) && isset($_POST['product_id'])) {
			$product_id = $_POST['product_id'];
			if ($this->DiscountedProduct->isMaxReached()) {
				$result['message'] = 'Produkt se nepodařilo označit jako doporučený. V systému může být maximálně ' . $this->DiscountedProduct->limit . ' doporučených produktů.';
			} elseif ($this->DiscountedProduct->isIncluded($product_id)) {
				$result['message'] = 'Produkt se nepodařilo označit jako doporučený, protože už je tak označený.';
			} else {
				$data = array(
					'DiscountedProduct' => array(
						'product_id' => $product_id
					)
				);
				if ($this->DiscountedProduct->save($data)) {
					$result['success'] = true;
				} else {
					$result['message'] = 'Produkt se nepodařilo označit jako doporučený.';
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
			$this->Session->setFlash('Není zadáno, který produkt chcete odstranit z doporučených.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->DiscountedProduct->delete($id)) {
			$this->Session->setFlash('Produkt byl úspěšně odstraněn z doporučených.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo odstranit z doporučených.', REDESIGN_PATH . 'flash_failure');
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
				$rec = $this->DiscountedProduct->find('first', array(
					'conditions' => array('DiscountedProduct.id' => $_POST['prevId']),
					'contain' => array(),
					'fields' => array('DiscountedProduct.id', 'DiscountedProduct.order')
				));
				
				if (!empty($rec)) {
					$order = $rec['DiscountedProduct']['order'];
				}
			}

			if ($this->DiscountedProduct->moveto($moved_id, $order)) {
				$result['success'] = true;
			} else {
				$result['message'] = 'Nepodařilo se přesunout uzel ' . $moved_id . ' na pozici ' . $order . '.';
			}
		}
		echo json_encode_result($result);
		die();
	}
	
	function admin_import() {
		$this->DiscountedProduct->import();
		die('here');
	}
}
?>
