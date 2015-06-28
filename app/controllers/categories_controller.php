<?php
class CategoriesController extends AppController {

	var $name = 'Categories';
	var $helpers = array('Html', 'Form', 'Javascript' );

	function admin_index() {
		$main_categories_ids = $this->Category->pseudo_root_categories_ids();
		// natahnu hlavni kategorie (parent je root)
		$main_categories = $this->Category->find('all', array(
			'conditions' => array(
				'Category.id' => $main_categories_ids,
			),
			'contain' => array(),
			'fields' => array('Category.id', 'Category.name', 'Category.lft', 'Category.rght'),
			'order' => array('Category.lft' => 'asc')
		));

		foreach ($main_categories as &$main_category) {
			$subcategories = $this->Category->find('threaded', array(
				'conditions' => array(
					'Category.lft >' => $main_category['Category']['lft'],
					'Category.rght <' => $main_category['Category']['rght']
				),
				'contain' => array(),
				'order' => array('Category.lft' => 'asc')
			));
			
			$main_category['categories'] = $subcategories;
		}

		$this->set('main_categories', $main_categories);
		
		// nastaveni kvuli otevreni spravneho tabu
		$pseudo_root_category_id = null;
		if (isset($this->params['named']['pseudo_root_category_id']) && !empty($this->params['named']['pseudo_root_category_id'])) {
			$pseudo_root_category_id = $this->params['named']['pseudo_root_category_id'];
		}
		$this->set('pseudo_root_category_id', $pseudo_root_category_id);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_view($id = null) {
		$this->Category->id = $id;
		$this->set('category', $this->Category->read()); // nacitani dat o kategorii
		$fields = array("id", "name"); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$this->set('path_categories', $this->Category->getPath($id, $fields, -1));
		// $path_categories obsahuje cestu do kategorie, kterou prave prohlizim
		$fields = array("id", "name"); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$this->set('childs', $this->Category->children($id, true, $fields, null, null, 1, -1));
		$this->set('opened_category_id', $id);
	}

	function getCategoriesMenuList($opened_category_id = ROOT_CATEGORY_ID){
		$this->Category->unbindModel( array('hasAndBelongsToMany' => array('Product')), false);
		$this->Category->id = $opened_category_id;
		$fields = array('id'); // seznam poli, ktera potrebuji z databaze ohledne kategorii
		$path = $this->Category->getPath($this->Category->id, $fields, -1);
		
		$ids_to_find = Set::extract('/Category/id', $path);
		$ids_to_find[] = ROOT_CATEGORY_ID;

		$categories = $this->Category->find('all', array(
			'conditions' => array("parent_id IN ('" . implode("', '", $ids_to_find) . "')"),
			'order' => array("lft" => 'asc')
		));

		// ke kazde kategorii si zjistim kolik ma
		// v sobe produktu
		foreach( $categories as $key => $value ){
			$categories[$key]['Category']['productCount'] = $this->Category->countProducts($categories[$key]['Category']['id']);
			$categories[$key]['Category']['activeProductCount'] = $this->Category->countActiveProducts($categories[$key]['Category']['id']);
		}

		return array(
			'categories' => $categories, 'ids_to_find' => $ids_to_find, 'opened_category_id' => $opened_category_id
		);
	}
	
	function ajax_menu() {
		$result = array(
			'data' => null,
			'message' => '',
			'success' => false
		);
		
		if (!isset($_POST) || !isset($_POST['openedCategoryId']) || !isset($_POST['isCustomerLoggedIn'])) {
			$result['message'] = 'Nesprávná POST data';
		} else {
			$opened_category_id = $_POST['openedCategoryId'];
			$is_logged = $_POST['isCustomerLoggedIn'];
			
			$categories = $this->Category->getSubcategoriesMenuList($opened_category_id, $is_logged);
			$result['data'] = $categories;
			$result['success'] = true;
		}
		
		echo json_encode($result);
		die();
	}
	
	function getSubcategoriesMenuList($opened_category_id = null, $order_by_opened = false, $show_all = false){
		return $this->Category->getSubcategoriesMenuList($opened_category_id, false, $order_by_opened, $show_all);
	}

	function admin_add($id = null) {
		if (!empty($this->data)) {
			$this->Category->create();

			// musim si zkontrolovat, jestli neni prazdny titulek
			// a popisek a url, pokud jsou, musim si je vygenerovat
			if (empty($this->data['Category']['title']) && !empty($this->data['Category']['name'])) {
				$this->data['Category']['title'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['description']) && !empty($this->data['Category']['name'])) {
				$this->data['Category']['description'] = "Kategorii " . $this->data['Category']['name'] . " naleznete v nabídce online obchodu " . CUST_NAME;
			}
			// pripadne doplnim nadpis a breadcrumb podle nazvu
			if (empty($this->data['Category']['heading']) && !empty($this->data['Category']['name'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb']) && !empty($this->data['Category']['heading'])) {
				$this->data['Category']['breadcrumb'] = $this->data['Category']['heading'];
			}
			
			// zvaliduju data
			$this->Category->set($this->data);
			if ($this->Category->validates()) {
				// nahraju obrazek na disk
				$this->data['Category']['image'] = $this->Category->loadImage($this->data['Category']['image']);
				if ($this->data['Category']['image'] !== false) {
					// ulozim data
					$this->Category->save($this->data['Category']);
					// url musim kontrolovat az po ulozeni, protoze neznam ID kategorie, nejdriv ji ulozim a pak checknu
					if ( empty($this->data['Category']['url']) ){
						$this->data['Category']['url'] = strip_diacritic($this->data['Category']['name'] . '-c'. $this->Category->id);
						// zmenim url a znovu ulozim ( UPDATE )
						$this->Category->save($this->data['Category']);
					}
	
					$this->Session->setFlash('Kategorie byla vložena!', REDESIGN_PATH . 'flash_success');
					$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($this->Category->id)));
				} else {
					$this->Session->setFlash('Kategorie nebyla vložena, nepodařilo se nahrát obrázek kategorie.', REDESIGN_PATH . 'flash_failure');
				}
			} else {
				$this->Session->setFlash('Kategorie nebyla vložena, opravte chyby ve formuláři a uložte jej znovu.', REDESIGN_PATH . 'flash_failure');
			}

			$this->set('parent_id', $this->data['Category']['parent_id']);
			$this->set('opened_category_id', $this->data['Category']['parent_id']);
		} else {
			$this->set('opened_category_id', $id);
			$this->set('parent_id', $id);
			$this->data['Category']['public'] = true;
			$this->data['Category']['active'] = true;
		}
		
		$this->set('tinyMceElement', 'CategoryContent');
		
		// "rootove" kategorie v menu (abych u nich mohl zadavat homepage class kvuli ikone)
		$pseudo_root_categories_ids = $this->Category->pseudo_root_categories_ids();
		$this->set('pseudo_root_categories_ids', $pseudo_root_categories_ids);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}
		
		$category = $this->Category->find('first', array(
			'conditions' => array('Category.id' => $id),
			'contain' => array(),
		));
		if (!empty($category['Category']['image'])) {
			$category['Category']['image'] = $this->Category->image_path . DS . $category['Category']['image'];
		}
		
		if (empty($category)) {
			$this->Session->setFlash('Neexistující kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action'=>'index'), null, true);
		}

