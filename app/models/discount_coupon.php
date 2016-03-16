<?php 
class DiscountCoupon extends AppModel {
	var $name = 'DiscountCoupon';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Customer',
		'Order'
	);
	
	var $hasMany = array(
		'DiscountCouponsProduct' => array(
			'dependent' => true
		),
		'DiscountCouponsCategory' => array(
			'dependent' => true
		)
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název kupónu'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Název kupónu existuje, zadejte jiný'
			)
		),
		'value' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte hodnotu kupónu'
			)
		)
	);
	
	var $checkError = 'Slevový kupón neexistuje.';
	
	function beforeSave() {
		if (array_key_exists('valid_until', $this->data['DiscountCoupon']) && !empty($this->data['DiscountCoupon']['valid_until'])) {
			$this->data['DiscountCoupon']['valid_until'] = cz2db_date($this->data['DiscountCoupon']['valid_until']);
		}
		return true;
	}
	
	function delete($id) {
		$save = array(
			'DiscountCoupon' => array(
				'id' => $id,
				'active' => false
			)
		);
		return $this->save($save);
	}
	
	function checkCart($id, $customerId) {
		$cartId = $this->Order->OrderedProduct->Product->CartsProduct->Cart->get_id();
		
		// zjistim produkty v kosiku
		$cartProducts = $this->DiscountCouponsProduct->Product->CartsProduct->Cart->getProducts($cartId);
		$amount = 0;
		foreach ($cartProducts as $cartProduct) {
			$amount += $cartProduct['CartsProduct']['price_with_dph'] * $cartProduct['CartsProduct']['quantity'];
		}
		$productIds = Set::extract('/CartsProduct/product_id', $cartProducts);
			
		return $this->check($id, $customerId, $productIds, $amount);
	}
	
	function check($id, $customerId, $productIds, $amount) {
		return $this->checkActive($id) && $this->checkCustomer($id, $customerId) && $this->checkValidity($id) && $this->checkUsed($id) && $this->checkProducts($id,  $productIds) && $this->checkMinAmount($id, $amount);
	}
	
	function checkActive($id) {
		// mam kupon?
		if ($coupon = $this->getItemById($id)) {
			// je kupon aktivni? (neni smazany...)
			// NE
			if (!$coupon['DiscountCoupon']['active']) {
				// nastavim chybovou hlasku
				$this->checkError = 'Slevový kupón neexistuje.';
				return false;
			}
			// kupon je aktivni
			return true;
		}
		// nenasel jsem kupon s danym IDckem, kupon nelze pouzit
		return false;
	}
	
	function checkCustomer($id, $customerId) {
		// mam kupon?
		if ($coupon = $this->getItemById($id)) {
			// je kupon omezeny na konkretniho zakaznika?
			// ANO
			if (!empty($coupon['DiscountCoupon']['customer_id'])) {
				// sedi mi omezeni na zakaznika?
				// NE
				if ($coupon['DiscountCoupon']['customer_id'] != $customerId) {
					// nastavim chybovou hlasku
					$this->checkError = 'Slevový kupón je určen pro jiného zákazníka a nemůžete jej proto použít.';
					return false;
				}
			}
			// kupon neni omezen na zakaznika nebo omezeni sedi
			return true;
		}
		// nenasel jsem kupon s danym IDckem, kupon nelze pouzit
		return false;
	}
	
	function checkProducts($id, $productIds) {
		// mam kupon?
		if ($coupon = $this->getItemById($id)) {
			// je kupon omezeny na konkretni produkty?
			// ANO
			if ($this->DiscountCouponsProduct->hasAny(array('DiscountCouponsProduct.discount_coupon_id' => $id)) || $this->DiscountCouponsCategory->hasAny(array('DiscountCouponsCategory.discount_coupon_id' => $id))) {
				// zjistim vsechny produkty s vazbou na dany kupon
				// nejdriv prime vazby na produkt
				$productProductIds = $this->DiscountCouponsProduct->getProductIds($id);
				// pridam navazane pres kategorie
				$categoryProductIds = $this->DiscountCouponsCategory->getProductIds($id);
				$couponProductIds = array_unique(array_merge($productProductIds, $categoryProductIds));
				// vratim, jestli mam v seznamu aspon jeden produkt z tech, co jsou navazane na kupon
				$intersect = array_intersect($couponProductIds, $productIds);
				if (empty($intersect)) {
					$this->checkError = 'Slevový kupón je určen pro nákup jiných produktů a nemůžete jej proto použít.';
					return false;
				}
			}
			// kupon neni omezen na produkty nebo omezeni sedi
			return true;
		}
		// nenasel jsem kupon s danym IDckem, kupon nelze pouzit.
		return false;
	}
	
	function checkValidity($id) {
		// mam kupon?
		if ($coupon = $this->getItemById($id)) {
			// je kupon omezeny na datum?
			// ANO
			if ($coupon['DiscountCoupon']['valid_until']) {
				$today = date('Y-m-d');
				// uplynula doba platnosti kuponu?
				// ANO
				if ($coupon['DiscountCoupon']['valid_until'] < $today) {
					$this->checkError = 'Doba platnosti kupónu vypršela a nemůžete jej proto použít.';
					return false;
				}
			}
			// kupon neni omezen datem nebo omezeni sedi
			return true;
		}
		// nenasel jsem kupon s danym IDckem, kupon nelze pouzit
		return false;
	}
	
	function checkUsed($id) {
		// mam kupon?
		if ($coupon = $this->getItemById($id)) {
			// byl uz kupon pouzity?
			if ($coupon['DiscountCoupon']['order_id']) {
				$this->checkError = 'Kupón už byl využit dříve a nemůžete jej proto použít znovu.';
				return false;
			}
			// kupon nebyl dosud pouzit
			return true;
		}
		// nenasel jsem kupon s danym IDckem, kupon nelze pouzit
		return false;
	}
	
	function checkMinAmount($id, $amount) {
		// mam kupon?
		if ($coupon = $this->getItemById($id)) {
			// je hodnota vyšší než požadovaná mez?
			if ($coupon['DiscountCoupon']['min_amount'] && $coupon['DiscountCoupon']['min_amount'] > $amount) {
				$this->checkError = 'Minimální hodnota pro uplatnění kupónu je ' . front_end_display_price($coupon['DiscountCoupon']['min_amount']) . '&nbsp;Kč, přidejte prosím další zboží do objednávky.';
				return false;
			}
			// kupon nema danou minimalni mez nebo je hodnota vyšší než požadovaná mez
			return true;
		}
		// nenasel jsem kupon s danym IDckem, kupon nelze pouzit
		return false;
	}
	
	// generovani kuponu
	function generateName($length = 10) {
		$base = md5(time() . Configure::read('Security.salt'));
		$startMax = strlen($base) - $length;
		$start = rand(1, $startMax);
		$name = substr($base, $start, $length);
		if ($this->hasAny(array('DiscountCoupon.name' => $name))) {
			return $this->generateName();
		}
		return $name;
	}
	
	function filterCustomersData($data) {
		return $this->filterRelatedModelData('customer_id', $data);
	}
	
	function filterProductsData($data) {
		return $this->filterRelatedModelData('product_id', $data);
	}
	
	function filterCategoriesData($data) {
		return $this->filterRelatedModelData('category_id', $data);
	}
	
	static function filterRelatedModelData($field, $data) {
		$res = array();
		foreach ($data as $item) {
			if (!empty($item[$field])) {
				$res[] = $item;
			}
		}
		return $res;
	}
	
	function do_form_search($conditions, $data) {
		$conditions[] = array(
			'OR' => array(
				'(' . $this->Customer->virtualFields['name'] . ' LIKE "%%' . $data['DiscountCoupon']['query'] . '%%")',
				'(Customer.email LIKE "%%' . $data['DiscountCoupon']['query'] . '%%")',
				'(Customer.phone LIKE "%%' . $data['DiscountCoupon']['query'] . '%%")',
				'(Product.name LIKE "%%' . $data['DiscountCoupon']['query'] . '%%")',
				'(Category.name LIKE "%%' . $data['DiscountCoupon']['query'] . '%%")',
				'(DiscountCoupon.name LIKE "%%' . $data['DiscountCoupon']['query'] . '%%")'
			)
		);
		return $conditions;
	}
}
?>