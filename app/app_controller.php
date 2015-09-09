<? class AppController extends Controller {
	// musim si importnout cookie, potrebuju je pouzivat	
	var $components = array('Cookie', 'Session');
	
	var $paginate = array(
		'limit' => 50	
	);
	
	// beforeFilter se provede pri uplne kazde akci, ktera se vykona
	function beforeFilter() {
		Controller::disableCache();
		
		// import tool modelu
		App::import('Model', 'Tool');
		
		// osetruju pristup na /admin/ a na /admin, kdyz nekdo pritupuje na tyto adresy, je presmerovan na stranku s loginem
		if ( $_SERVER['REQUEST_URI'] == '/admin/' || $_SERVER['REQUEST_URI'] == '/admin' ){
			$this->redirect('/admin/administrators/login', null, true);
		}
				
		
		if ( !$this->Session->check('Config') || !$this->Session->check('Config.rand') ) {
			$this->Session->_checkValid();
			if ( !$this->Session->check('Config.rand') ) {
				$this->Session->write(array('Config.rand' => rand()));
			}
		}
		
		// otestuju, zda nekdo chce pristoupit do adminu
		// ale zaroven testuju, zda se nesnazi zalogovat
		//debug($this->params); die();
		if (isset($this->params['admin']) && $this->params['admin'] == '1' && $this->action != 'admin_login') {
			// jestlize clovek pristupuje do adminu
			// a jeste se nezalogoval, musim ho presmerovat na stranku
			// s loginem
			if ( !$this->Session->check('Administrator') ){
				// musim zkontrolovat, zda nema clovek ulozenu cookie,
				// na "dlouhe prihlaseni"
				$cookie = $this->Cookie->read('Administrator');
				if ( !empty($cookie) ){
					App::import('Model', 'Administrator');
					$this->Administrator = new Administrator;
					$admin = $this->Administrator->recursive = -1;
					
					// musim zjistit, jestli heslo ulozene v cookies koresponduje
					// s heslem v databazi
					if ( $cookie['password'] == md5($admin['Administrator']['password'] . Configure::read('Security.salt')) ){
						// jestli ma tak si pretahnu data z cookie do session a jedeme dal
						$this->Session->write('Administrator', $cookie['Administrator']);
					} else {
						// donutim uzivatele zalogovat se
						$this->redirect('/admin/administrators/login', null, true);
					}
				} else {
					// donutim uzivatele zalogovat se
					$this->redirect('/admin/administrators/login/url:' . base64_encode($this->params['url']['url']), null, true);
				}
			}

			// administrator ma session timeout 720 sekund * 10 = 2 hodiny
			Configure::write('Session.timeout', 720);

		}
		
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$this->Setting->init();
	}
	
	function beforeRender() {
		// pokud jsem ve front endu
		if (!isset($this->params['admin'])) {
			// natahnu si modely pro odeslani doporuceni a prihlaseni se k odberu novinek, abych mohl jejich validacni chybove hlasky vypisovat v pohledu (v paticce)
			$this->loadModel('Recommendation');
			$this->loadModel('Subscriber');
			// volam fci, ktera mi pres sesnu zajisti prenost validacnich hlasek mezi modely
			$this->_persistValidation();
			
			App::import('Model', 'Product');
			$this->Product = &new Product;
			
			$opened_category_id = ROOT_CATEGORY_ID;
			if (isset($this->viewVars['opened_category_id'])) {
				$opened_category_id = $this->viewVars['opened_category_id'];
			}
			
			// data o kosiku
			$this->set('carts_stats', $this->Product->CartsProduct->getStats($this->Product->CartsProduct->Cart->get_id()));

/*			// submenu kategorii
			$this->set('categories_submenu', $this->Product->CategoriesProduct->Category->getSubmenuCategories());
*/
/*			// vyrobci do selectu
			$manufacturers_list = $this->Product->Manufacturer->find('list', array(
				'conditions' => array('Manufacturer.active' => true)	
			));
			$this->set('manufacturers_list', $manufacturers_list);
*/			
			// nastaveni aktivniho tabu (kategorie / priznaky), defaultne kategorie
			$categories_bothers_tab = 'categories';
			if ($this->Session->check('categories_bothers_tab')) {
				// jinak nactu ze sesny
				$categories_bothers_tab = $this->Session->read('categories_bothers_tab');
			}
			$this->set('categories_bothers_tab', $categories_bothers_tab);
			
			// je zakaznik zalogovany
			$is_logged_in = false;
			if ($this->Session->check('Customer')) {
				$customer = $this->Session->read('Customer');
				if (isset($customer['id']) && !empty($customer['id']) && !isset($customer['noreg'])) {
					$is_logged_in = true;
				}
			}
			$this->set('is_logged_in', $is_logged_in);
			
			$categories_menu = array();
			$bothers_menu = array();
			if ($this->layout == REDESIGN_PATH . 'content') {
				// nejprodavanejsi produkty
				App::import('Model', 'CustomerType');
				$this->CustomerType = new CustomerType;
				$customer_type_id = $this->CustomerType->get_id($this->Session->read());
				// na obsahove strance chci pouze seznam rootovych kategorii s otevrenym aktualnim podstromem
				$categories_menu = $this->Product->CategoriesProduct->Category->getSidebarMenu($opened_category_id, $this->Session->check('Customer'), false, false, true, 408);
				$bothers_menu = $this->Product->CategoriesProduct->Category->getSidebarMenu($opened_category_id, $this->Session->check('Customer'), false, false, true, 398);
			} elseif ($this->layout == REDESIGN_PATH . 'homepage') {
				if ($_SERVER['REQUEST_URI'] == '/') {
					// uplny strom kategorii - vypisuje se pouze na HP
					$categories_menu = $this->Product->CategoriesProduct->Category->getSidebarMenu($opened_category_id, $this->Session->check('Customer'), false, false, false, 408);
					$bothers_menu = $this->Product->CategoriesProduct->Category->getSidebarMenu($opened_category_id, $this->Session->check('Customer'), false, false, false, 398);
				}
			}
			$this->set('categories_menu', $categories_menu);
			$this->set('bothers_menu', $bothers_menu);
			
			App::import('Model', 'News');
			$this->News = &new News;
			$newest_actualities = $this->News->hp_list();
			$this->set('newest_actualities', $newest_actualities);
		} elseif (isset($this->params['admin']) && $this->params['admin'] == 1) {
			App::import('Model', 'Status');
			$this->Status = &new Status;
			$statuses_menu = $this->Status->find('all', array(
				'contain' => array(),
				'order' => array('Status.order' => 'asc')
			));
			$this->set('statuses_menu', $statuses_menu);
		}
	}
	
	/**
	 * Handles automatic pagination of model records.
	 *
	 * @param mixed $object Model to paginate (e.g: model instance, or 'Model', or 'Model.InnerModel')
	 * @param mixed $scope Conditions to use while paginating
	 * @param array $whitelist List of allowed options for paging
	 * @return array Model query results
	 * @access public
	 * @link http://book.cakephp.org/view/1232/Controller-Setup
	 */
	function paginate($object = null, $scope = array(), $whitelist = array()) {
		if (is_array($object)) {
			$whitelist = $scope;
			$scope = $object;
			$object = null;
		}
		$assoc = null;
	
		if (is_string($object)) {
			$assoc = null;
			if (strpos($object, '.')  !== false) {
				list($object, $assoc) = pluginSplit($object);
			}
	
			if ($assoc && isset($this->{$object}->{$assoc})) {
				$object =& $this->{$object}->{$assoc};
			} elseif (
					$assoc && isset($this->{$this->modelClass}) &&
					isset($this->{$this->modelClass}->{$assoc}
			)) {
				$object =& $this->{$this->modelClass}->{$assoc};
			} elseif (isset($this->{$object})) {
				$object =& $this->{$object};
			} elseif (
					isset($this->{$this->modelClass}) && isset($this->{$this->modelClass}->{$object}
			)) {
				$object =& $this->{$this->modelClass}->{$object};
			}
		} elseif (empty($object) || $object === null) {
			if (isset($this->{$this->modelClass})) {
				$object =& $this->{$this->modelClass};
			} else {
				$className = null;
				$name = $this->uses[0];
				if (strpos($this->uses[0], '.') !== false) {
					list($name, $className) = explode('.', $this->uses[0]);
				}
				if ($className) {
					$object =& $this->{$className};
				} else {
					$object =& $this->{$name};
				}
			}
		}
	
		if (!is_object($object)) {
			trigger_error(sprintf(
			__('Controller::paginate() - can\'t find model %1$s in controller %2$sController',
			true
			), $object, $this->name
			), E_USER_WARNING);
			return array();
		}
		$options = array_merge($this->params, $this->params['url'], $this->passedArgs);
	
		if (isset($this->paginate[$object->alias])) {
			$defaults = $this->paginate[$object->alias];
		} else {
			$defaults = $this->paginate;
		}
	
		if (isset($options['show'])) {
			$options['limit'] = $options['show'];
		}
	
		if (isset($options['sort'])) {
			$direction = null;
			if (isset($options['direction'])) {
				$direction = strtolower($options['direction']);
			}
			if ($direction != 'asc' && $direction != 'desc') {
				$direction = 'asc';
			}
			$options['order'] = array($options['sort'] => $direction);
		}
	
		if (!empty($options['order']) && is_array($options['order'])) {
			$alias = $object->alias ;
			$key = $field = key($options['order']);
	
			if (strpos($key, '.') !== false) {
				list($alias, $field) = explode('.', $key);
			}
			$value = $options['order'][$key];
			unset($options['order'][$key]);
	
			if ($object->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
			} elseif ($object->hasField($field, true)) {
				$options['order'][$field] = $value;
			} elseif (isset($object->{$alias}) && $object->{$alias}->hasField($field)) {
				$options['order'][$alias . '.' . $field] = $value;
			}
		}
		$vars = array('fields', 'order', 'limit', 'page', 'recursive');
		$keys = array_keys($options);
		$count = count($keys);
	
		for ($i = 0; $i < $count; $i++) {
			if (!in_array($keys[$i], $vars, true)) {
				unset($options[$keys[$i]]);
			}
			if (empty($whitelist) && ($keys[$i] === 'fields' || $keys[$i] === 'recursive')) {
				unset($options[$keys[$i]]);
			} elseif (!empty($whitelist) && !in_array($keys[$i], $whitelist)) {
				unset($options[$keys[$i]]);
			}
		}
		$conditions = $fields = $order = $limit = $page = $recursive = null;

		if (!isset($defaults['conditions'])) {
			$defaults['conditions'] = array();
		}
	
		$type = 'all';
	
		if (isset($defaults[0])) {
			$type = $defaults[0];
			unset($defaults[0]);
		}
	
		$options = array_merge(array('page' => 1, 'limit' => 20), $defaults, $options);
		$options['limit'] = (int) $options['limit'];
		if (empty($options['limit']) || $options['limit'] < 1) {
			$options['limit'] = 1;
		}

		extract($options);
	
		if (is_array($scope) && !empty($scope)) {
			$conditions = array_merge($conditions, $scope);
		} elseif (is_string($scope)) {
			$conditions = array($conditions, $scope);
		}
		if ($recursive === null) {
			$recursive = $object->recursive;
		}
	
		$extra = array_diff_key($defaults, compact(
				'conditions', 'fields', 'order', 'limit', 'page', 'recursive'
		));
		if ($type !== 'all') {
			$extra['type'] = $type;
		}

		if (method_exists($object, 'paginateCount')) {
			$count = $object->paginateCount($conditions, $recursive, $extra);
		} else {
			$parameters = compact('conditions');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$count = $object->find('count', array_merge($parameters, $extra));
		}

		// Show all records
		if ((isset($extra['show']) && 'all' == $extra['show']) || (isset($this->params['named']['show']) && 'all' == $this->params['named']['show'])) {
			if ($count != 0) {
				$options['limit'] = $defaults['limit'] = $limit = $count;
			}
		}
		
		$pageCount = intval(ceil($count / $limit));
	
		if ($page === 'last' || $page >= $pageCount) {
			$options['page'] = $page = $pageCount;
		} elseif (intval($page) < 1) {
			$options['page'] = $page = 1;
		}
		$page = $options['page'] = (integer)$page;
	
		if (method_exists($object, 'paginate')) {
			$results = $object->paginate(
					$conditions, $fields, $order, $limit, $page, $recursive, $extra
			);
		} else {
			$parameters = compact('conditions', 'fields', 'order', 'limit', 'page');
			if ($recursive != $object->recursive) {
				$parameters['recursive'] = $recursive;
			}
			$results = $object->find($type, array_merge($parameters, $extra));
		}
		$paging = array(
				'page'		=> $page,
				'current'	=> count($results),
				'count'		=> $count,
				'prevPage'	=> ($page > 1),
				'nextPage'	=> ($count > ($page * $limit)),
				'pageCount'	=> $pageCount,
				'defaults'	=> array_merge(array('limit' => 20, 'step' => 1), $defaults),
				'options'	=> $options
		);
		$this->params['paging'][$object->alias] = $paging;
	
		if (!in_array('Paginator', $this->helpers) && !array_key_exists('Paginator', $this->helpers)) {
			$this->helpers[] = 'Paginator';
		}
		return $results;
	}
	
	function _persistValidation() {
		$args = func_get_args();
	
		if (empty($args)) {
			if ($this->Session->check('Validation')) {
				$validation = $this->Session->read('Validation');
				$this->Session->delete('Validation');
				foreach ($validation as $modelName => $sessData) {
					if ($this->name != $sessData['controller']){
						if (in_array($modelName, $this->modelNames)) {
							$Model =& $this->{$modelName};
						} elseif (ClassRegistry::isKeySet($modelName)) {
							$Model =& ClassRegistry::getObject($modelName);
						} else {
							continue;
						}
	
						$Model->data = $sessData['data'];
						$Model->validationErrors = $sessData['validationErrors'];
						$this->data = Set::merge($sessData['data'],$this->data);
					}
				}
			}
		} else {
			foreach($args as $modelName) {
				if (in_array($modelName, $this->modelNames) && !empty($this->{$modelName}->validationErrors)) {
					$this->Session->write('Validation.'.$modelName, array(
						'controller' => $this->name,
						'data' => $this->{$modelName}->data,
						'validationErrors' => $this->{$modelName}->validationErrors
					));
				}
			}
		}
	}
} // konec tridy
?>
