<?php 
class ToolsController extends AppController {
	var $name = 'Tools';
	
	// zapise do sesny zvoleny tab v login boxu
	function login_box_tab() {
		if (isset($_POST['tab'])) {
			$this->Session->write('login_box_tab', $_POST['tab']);
		}
		die();
	}
	
	function phpinfo() {
		phpinfo();
		die();
	}
	
	// zapise do sesny zvoleny tab (kategorie / priznaky)
	function categories_bothers_tab() {
		if (isset($_POST['tab'])) {
			$this->Session->write('categories_bothers_tab', $_POST['tab']);
		}
		die();
	}
	
	function gosms_test() {
		App::import('Vendor', 'GoSMS', array('file' => 'gosms.php'));
		$this->GoSMS = &new GoSMS;
		$this->GoSMS->logLevel = 1;
		$this->GoSMS->send('723238866', 'ahoj brko z uctu meavita');
		die();
	}
}
?>
