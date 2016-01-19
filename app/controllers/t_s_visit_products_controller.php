<?php
class TSVisitProductsController extends AppController {
	var $name = 'TSVisitProducts';
	
	function product_description_shown($product_id = null) {
		if (!$product_id) {
			return false;
		}
		$key = $this->TSVisitProduct->TSVisit->TSCustomerDevice->getKey($this->Cookie, $this->Session);
		$this->TSVisitProduct->sthId = $product_id;
		$this->TSVisitProduct->productDescriptionShow();
		die();
	}
	
	function product_comments_shown($product_id = null) {
		if (!$product_id) {
			return false;
		}
		$key = $this->TSVisitProduct->TSVisit->TSCustomerDevice->getKey($this->Cookie, $this->Session);
		$this->TSVisitProduct->sthId = $product_id;
		$this->TSVisitProduct->productCommentsShow();
		die();
	}
	
	function my_create($id) {
		$key = $this->TSVisitProduct->TSVisit->TSCustomerDevice->getKey($this->Cookie, $this->Session);
		$this->TSVisitProduct->myCreate($id);
		die();
	}
}