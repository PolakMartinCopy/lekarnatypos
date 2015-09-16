<?
class Administrator extends AppModel{
	var $name = 'Administrator';

	var $hasMany = array('Ordernote');
	
	/*
	 * Natahne sportnutrition data
	 */
	function import() {
		$this->truncate();
		$snAdministrators = $this->findAllSn();
		foreach ($snAdministrators as $snAdministrator) {
			$administrator = $this->transformSn($snAdministrator);
			$this->create();
			if (!$this->save($administrator)) {
				debug($administrator);
				debug($this->validationErrors);
			}
		}
		return true;
	}
	
	function findAllSn($condition = null) {
		$this->setDataSource('admin');
		$query = '
			SELECT *
			FROM administrators AS SnAdministrator
		';
		if ($condition) {
			$query .= '
				WHERE ' . $condition . '
			';
		}
		$snAdministrators = $this->query($query);
		$this->setDataSource('default');
		return $snAdministrators;
	}
	
	function transformSn($snAdministrator) {
		$administrator = array(
			'Administrator' => array(
				'id' => $snAdministrator['SnAdministrator']['id'],
				'first_name' => $snAdministrator['SnAdministrator']['first_name'],
				'last_name' => $snAdministrator['SnAdministrator']['last_name'],
				'login' => $snAdministrator['SnAdministrator']['login'],
				'password' => $snAdministrator['SnAdministrator']['password'],
			)
		);
	
		return $administrator;
	}
	
	function isHost($id) {
		return $id == HOST_ID;
	}
	
	function hasAccess($id, $controller, $action) {
		// je uzivatel host?
		if ($this->isHost($id)) {
			/* uzivatel HOST muze pouze:
			 * vyhledat produkty				admin/products/index
			 * přidat produkt					admin/products/add
			 * úprava produktu:
			 *		zadání názvu,				admin/products/edit_detail
			 *		krátký popis,				admin/products/edit_detail
			 *		popis,						admin/products/edit_detail
			 *		zadání výrobce,				admin/products/edit_detail
			 *		určení dostupnosti,			admin/products/edit_detail
			 *		zařazení do typu produktu	admin/products/edit_detail
			 *      zařadit do kategorie		admin/products/edit_categories
			 * 		upravit cenu				admin/products/edit_price_list
			 * 		vložit fotografii			admin/products/images_list
			 */
			$host_allowed_sections = array(
				array(
					'controller' => 'administrators',
					'actions' => array('admin_login', 'admin_logout')
				),
				array(
					'controller' => 'products',
					'actions' => array('admin_index', 'admin_add', 'admin_edit_detail', 'admin_edit_categories', 'admin_edit_price_list', 'admin_images_list', 'admin_delete', 'admin_attributes_list', 'admin_add_subproducts')
				),
				array(
					'controller' => 'images',
					'actions' => array('admin_add', 'admin_delete', 'admin_move_up', 'admin_move_down')
				),
				array(
					'controller' => 'categories_products',
					'actions' => array('admin_delete', 'admin_add')
				),
				array(
					'controller' => 'subproducts',
					'actions' => array('admin_control')
				)
			);
			$accessAllowed = false;
			foreach ($host_allowed_sections as $host_allowed_section) {
				if ($host_allowed_section['controller'] == $controller) {
					if (in_array($action, $host_allowed_section['actions'])) {
						$accessAllowed = true;
					}
					break;
				}
			}
			return $accessAllowed;
		}
		return true;
	}
}
?>
