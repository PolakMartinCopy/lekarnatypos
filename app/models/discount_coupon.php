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
	
	function checkCart($id, $customerId) {
		$cartId = $this->Order->OrderedProduct->Product->CartsProduct->Cart->get_id();
		
		// zjistim produkty v kosiku
		$cartProductIds = $this->DiscountCouponsProduct->Product->CartsProduct->Cart->getProducts($cartId);
		$cartProductIds = Set::extract('/CartsProduct/product_id', $cartProductIds);
			
		return $this->check($id, $customerId, $cartProductIds);
	}
	
	function check($id, $customerId, $productIds) {
		return $this->checkCustomer($id, $customerId) && $this->checkValidity($id) && $this->checkUsed($id) && $this->checkProducts($id,  $productIds);
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
}
?>