<?php 
class CSStoringsController extends AppController {
	var $name = 'CSStorings';
	
	var $left_menu_list = array('c_s_storings');
	
	function beforeRender() {
		parent::beforeFilter();
		$this->set('active_tab', 'c_s_storings');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		// model, ze ktereho metodu volam
		$model = 'CSStoring';
		
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.' . $model . 'Form');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud chci vysledky vyhledavani
		if (isset($this->data[$model]['search_form']) && $this->data[$model]['search_form'] == 1){
			$this->Session->write('Search.' . $model . 'Form', $this->data);
			$conditions = $this->$model->do_form_search($conditions, $this->data);
		} elseif ($this->Session->check('Search.' . $model . 'Form')) {
			$this->data = $this->Session->read('Search.' . $model . 'Form');
			$conditions = $this->$model->do_form_search($conditions, $this->data);
		}

		// aby mi to radilo i podle poli modelu, ktere nemam primo navazane na delivery note, musim si je naimportovat
		App::import('Model', 'CSProduct');
		$this->$model->CSProduct = new CSProduct;
		App::import('Model', 'Unit');
		$this->$model->Unit = new Unit;
	
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'c_s_transaction_items',
					'alias' => 'CSTransactionItem',
					'type' => 'left',
					'conditions' => array($model . '.id = CSTransactionItem.c_s_storing_id')
				),
				array(
					'table' => 'c_s_products',
					'alias' => 'CSProduct',
					'type' => 'left',
					'conditions' => array('CSTransactionItem.c_s_product_id = CSProduct.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('CSProduct.unit_id = Unit.id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array($model . '.user_id = User.id')
				)
			),
			'fields' => array(
				'CSStoring.id',
				'CSStoring.date',

				'CSTransactionItem.id',
				'CSTransactionItem.price',
				'CSTransactionItem.quantity',
				'CSTransactionItem.c_s_product_name',
	
				'CSProduct.id',
				'CSProduct.vzp_code',
				'CSProduct.group_code',
							
				'Unit.id',
				'Unit.shortcut',
					
				'User.id',
				'User.last_name'
			),
			'order' => array(
				'CSStoring.date' => 'desc',
				'CSStoring.time' => 'desc'
			)
		);
		$storings = $this->paginate();
		$this->set('storings', $storings);

		$this->set('find', $this->paginate);

		$export_fields = $this->$model->export_fields();
		$this->set('export_fields', $export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$users = $this->$model->User->find('all', array(
			'conditions' => $users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('users', $users);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['CSTransactionItem'])) {
				// odnastavim prazdne radky
				foreach ($this->data['CSTransactionItem'] as $index => $transaction_item) {
					if (empty($transaction_item['c_s_product_id']) && empty($transaction_item['quantity']) && empty($transaction_item['price'])) {
						unset($this->data['CSTransactionItem'][$index]);
					}
				}
				if (empty($this->data['CSTransactionItem'])) {
					$this->Session->setFlash('Požadavek k naskladněnění neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->CSStoring->saveAll($this->data)) {
						$this->Session->setFlash('Produkty byly naskladněny');
						$this->redirect(array('action' => 'index'));
					}						
				}
			} else {
				$this->Session->setFlash('Požadavek k naskladnění neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['CSStoring']['date'] = date('d.m.Y');
		}
		
		$this->set('user', $this->user);
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno naskladnění, které chcete upravit.');
			$this->redirect(array('action' => 'index'));
		}
		
		$storing = $this->CSStoring->find('first', array(
			'conditions' => array('CSStoring.id' => $id),
			'contain' => array(
				'CSTransactionItem' => array(
					'fields' => array('CSTransactionItem.id', 'CSTransactionItem.quantity', 'CSTransactionItem.price', 'CSTransactionItem.description', 'CSTransactionItem.c_s_product_id', 'CSTransactionItem.c_s_product_name')
				)
			),
			'fields' => array('CSStoring.id', 'CSStoring.date', 'CSStoring.time', 'CSStoring.note')
		));
		
		if (empty($storing)) {
			$this->Session->setFlash('Naskladnění, které chcete upravit, neexistuje.');
			$this->redirect(array('action' => 'index'));
		}
		
		foreach ($storing['CSTransactionItem'] as &$transaction_item) {
			if (isset($transaction_item['c_s_product_id']) && !empty($transaction_item['c_s_product_id'])) {
				$product = $this->CSStoring->CSTransactionItem->CSProduct->find('first', array(
					'conditions' => array('CSProduct.id' => $transaction_item['c_s_product_id']),
					'contain' => array(),
					'fields' => array('CSProduct.id', 'CSProduct.info')
				));
		
				if (!empty($product)) {
					$transaction_item['CSProduct'] = $product['CSProduct'];
				}
			}
		}
		
		$this->set('storing', $storing);
		
		if (isset($this->data)) {
			if (isset($this->data['CSTransactionItem'])) {
				// odnastavim prazdne radky
				foreach ($this->data['CSTransactionItem'] as $index => $transaction_item) {
					if (empty($transaction_item['c_s_product_id']) && empty($transaction_item['quantity']) && empty($transaction_item['price'])) {
						unset($this->data['CSTransactionItem'][$index]);
					}
				}
				if (empty($this->data['CSTransactionItem'])) {
					$this->Session->setFlash('Požadavek k naskladněnění neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					// pokud naskladneni obsahuje validni data a nebude problem s ulozenim, odstranim vsechny transaction items k danemu naskladneni a vlozim nove
					if ($this->CSStoring->saveAll($this->data)) {
						// musim smazat vsechny polozky, ktere jsou v systemu pro dany zaznam, ale nejsou uz aktivni podle editace (byly odstraneny ze seznamu)
						$to_del_tis = $this->CSStoring->CSTransactionItem->find('all', array(
							'conditions' => array(
								'CSTransactionItem.c_s_storing_id' => $this->CSStoring->id,
								'CSTransactionItem.id NOT IN (' . implode(',', $this->CSStoring->CSTransactionItem->active) . ')'
							),
							'contain' => array(),
							'fields' => array('CSTransactionItem.id')
						));
						foreach ($to_del_tis as $to_del_ti) {
							$this->CSStoring->CSTransactionItem->delete($to_del_ti['CSTransactionItem']['id']);
						}
						
						$this->Session->setFlash('Produkty byly naskladněny');
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash('Produkty nelze naskladnit, opravte chyby ve formuláři a opakujte prosím akci.');
					}
				}
			} else {
				$this->Session->setFlash('Požadavek k naskladnění neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$storing['CSStoring']['date'] = db2cal_date($storing['CSStoring']['date']);
			$this->data = $storing;
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno naskladnění, které chcete odstranit.');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->CSStoring->hasAny(array('CSStoring.id' => $id))) {
			$this->Session->setFlash('Naskladnění, které chcete odstranit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->CSStoring->delete($id)) {
			$this->Session->setFlash('Naskladnění bylo odstraněno');
			$this->redirect(array('action' => 'index'));
		}
	}
}
?>