<?php
/**
 * 
 * @author Martin Polak
 * 
 * k modelu neexistuje v DB fyzicky zadna tabulka. Typy CS transakci jsou naskladneni, faktura a dobropis. Data pro model
 * CSTransaction beru jako union pres tyto 3 typy, nad kterym pak provadim veskere dalsi operace
 * 
 * union je definovan v custom data source v app/models/c_s_transactions_datasource.php
 *
 */
class CSTransaction extends AppModel {
	var $name = 'CSTransaction';
	
	var $useDbConfig = 'c_s_transactions';
	
	var $useTable = false;
	
	var $export_file = 'files/c_s_transactions.csv';
	
	function export_fields() {
		$export_fields = array(
				array('field' => 'CSTransaction.item_id', 'position' => '["CSTransaction"]["item_id"]', 'alias' => 'CSTransactionItem.id'),
				array('field' => 'CSTransaction.code', 'position' => '["CSTransaction"]["code"]', 'alias' => 'CSTransaction.code'),
				array('field' => 'CSTransaction.business_partner_name', 'position' => '["CSTransaction"]["business_partner_name"]', 'alias' => 'BusinessPartner.name'),
				array('field' => 'CSTransaction.date_of_issue', 'position' => '["CSTransaction"]["date_of_issue"]', 'alias' => 'CSTransaction.date_of_issue'),
				array('field' => 'CSTransaction.c_s_product_name', 'position' => '["CSTransaction"]["c_s_product_name"]', 'alias' => 'CSTransactionItem.c_s_product_name'),
				array('field' => 'CSTransaction.c_s_product_vzp_code', 'position' => '["CSTransaction"]["c_s_product_vzp_code"]', 'alias' => 'CSProduct.vzp_code'),
				array('field' => 'CSTransaction.c_s_product_group_code', 'position' => '["CSTransaction"]["c_s_product_group_code"]', 'alias' => 'CSProduct.group_code'),
				array('field' => 'CSTransaction.quantity', 'position' => '["CSTransaction"]["quantity"]', 'alias' => 'CSTransactionItem.quantity'),
				array('field' => 'CSTransaction.price', 'position' => '["CSTransaction"]["price"]', 'alias' => 'CSTransactionItem.price'),
				array('field' => 'CSTransaction.unit_shortcut', 'position' => '["CSTransaction"]["unit_shortcut"]', 'alias' => 'Unit.shortcut'),
		);
	
		return $export_fields;
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'CSTransaction.business_partner_name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['ico'])) {
			$conditions[] = 'CSTransaction.business_partner_ico LIKE \'%%' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['dic'])) {
			$conditions[] = 'CSTransaction.business_partner_dic LIKE \'%%' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		if (!empty($data[$this->alias]['date_of_issue_from'])) {
			$date_from = explode('.', $data[$this->alias]['date_of_issue_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions[$this->alias . '.date_of_issue_from >='] = $date_from;
		}
		if (!empty($data[$this->alias]['date_of_issue_from'])) {
			$date_to = explode('.', $data[$this->alias]['date_of_issue_from']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to[0];
			$conditions[$this->alias . '.date_of_issue_from <='] = $date_to;
		}
		if (!empty($data[$this->alias]['code'])) {
			$conditions[] = $this->alias . '.code LIKE \'%%' . $data[$this->alias]['code'];
		}
		if (!empty($data['Product']['group_code'])) {
			$conditions[] = 'CSTransaction.c_s_product_group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (!empty($data['Product']['vzp_code'])) {
			$conditions[] = 'CSTransaction.c_s_product_vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
	
		return $conditions;
	}

}
?>