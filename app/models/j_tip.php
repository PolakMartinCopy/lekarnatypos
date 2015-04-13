<?php 
class JTip extends AppModel {
	 var $name = 'JTip';
	 
	 var $validate = array(
	 	'text' => array(
	 		'notEmpty' => array(
	 			'rule' => array('notEmpty'),
	 			'message' => 'Zadejte hlášku.'
	 		)
	 	)
	 );
	 
	 function import() {
	 	if ($this->truncate()) {
	 		$url = 'http://www.sportnutrition.cz/administrace/help.php?width=500&id=';
	 		for($i = 1; $i < 110; $i++) {
	 			$theUrl = $url . $i;
	 			$text = file_get_contents($theUrl);
	 			$text = trim($text);
	 			$empty_tip = '<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">';
	 			if ($text == $empty_tip) {
	 				break;
	 			}
	 			$j_tip = array(
	 				'JTip' => array(
	 					'id' => $i,
	 					'text' => $text
	 				)
	 			);
	 			
	 			if (!$this->save($j_tip)) {
	 				debug($j_tip);
	 				$this->save($j_tip, false);
	 			}
	 		}	 		
	 	}
	}
}
?>