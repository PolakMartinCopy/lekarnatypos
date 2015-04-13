<?
class Ordernote extends AppModel{
	var $name = 'Ordernote';
	var $order = array(
		'Ordernote.created' => 'asc'
	);

	var $belongsTo = array('Administrator', 'Order', 'Status');
	
	var $validate = array(
		'note' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte text poznámky.'
			)
		)	
	);
}
?>