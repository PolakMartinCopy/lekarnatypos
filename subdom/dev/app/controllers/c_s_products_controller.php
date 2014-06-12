<?php 
class CSProductsController extends AppController {
	var $name = 'CSProducts';
	
	var $left_menu_list = array('c_s_products');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'c_s_products');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		// reset filtru
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'c_s_products') {
			$this->Session->delete('Search.CSProductSearch2');
			$this->redirect(array('controller' => 'c_s_products', 'action' => 'index'));
		}
	
		// inicializace vyhledavacich podminek
		$conditions = array('CSProduct.active' => true);
	
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['CSProductSearch2']['CSProduct']['search_form']) && $this->data['CSProductSearch2']['CSProduct']['search_form'] == 1 ){
			$this->Session->write('Search.CSProductSearch2', $this->data['CSProductSearch2']);
			$conditions = $this->CSProduct->do_form_search($conditions, $this->data['CSProductSearch2']);
		} elseif ($this->Session->check('Search.CSProductSearch2')) {
			$this->data['CSProductSearch2'] = $this->Session->read('Search.CSProductSearch2');
			$conditions = $this->CSProduct->do_form_search($conditions, $this->data['CSProductSearch2']);
		}
	
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array('Unit', 'TaxClass'),
			'fields' => array('CSProduct.id', 'CSProduct.vzp_code', 'CSProduct.group_code', 'CSProduct.name', 'CSProduct.store_price', 'CSProduct.quantity', 'Unit.name', 'TaxClass.value'),
			'order' => array('CSProduct.name' => 'asc'),
			'limit' => 40
		);
	
		$products = $this->paginate();
	
		$find = $this->paginate;
		// parametry pro xls export
		unset($find['limit']);
		unset($find['fields']);
	
		// pole pro xls export
		$export_fields = array(
			array('field' => 'CSProduct.id', 'position' => '["CSProduct"]["id"]', 'alias' => 'CSProduct.id'),
			array('field' => 'CSProduct.name', 'position' => '["CSProduct"]["name"]', 'alias' => 'CSProduct.name'),
			array('field' => 'CSProduct.vzp_code', 'position' => '["CSProduct"]["vzp_code"]', 'alias' => 'CSProduct.vzp_code'),
			array('field' => 'CSProduct.group_code', 'position' => '["CSProduct"]["group_code"]', 'alias' => 'CSProduct.group_code'),
			array('field' => 'Unit.name', 'position' => '["Unit"]["name"]', 'alias' => 'Unit.name'),
			array('field' => 'CSProduct.store_price', 'position' => '["CSProduct"]["store_price"]', 'alias' => 'CSProduct.store_price'),
			array('field' => 'CSProduct.quantity', 'position' => '["CSProduct"]["quantity"]', 'alias' => 'CSProduct.quantity'),
			array('field' => 'TaxClass.value', 'position' => '["TaxClass"]["value"]', 'alias' => 'TaxClass.value')
		);

		$this->set(compact('products', 'find', 'export_fields'));
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->CSProduct->save($this->data)) {
				$this->Session->setFlash('Zboží bylo vloženo do číselníku');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Zboží se nepodařilo vložit do číselníku, opravte chyby ve formuláři a opakujte akci');
			}
		}
	
		$units = $this->CSProduct->Unit->find('list', array(
			'order' => array('Unit.name' => 'asc')
		));
		$this->set('units', $units);
		
		$tax_classes = $this->CSProduct->TaxClass->find('all', array(
			'contain' => array(),
			'fields' => array('TaxClass.id', 'TaxClass.value'),
			'order' => array('TaxClass.value' => 'asc')
		));
		$tax_classes = Set::combine($tax_classes, '{n}.TaxClass.id', '{n}.TaxClass.value');
		$this->set('tax_classes', $tax_classes);
	}
	
	function user_edit($id = null) {
		if (!isset($id)) {
			$this->Session->setFlash('Není zadán produkt, který chcete upravovat');
			$this->redirect(array('action' => 'index'));
		}
	
		$product = $this->CSProduct->find('first', array(
			'conditions' => array('CSProduct.id' => $id),
			'contain' => array()
		));
	
		if (empty($product)) {
			$this->Session->setFlash('Produkt, který chcete upravit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
	
		if (isset($this->data)) {
			if ($this->CSProduct->save($this->data)) {
				$this->Session->setFlash('Produkt byl upraven');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Produkt se nepodařilo upravit, opravte chyby ve formuláři a opakujte akci');
			}
		} else {
			$this->data = $product;
		}
	
		$units = $this->CSProduct->Unit->find('list', array(
			'order' => array('Unit.name' => 'asc')
		));
		$this->set('units', $units);
		
		$tax_classes = $this->CSProduct->TaxClass->find('all', array(
			'contain' => array(),
			'fields' => array('TaxClass.id', 'TaxClass.value'),
			'order' => array('TaxClass.value' => 'asc')
		));
		$tax_classes = Set::combine($tax_classes, '{n}.TaxClass.id', '{n}.TaxClass.value');
		$this->set('tax_classes', $tax_classes);
	}
	
	function user_delete($id = null) {
		// produkt deaktivuju (soft delete), nemazu!!!
		if (!isset($id)) {
			$this->Session->setFlash('Není zadán produkt, který chcete smazat');
			$this->redirect(array('action' => 'index'));
		}
	
		if (!$this->CSProduct->hasAny(array('CSProduct.id' => $id))) {
			$this->Session->setFlash('Produkt, který chcete smazat, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
	
		if ($this->CSProduct->delete($id)) {
			$this->Session->setFlash('Produkt byl odstraněn');
		} else {
			$this->Session->setFlash('Produkt se nepodařilo odstranit, opakujte prosím akci');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function user_autocomplete_list() {
		$term = null;
		if ($_GET['term']) {
			$term = $_GET['term'];
		}
	
		echo $this->CSProduct->autocomplete_list($term);
		die();
	}
}
?>