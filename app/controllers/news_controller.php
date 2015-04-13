<?php 
class NewsController extends AppController {
	var $name = 'News';
	
	var $helpers = array('Html', 'Form', 'Javascript');
	
	function admin_index() {
		$news = $this->News->find('all', array(
			'conditions' => array(),
			'contain' => array(),
			'fields' => array('News.id', 'News.title', 'News.first_sentence'),
			'order' => array('News.order' => 'desc')
		));
		
		$this->set('news', $news);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add() {
		if (isset($this->data)) {
			if ($this->News->save($this->data)) {
				$this->Session->setFlash('Aktualita byla úspěšně uložena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Aktualitu se nepodařilo uložit. Opravte chyby ve formuláři a opakujte akci.', REDESIGN_PATH . 'flash_failure');
			}
		}
		
		$this->set('tiny_mce_elements', 'NewsText');
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána aktualita, kterou chcete upravit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		$actuality = $this->News->find('first', array(
			'conditions' => array('News.id' => $id),
			'contain' => array(),
			'fields' => array('News.id', 'News.title', 'News.text')
		));
		
		if (empty($actuality)) {
			$this->Session->setFlash('Aktualita, kterou chcete upravit, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if (isset($this->data)) {
			if ($this->News->save($this->data)) {
				$this->Session->setFlash('Aktualita ' . $this->data['News']['id'] . ' byla úspěšně uložena.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Aktualitu se nepodařilo uložit, opravte chyby ve formuláři a opakujte akci.', REDESIGN_PATH . 'flash_failure');
			}
		} else {
			$this->data = $actuality;
		}
		
		$this->set('actuality', $actuality);
		$this->set('tiny_mce_elements', 'NewsText');
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_move_up($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá aktualita.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => $index));
		}
		// volam metodu pro posunuti DOLU, protoze ve vypisu ma nejnovejsi aktualita nejvyssi order, tim padem mam obracene poradi
		if ($this->News->moveDown($id)) {
			$this->Session->setFlash('Aktualita byla posunuta nahorů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Aktualit se nepodařilo přesunout nahorů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function admin_move_down($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá aktualita.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => $index));
		}
		// volam metodu pro posunuti NAHORU, protoze ve vypisu ma nejnovejsi aktualita nejvyssi order, tim padem mam obracene poradi
		if ($this->News->moveUp($id)) {
			$this->Session->setFlash('Aktualita byla posunuta dolů.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Aktualit se nepodařilo přesunout dolů.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána aktualita, kterou chcete smazat.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		if ($this->News->delete($id)) {
			$this->Session->setFlash('Aktualita '. $id . ' byla úspěšně smazána.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Aktualitu '. $id . ' se nepodařilo smazat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('action' => 'index'));
	}
	
	function index() {
		$this->paginate = array(
			'limit' => 25,
			'conditions' => array(),
			'contain' => array(),
			'fields' => array('News.id', 'News.title', 'News.first_sentence', 'News.czech_date'),
			'order' => array('News.order' => 'desc')
		);
		
		$news = $this->paginate();
		
		$this->set('news', $news);
		$this->layout = REDESIGN_PATH . 'content';
		
		// nastaveni meta tagu
		$title = 'Aktuality';
		$this->set('_title', $title);
		$description = 'Přečtěte si, co nového se děje na webu ' . CUST_NAME;
		$this->set('_description', $description);
		
		// breadcrumbs
		$breadcrumbs = array(
			array(
				'anchor' => 'Aktuality',
				'href' => '/aktuality'
			)
		);
		$this->set('breadcrumbs', $breadcrumbs);
	}
	
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou novinku chcete zobrazit.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		$actuality = $this->News->find('first', array(
			'conditions' => array('News.id' => $id),
			'contain' => array(),
		));
		
		if (empty($actuality)) {
			$this->Session->setFlash('Novinka, kterou chcete zobrazit, neexistuje.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->layout = REDESIGN_PATH . 'content';
		
		$this->set('actuality', $actuality);
		$this->set('_title', $actuality['News']['title']);
		$this->set('_description', $actuality['News']['first_sentence']);
	}
}
?>