		$this->set('opened_category_id', $id);
		
		if (isset($this->data)) {
			// musim si zkontrolovat, jestli neni prazdny titulek
			// a popisek a url, pokud jsou, musim si je vygenerovat
			if (empty($this->data['Category']['url']) && !empty($this->data['Category']['name'])) {
				$this->data['Category']['url'] = strip_diacritic($this->data['Category']['name'] . '-c'. $id);
			}
			
			if (empty($this->data['Category']['title']) && !empty($this->data['Category']['name'])) {
				$this->data['Category']['title'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['description']) && !empty($this->data['Category']['name'])) {
				$this->data['Category']['description'] = "Kategorii " . $this->data['Category']['name'] . " naleznete v nabídce online obchodu " . CUST_NAME;
			}
			// pripadne doplnim nadpis a breadcrumb podle nazvu
			if (empty($this->data['Category']['heading']) && !empty($this->data['Category']['name'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb']) && !empty($this->data['Category']['heading'])) {
				$this->data['Category']['breadcrumb'] = $this->data['Category']['heading'];
			}
			
			if (empty($this->data['Category']['image']['tmp_name'])) {
				unset($this->data['Category']['image']);
			}

			$this->Category->set($this->data);
			if ($this->Category->validates()) {
				// pokud nahravam novy obrazek, updatuju info o obrazku
				$old_image = null;
				if ($this->data['Category']['image']['tmp_name']) {
					if (!empty($category['Category']['image'])) {
						$old_image = $category['Category']['image'];
					}
					$this->data['Category']['image'] = $this->Category->loadImage($this->data['Category']['image']);
				}
				// pokud mam natazeny obrazek
				if (!isset($this->data['Category']['image']) || $this->data['Category']['image'] !== false) {

					if ($this->Category->save($this->data)) {
						if ($old_image && file_exists($old_image)) {
							unlink($old_image);
						}
						$this->Session->setFlash('Kategorie byla upravena.', REDESIGN_PATH . 'flash_success');
						$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($id)));
					} else {
						$this->Session->setFlash('Ukládání kategorie se nezdařilo!', REDESIGN_PATH . 'flash_failure');
					}					
				}
			}
		} else {
			$this->data = $category;
			if (empty($this->data['Category']['heading'])) {
				$this->data['Category']['heading'] = $this->data['Category']['name'];
			}
			if (empty($this->data['Category']['breadcrumb'])) {
				$this->data['Category']['breadcrumb'] = $this->data['Category']['name'];
			}
			unset($this->data['Category']['image']);
		}
		
