<?php 
class MostSoldProductsController extends AppController {
	var $name = 'MostSoldProducts';
	
	function admin_index() {
		if (isset($this->data)) {
			switch ($this->data['MostSoldProduct']['action']) {
				case 'change_image':
					$this->data['MostSoldProduct']['image'] = $this->MostSoldProduct->loadImage($this->data['MostSoldProduct']['image']);
					if ($this->data['MostSoldProduct']['image'] !== false) {
						$old_image = $this->MostSoldProduct->find('first', array(
							'conditions' => array('MostSoldProduct.id' => $this->data['MostSoldProduct']['id']),
							'contain' => array(),
							'fields' => array('MostSoldProduct.id', 'MostSoldProduct.image')
						));
						$old_image = $old_image['MostSoldProduct']['image'];
						if ($this->MostSoldProduct->save($this->data)) {
							// smazu puvodni obrazek
							if ($old_image) {
								$old_image = $this->MostSoldProduct->image_path . '/' . $old_image;
								if (file_exists($old_image)) {
									unlink($this->MostSoldProduct->image_path . '/' . $old_image);
								}
							}
								
							$this->Session->setFlash('Obrázek byl úspěšně nahrán', REDESIGN_PATH . 'flash_success');
						} else {
							$this->Session->setFlash('Obrázek se nepodařilo uložit do systému', REDESIGN_PATH . 'flash_failure');
						}
					} else {
						$this->Session->setFlash('Nepodařilo se nahrát obrázek', REDESIGN_PATH . 'flash_failure');
					}
					$this->redirect(array('controller' => 'most_sold_products', 'action' => 'index'));
					break;
				case 'change_gender':
					if ($this->MostSoldProduct->isMaxReached($this->data['MostSoldProduct']['gender'])) {
						$this->Session->setFlash('Produkt se nepodařilo přidat do seznamu. V seznamu může být maximálně ' . $this->MostSoldProduct->limit . ' produktů pro dané pohlaví.', REDESIGN_PATH . 'flash_failure');
					} elseif ($this->MostSoldProduct->save($this->data)) {
						$this->Session->setFlash('Pohlaví u produktu bylo upraveno', REDESIGN_PATH . 'flash_success');
						$this->redirect(array('controller' => 'most_sold_products', 'action' => 'index'));
					} else {
						$this->Session->setFlash('Pohlaví u produktu se nepodařilo upravit', REDESIGN_PATH . 'flash_failure');
					}
					break;
			}

			
		}
		
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

		foreach ($most_sold as &$product) {
			$product['MostSoldProduct']['image'] = $this->MostSoldProduct->getImage($product['MostSoldProduct']['id']);
			$product['MostSoldProduct']['has_image'] = strstr($product['MostSoldProduct']['image'], $this->MostSoldProduct->image_path);
		}

		$this->set('most_sold', $most_sold);
		$this->set('limit', $this->MostSoldProduct->limit);
		
		$defaultGender = 0;
		if ($this->Session->check('MostSoldProduct.default_gender')) {
			$defaultGender = $this->Session->read('MostSoldProduct.default_gender');
		}
		$this->set('defaultGender',  $defaultGender);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		$result = array(
			'success' => false,
			'message' => ''
		);
		
		if (isset($_POST['gender'])) {
			$gender = $_POST['gender'];
			$this->Session->write('MostSoldProduct.default_gender', $gender);
		
			if (!empty($_POST) && isset($_POST['product_id'])) {
				$product_id = $_POST['product_id'];
				if ($this->MostSoldProduct->isMaxReached($gender)) {
					$result['message'] = 'Produkt se nepodařilo přidat do seznamu. V seznamu může být maximálně ' . $this->MostSoldProduct->limit . ' produktů pro dané pohlaví.';
				} elseif ($this->MostSoldProduct->isIncluded($product_id)) {
					$result['message'] = 'Produkt se nepodařilo přidat do seznamu, protože už tam je.';
				} else {
					$data = array(
						'MostSoldProduct' => array(
							'product_id' => $product_id,
							'gender' => $gender
						)
					);
					if ($this->MostSoldProduct->save($data)) {
						$result['success'] = true;
					} else {
						$result['message'] = 'Produkt se nepodařilo  přidat do seznamu.';
					}
				}
			} else {
				$result['message'] = 'POST data nejsou správně nastavena';
			}
		} else {
			$result['message'] = 'Není zadáno pohlaví';
		}
		
		echo json_encode_result($result);
		die();
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, který produkt chcete odstranit ze seznamu.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->MostSoldProduct->delete($id)) {
			$this->Session->setFlash('Produkt byl úspěšně odstraněn ze seznamu.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo odstranit ze seznamu.', REDESIGN_PATH . 'flash_failure');
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
				$moved = $this->MostSoldProduct->find('first', array(
					'conditions' => array('MostSoldProduct.id' => $moved_id),
					'contain' => array(),
					'fields' => array('MostSoldProduct.id', 'MostSoldProduct.order')
				));
				$rec = $this->MostSoldProduct->find('first', array(
					'conditions' => array('MostSoldProduct.id' => $_POST['prevId']),
					'contain' => array(),
					'fields' => array('MostSoldProduct.id', 'MostSoldProduct.order')
				));

				if (!empty($rec)) {
					$order = $rec['MostSoldProduct']['order'];
					// pokud posoouvam nahoru, k poradi elementu na predchozim radku pricitam jednicku
					if ($rec['MostSoldProduct']['order'] < $moved['MostSoldProduct']['order']) {
						$order = $rec['MostSoldProduct']['order'] + 1;
					}
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
}
?>
