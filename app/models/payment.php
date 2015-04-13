<?
class Payment extends AppModel {
	var $name = 'Payment';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => 'active'
		)
	);
	
	var $belongsTo = array('PaymentType');
	
	var $hasMany = array('Order');
	
	function delete($id) {
		// pred "smazanim" (deaktivaci) musim dopravu presunout na konec seznamu aktivnich doprav
		while (!$this->islast($id)) {
			$this->moveDown($id);
		}
	
		$payment = array(
			'Payment' => array(
				'id' => $id,
				'active' => false
			)
		);
	
		return $this->save($payment);
	}
	
	function get_data($id){
		$payment = $this->find('first', array(
			'conditions' => array('Payment.id' => $id),
			'contain' => array(),
		));
	
		return $payment;
	}
	
	function findBySnName($snName) {
		$payment = $this->find('first', array(
			'conditions' => array('Payment.sn_name' => $snName),
			'contain' => array()	
		));
		
		return $payment;
	}
}
?>