		$this->set('tinyMceElement', 'CategoryContent');
		$this->set('category', $category);
		
		// "rootove" kategorie v menu (abych u nich mohl zadavat homepage class kvuli ikone)
		$pseudo_root_categories_ids = $this->Category->pseudo_root_categories_ids();
		$this->set('pseudo_root_categories_ids', $pseudo_root_categories_ids);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
	
	// soft delete kategorie
	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'));
		}
		
		if (!$this->Category->hasAny(array('Category.id' => $id))) {
			$this->Session->setFlash('Neexistující kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'));
		}
		
		$category = array(
			'Category' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		if ($this->Category->save($category)) {
			$this->Session->setFlash('Kategorie byla deaktivována.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Kategorii se nepodařilo deaktivovat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($id)));
	}

	// natvrdo smaze kategorii ze systemu
	function admin_delete_from_db($id = null) {
		if (!$id) {
			$this->Session->setFlash('Neznámá kategorie.', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}

		// zjistim si pocet deti a jestli jsou v kategorii nejake produkty
		$children = $this->Category->childcount($id);
		$productCount = $this->Category->countAllProducts($id);

		if ($children != 0) {
			// jestlize obsahuje podkategorie, nedovolim mazat a vypisu hlasku
			$this->Session->setFlash('Kategorii nelze vymazat, protože není prázdná, obsahuje jiné podkategorie!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($id)));
		} elseif ($productCount != 0) {
			// obsahuje produkty, nedovolim mazat
			$this->Session->setFlash('Kategorii nelze vymazat, protože není prázdná, obsahuje produkty!', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($id)));
		}

		$this->Category->id = $id;
		$category = $this->Category->read('parent_id');
		if ($this->Category->delete($id)) {
			$this->Session->setFlash('Kategorie byla vymazána.', REDESIGN_PATH . 'flash_success');
		} else {
			$this->Session->setFlash('Kategorii se nepodařilo vymazat.', REDESIGN_PATH . 'flash_failure');
		}
		$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($category['Category']['parent_id'])));
	}

	function getCategoriesList($active_id = ROOT_CATEGORY_ID){
		$this->set('list', $this->find('all'));
	}

	function admin_list_products($category_id) {
		$this->paginate['CategoriesProduct'] = array(
			'contain' => 'Product',
			'order' => array('Product.active' => 'desc')
		);
		$data = $this->paginate('CategoriesProduct', array('category_id' => $category_id) );
		$this->set(
			'products', $data
		);
		$this->set('opened_category_id', $category_id);
	}

	function admin_moveup($id){
		// otestuju si, jestli je nastavene id
		// a je ruzne od id korenove kategorie
		if ( isset($id) && $id != ROOT_CATEGORY_ID ){
			if ( $this->Category->moveup($id) ){
				$this->Session->setFlash('Kategorie byla posunuta nahoru.', REDESIGN_PATH . 'flash_success');
			} else {
				$this->Session->setFlash('Kategorii nelze posunout, je na nejvyssi možné pozici.', REDESIGN_PATH . 'flash_failure');
			}
			$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($id)));
		} else {
			// presmeruju na zakladni stranku
			$this->Session->setFlash('Kategorie neexistuje, nebo se snažíte posunout zakladni kategorii..', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('action' => 'index'));
		}
	}

	function admin_movedown($id){
		// otestuju si, jestli je nastavene id
		// a je ruzne od id korenove kategorie
		if (isset($id) && $id != ROOT_CATEGORY_ID){
			if ( $this->Category->movedown($id) ){
				$this->Session->setFlash('Kategorie byla posunuta dolů.', REDESIGN_PATH . 'flash_success');
			} else {
				$this->Session->setFlash('Kategorii nelze posunout, je na nejnižší možné pozici.', REDESIGN_PATH . 'flash_failure');
			}
			$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($id)));
		} else {
			// presmeruju na zakladni stranku
			$this->Session->setFlash('Kategorie neexistuje, nebo se snažíte posunout zakladní kategorii..', REDESIGN_PATH . 'flash_failure');
			$this->redirect(array('controller' => 'categories', 'action' => 'index'));
		}
	}

	function admin_movenode($id) {
		if (!isset($this->data)) { // formular jeste nebyl odeslan
			// nactu si data o kategorii, s kterou chci pracovat
			$this->Category->recursive = -1;
			$this->data = $this->Category->read(null, $id);

			// nahraju si strukturovany seznam kategorii, vynecham kategorii, kterou chci presunout
			$categories = $this->Category->generatetreelist(array('not' => array('id' => array($id))), '{n}.Category.id', '{n}.Category.name', ' - ');
			$this->set(compact(array('categories')));
		} else {
			$this->Category->id = $id;
			$data = array(
				'parent_id' => $this->data['Category']['target_id']
			);
			$this->Category->save($data, false, array('parent_id'));

			$this->Session->setFlash('Kategorie byla přesunuta do nového uzlu.', REDESIGN_PATH . 'flash_success');
			$this->redirect(array('controller' => 'categories', 'action' => 'index', 'pseudo_root_category_id' => $this->Category->pseudo_root_category_id($id)), null, true);
		}
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	/**
	 * Natahne data ze struktury tabulek
	 */
	function admin_import() {
		$this->Category->import();
		die('here');
	}
	
	function load_images() {
		$unactive_categories = $this->Category->find('all', array(
			'conditions' => array('Category.active' => false),
			'contain' => array(),
			'fields' => array('Category.id')	
		));
		$unactive_categories_ids = array();
		foreach ($unactive_categories as $unactive_category) {
			$unactive_subtree_ids = $this->Category->subtree_ids($unactive_category['Category']['id']);
			$unactive_categories_ids = array_merge($unactive_categories_ids, $unactive_subtree_ids);
		}
		// zjistim aktivni kategorie, ktere nemaji obrazek
		$categories = $this->Category->find('all', array(
			'conditions' => array(
				'Category.id !=' => ROOT_CATEGORY_ID,
				'Category.id NOT IN (' . implode(',', $unactive_categories_ids) . ')',
				'OR' => array(
					array('Category.image IS NULL'),
					array('Category.image' => '')
				)
			),
			'contain' => array(),
			'fields' => array('Category.id', 'Category.name')
		));

		// ktere projdu
		foreach ($categories as $category) {
			$subtree_ids = $this->Category->subtree_ids($category['Category']['id']);
			// najdu jeden produkt v podstromu kategorie
			$product = $this->Category->CategoriesProduct->find('first', array(
				'conditions' => array('CategoriesProduct.category_id' => $subtree_ids),
				'contain' => array(
					'Product' => array(
						'Image' => array(
							'fields' => array('Image.id', 'Image.name')
						),
						'fields' => array('Product.*')
					)
				),
				'fields' => array('CategoriesProduct.id', 'CategoriesProduct.product_id')
			));

			if (isset($product['Product']['Image'][0]['name'])) {
				$tmp_img_name = 'product-images/' . $product['Product']['Image'][0]['name'];
				$tmp_img_name_arr = explode('.', $tmp_img_name);
				$img_name_ext = $tmp_img_name_arr[count($tmp_img_name_arr)-1];
				$img_name = strip_diacritic($category['Category']['name']) . '.' . $img_name_ext;
				$image_data = array(
					'name' => $img_name,
					'tmp_name' => $tmp_img_name	
				);
				$res_img_name = $this->Category->loadImage($image_data);
				if ($res_img_name !== false) {
					$cat_save = array(
						'Category' => array(
							'id' => $category['Category']['id'],
							'image' => $res_img_name
						)
					);
					$this->Category->save($cat_save);

				}
			} else {
				$cat_save = array(
					'Category' => array(
						'id' => $category['Category']['id'],
						'image' => null
					)
				);
				$this->Category->save($cat_save);
			}
		}
		die('hotovo');
	}
	
	function load_descs() {
		$unactive_categories = $this->Category->find('all', array(
			'conditions' => array('Category.active' => false),
			'contain' => array(),
			'fields' => array('Category.id')
		));
		$unactive_categories_ids = array();
		foreach ($unactive_categories as $unactive_category) {
			$unactive_subtree_ids = $this->Category->subtree_ids($unactive_category['Category']['id']);
			$unactive_categories_ids = array_merge($unactive_categories_ids, $unactive_subtree_ids);
		}
		
		$categories = $this->Category->find('all', array(
			'conditions' => array(
				'Category.id !=' => ROOT_CATEGORY_ID,
				'Category.id NOT IN (' . implode(',', $unactive_categories_ids) . ')'
			),
			'contain' => array(),
			'fields' => array('Category.id', 'Category.name')
		));
		
		foreach ($categories as $category) {
			$old_categories = $this->Category->find('all', array(
				'conditions' => array(
					'Category.new_id' => $category['Category']['id'],
					array('Category.content IS NOT NULL'),
					array('Category.content !=' => '')
				),
				'contain' => array(),
			));
			if (!empty($old_categories)) {
				if (count($old_categories) == 1) {
					$category['Category']['content'] = $old_categories[0]['Category']['content'];
					$this->Category->save($category);
				} else {
					debug($category);
					debug($old_categories);
					die();
				}
			}
		}
		die('hotovo');
	}
	

	function admin_resize_images() {
		$categories = $this->Category->find('all', array(
			'conditions' => array(
				'Category.image IS NOT NULL',
				'Category.image !=' => ''
			),
			'contain' => array(),
			'fields' => array('Category.image')
		));
	
		$image_height = 88;
		$image_width = 88;
		
		App::import('Model', 'Image');
		$this->Image = &new Image;
	
		foreach ($categories as $category) {
			$image_name = $category['Category']['image'];
			$image_path = $this->Category->image_path . '/' . $image_name;
			$target_path = $this->Category->image_path . '/' . $image_name;
			if (file_exists($image_path)) {
				$this->Image->resize($image_path, $target_path, $image_width, $image_height);
			}
		}
		die('hotovo');
	}
} // konec definice tridy
?>
