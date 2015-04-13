<?php 
class JTipsController extends AppController {
	var $name = 'JTips';
	
	function admin_view($id = null) {
		$result = array(
			''
		);
		
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
		}
		
 		if (isset($id)) {
			$tip = $this->JTip->find('first', array(
				'conditions' => array('JTip.id' => $id),
				'contain' => array(),
				'fields' => array('JTip.id', 'JTip.text')	
			));
			
			if (!empty($tip)) {
				echo $tip['JTip']['text'];
			} else {
				echo 'Chyba: Pro id ' . $id . ' není v systému žádná nápověda.'; 
			}
		} else {
			echo 'Chyba: Není určeno id nápovědy, kterou chcete zobrazit.';
		}
		die();
	}
	
	function import() {
		$this->JTip->import();
		die('here');
	}
}
?>