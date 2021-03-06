<?
class RedirectsController extends AppController {

	var $name = 'Redirects';

	function admin_index(){
		$redirects = $this->Redirect->find('all');
		$this->set('redirects', $redirects);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_add(){
		if ( isset($this->data) ){
			// ostripuju si http www
			$this->data['Redirect']['request_uri'] = str_replace('http://www.' . CUST_ROOT, '', $this->data['Redirect']['request_uri']);
			$this->data['Redirect']['target_uri'] = str_replace('http://www.' . CUST_ROOT, '', $this->data['Redirect']['target_uri']);
			
			if ($this->data['Redirect']['request_uri'] == $this->data['Redirect']['target_uri']) {
				$this->Session->setFlash('Zdrojová a cílová adresa přesměrování jsou stejné, přesměrování nelze uložit', REDESIGN_PATH . 'flash_failure');
				$this->redirect(array('controller' => 'redirects', 'action' => 'index'), null, true);
			}
			
			// musim si najit, jestli uz podobny redirect je zavedeny a upravit presmerovani podle toho
			// hledam zda uz redirect pro toto uri neexistuje
			$r = $this->Redirect->find('all', array(
				'conditions' => array(
					'request_uri' => $this->data['Redirect']['request_uri']
				)
			));
			if ( !empty($r) ){
				$this->Session->setFlash('Presmerovani z <em>' . $this->data['Redirect']['request_uri'] . '</em> už existuje, nelze jej proto zavést znovu.', REDESIGN_PATH . 'flash_failure');
				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);
			}
			
			// hledam zda neexistuji redirecty ktere maji za cil stranku, ktera uz je presmerovana jinam
			// pokud ano, musim je upravit aby se zbytecne neredirectovalo nekolikrat po sobe
			// musim zmenit cil podle 
			$r = $this->Redirect->find('all', array(
				'conditions' => array(
					'request_uri' => $this->data['Redirect']['target_uri']
				)
			));
			if ( !empty($r) ){
				$this->Session->setFlash('Cilova stranka presmerovani <em>' . $this->data['Redirect']['target_uri'] . '</em> je jiz presmerovana na jinou stranku, je potreba upravit skript, aby se s tim umel vyporadat.', REDESIGN_PATH . 'flash_failure');
				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);
			} 
			
			// hledam, zda neexistuji redirecty ktere jsou cilem jineho redirectu
			// pokud ano, musim upravit cile techto redirectu, aby se zbytecne neredirectovalo nekolikrat po sobe
			$r = $this->Redirect->find('all', array(
				'conditions' => array(
					'target_uri' => $this->data['Redirect']['request_uri']
				)
			));
			if ( !empty($r) ){
				foreach ($r as $item) {
					$item['Redirect']['target_uri'] = $this->data['Redirect']['target_uri'];
					$this->Redirect->save($item);
				}
				$this->Redirect->create();
			}
			
			if ( $this->Redirect->save($this->data) ){
				$this->Session->setFlash('Přesměrování bylo uloženo.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);
			}
		}
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_edit($id){
		if ( isset($this->data) ){
			// ostripuju si http www
			$this->data['Redirect']['request_uri'] = str_replace('http://www.' . CUST_ROOT, '', $this->data['Redirect']['request_uri']);
			$this->data['Redirect']['target_uri'] = str_replace('http://www.' . CUST_ROOT, '', $this->data['Redirect']['target_uri']);
			if ( $this->Redirect->save($this->data) ){
				$this->Session->setFlash('Přesměrování bylo upraveno.', REDESIGN_PATH . 'flash_success');
				$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);
			}
		}
		$this->data = $this->Redirect->find('first', array('conditions' => array('id' => $id)));
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	function admin_delete($id){
		if ( $this->Redirect->delete($id) ){
			$this->Session->setFlash('Přesměrování bylo vymazáno.', REDESIGN_PATH . 'flash_success');
			$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);
		}
		$this->Session->setFlash('Přesměrování se nezdařilo vymazat, zkuste to prosím znovu.', REDESIGN_PATH . 'flash_failure');
		$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);
	}
	
	function admin_load() {
		$file = 'files/typos_presmerovani_kategorii.csv';
		$contents = file_get_contents($file);
		$contents = explode("\n", $contents);
		unset($contents[count($contents)-1]);
		
		$redirects = array();
		
		foreach ($contents as $content) {
			if (!list($request_uri, $target_uri) = str_getcsv($content, ';')) {
				debug($content);
				die();
			}
			
			if (!empty($request_uri) && !empty($target_uri)) {
				$request_uri = preg_replace('/"\s+\d+\s\d+"/', '', $request_uri);
				$target_uri = preg_replace('/"\s*$/', '', $target_uri);
				
				$request_uri = trim($request_uri);
				$target_uri = trim($target_uri);

				// musim si najit, jestli uz podobny redirect je zavedeny a upravit presmerovani podle toho
				// hledam zda uz redirect pro toto uri neexistuje
				$r = $this->Redirect->find('all', array(
					'conditions' => array(
						'request_uri' => $request_uri
					)
				));
				if (!empty($r)){
					debug($target_uri);
					debug($request_uri);
					debug($r);
					continue;
				}
					
				// hledam zda neexistuji redirecty ktere maji za cil stranku, ktera uz je presmerovana jinam
				// pokud ano, musim je upravit aby se zbytecne neredirectovalo nekolikrat po sobe
				// musim zmenit cil podle
				$r = $this->Redirect->find('all', array(
					'conditions' => array(
						'request_uri' => $target_uri
					)
				));
				if (!empty($r)){
					debug($target_uri);
					debug($request_uri);
					debug($r);
					continue;
				}
					
				// hledam, zda neexistuji redirecty ktere jsou cilem jineho redirectu
				// pokud ano, musim upravit cile techto redirectu, aby se zbytecne neredirectovalo nekolikrat po sobe
				$r = $this->Redirect->find('all', array(
					'conditions' => array(
						'target_uri' => $request_uri
					)
				));
				
				if (!empty($r)) {
					foreach ($r as $item) {
						$item['Redirect']['target_uri'] = $this->data['Redirect']['target_uri'];
						$redirects[] = array(
							'id' => $item['Redirect']['id'],
							'request_uri' => $item['Redirect']['request_uri'],
							'target_uri' => $target_uri
						);
					}
				}
				
				$redirects[] = array(
					'request_uri' => $request_uri,
					'target_uri' => $target_uri
				);
					
/*				if ( $this->Redirect->save($this->data) ){
					$this->Session->setFlash('Přesměrování bylo uloženo.');
					$this->redirect(array('controller' => 'redirects', 'action' => 'index', 'admin' => true), null, true);
				} */
			}
			
		}
		
		$this->Redirect->saveAll($redirects);
		
		die('here');
	}
}
?>
