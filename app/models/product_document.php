<?php 
class ProductDocument extends AppModel {
	var $name = 'ProductDocument';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => 'product_id'
		)
	);
	
	var $order = array('ProductDocument.order' => 'asc');
	
	var $belongsTo = array('Product');
	
	var $folder = null;
	
	function __construct() {
		parent::__construct();
		$this->folder = 'files' . DS . 'documents' . DS . 'product_documents';
	}
	
	function deleteDocument($id) {
		if ( !$id ){
			return false;
		} else {
			$document = $this->find('first', array(
				'conditions' => array('ProductDocument.id' => $id),
				'contain' => array(),
				'fields' => array('ProductDocument.id', 'ProductDocument.name')
			));
			if (empty($document)) {
				return false;
			}
			// smazu z disku
			$file_name = $this->folder . DS . $document['ProductDocument']['name'];
			if (file_exists($file_name)) {
				unlink($file_name);
			}
			// smazu z databaze
			return $this->delete($id);
		}
	}
	
	function deleteAllDocuments($id) {
		if ( !$id ){
			return false;
		} else {
			$documents = $this->find('all', array(
				'conditions' => array('ProductDocument.product_id' => $id),
				'fields' => array('ProductDocument.id')
			));
			foreach ($documents as $document){
				$this->deleteDocument($document['ProductDocument']['id']);
			}
		}
	}
	
	function checkName($name_in){
		// predpokladam, ze obrazek s
		// takovym jmenem neexistuje
		$name_out = $name_in;
	
		// pokud existuje, musim zkouset zda neexistuje s _{n}
		// az dokud se najde jmeno s cislem, ktere neexistuje
		if ( file_exists($name_in) ){
			$i = 1;
			$new_fileName = str_replace('.', '_' . $i . '.', $name_in);
			while ( file_exists($new_fileName ) ){
				$i++;
				$new_fileName = str_replace('.', '_' . $i . '.', $name_in);
			}
			$name_out = $new_fileName;
		}
		return $name_out;
	}
}
?>