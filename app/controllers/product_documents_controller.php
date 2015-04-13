<?php 
class ProductDocumentsController extends AppController {
	var $name = 'ProductDocuments';
	
	function admin_add() {
		if (!empty($this->data) ) {
			// musim si prestrukturovat data
			// proto to co budu ukladat do databaze
			// si budu vkladat do nove promenne
			$new_data = array();

			// prochazim vice souboru a nechci pri jedne chybe
			// prerusit zpracovani, ulozim si chybovky do jedne
			// promenne, na konci si nastavim setFlash
			$message = array();

			// projdu si vsechny formularove prvky pro soubory postupne
			for ( $i = 0; $i < $this->data['ProductDocument']['document_fields']; $i++ ) {
				if ( is_uploaded_file($this->data['ProductDocument']['document' . $i]['tmp_name']) ){
					// musim si zkontrolovat, abych si neprepsal jiz existujici soubor
					$this->data['ProductDocument']['document' . $i]['name'] = strip_diacritic($this->data['ProductDocument']['document' . $i]['name'], false);
					$this->data['ProductDocument']['document' . $i]['name'] = $this->ProductDocument->checkName($this->ProductDocument->folder . DS . $this->data['ProductDocument']['document' . $i]['name']);

					if ( move_uploaded_file($this->data['ProductDocument']['document' . $i]['tmp_name'], $this->data['ProductDocument']['document' . $i]['name']) ){

						// potrebuju zmenit prava
						chmod($this->data['ProductDocument']['document' . $i]['name'], 0644);
						$this->data['ProductDocument']['document' . $i]['name'] = explode("/", $this->data['ProductDocument']['document' . $i]['name']);
						$this->data['ProductDocument']['document' . $i]['name'] = $this->data['ProductDocument']['document' . $i]['name'][count($this->data['ProductDocument']['document' . $i]['name']) -1];
						$new_data[] = array(
							'product_id' => $this->data['ProductDocument']['product_id'],
							'name' => $this->data['ProductDocument']['document' . $i]['name'],
						);
					} else {
						$message[] = 'Souboru <strong>' . $this->data['ProductDocument']['document' . $i]['name'] . '</strong> nemohl být uložen, zkuste to znovu prosím.';
					} // move_uploaded_file
				} // is_uplodaed_file
			} // for cyklus

			// prosel jsem vsechny soubory, musim zpracovat vysledek
			foreach ( $new_data as $data ){
				$this->ProductDocument->create();
				if ( $this->ProductDocument->save(array('ProductDocument' => $data)) ){
					$message[] = 'Soubor ' . $data['name'] . ' byl uložen.';
				} else {
					$message[] = 'Ukládání souboru ' . $data['name'] . ' se nezdařilo!';
				}

				// potrebuju tuto promennou pozdeji
				$product_id = $data['product_id'];
			}
			
			$category_id = null;
			if (isset($this->data['ProductDocument']['category_id'])) {
				$category_id = $this->data['ProductDocument']['category_id'];
			}

			// presmeruju
			$this->Session->setFlash(implode("<br />", $message), REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'edit_documents', $this->data['ProductDocument']['product_id'], (isset($category_id) ? $category_id : null)));
		}
	}
	
	function admin_move_up($id = null, $category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dokument.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		$document = $this->ProductDocument->find('first', array(
			'conditions' => array('ProductDocument.id' => $id),
			'contain' => array(),
			'fields' => array('ProductDocument.product_id')
		));

		if (empty($document)) {
			$this->Session->setFlash('Neexistující dokument.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
		
		if ($this->ProductDocument->moveup($id)) {
			$this->Session->setFlash('Dokument byl posunut nahoru.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Dokument se nepodařilo posunout nahoru.', REDESIGN_PATH . 'flash_failure');
		}
		
		$this->redirect(array('controller' => 'products', 'action' => 'edit_documents', $document['ProductDocument']['product_id'], $category_id));
	}
	
	function admin_move_down($id = null, $category_id) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dokument.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
	
		$document = $this->ProductDocument->find('first', array(
			'conditions' => array('ProductDocument.id' => $id),
			'contain' => array(),
			'fields' => array('ProductDocument.product_id')
		));
	
		if (empty($document)) {
			$this->Session->setFlash('Neexistující dokument.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'products', 'action' => 'index'));
		}
	
		if ($this->ProductDocument->movedown($id)) {
			$this->Session->setFlash('Dokument byl posunut dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Dokument se nepodařilo posunout dolů.', REDESIGN_PATH . 'flash_failure');
		}
	
		$this->redirect(array('controller' => 'products', 'action' => 'edit_documents', $document['ProductDocument']['product_id'], $category_id));
	}
	
	function admin_delete($id = null, $category_id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for document');
			$this->redirect(array('action'=>'index'), null, true);
		}

		// nactu si info o souboru, potrebuju ID produktu kvuli navratovemu URL
		$document = $this->ProductDocument->find('first', array(
			'conditions' => array('ProductDocument.id' => $id),
			'contain' => array(),
			'fields' => array('ProductDocument.id', 'ProductDocument.product_id')
		));
	
		// vymazu zaznam z databaze a presmeruju
		if ($this->ProductDocument->deleteDocument($id)) {
			$this->Session->setFlash('Dokument byl vymazán.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Dokument se nepodařilo vymazat.', REDESIGN_PATH . 'flash_failure');
		}
		
		$this->redirect(array('controller' => 'products', 'action'=>'edit_documents', $document['ProductDocument']['product_id'], $category_id));
	}
}
?>