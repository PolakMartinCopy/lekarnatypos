<?php 
class PDKManufacturersController extends AppController {
	var $name = 'PDKManufacturers';
	
	function import() {
		$manufacturers = 'http://' . $_SERVER['HTTP_HOST'] .  '/' . PDK_DIAL_DIR . '/' . $this->PDKManufacturer->file_name;
		
		$manufacturers = download_url($manufacturers);
		$manufacturers = iconv('cp852', 'UTF-8', $manufacturers);
		
		$manufacturers = $this->PDKManufacturer->dial_2_array($manufacturers, 0, 3);
		
		foreach ($manufacturers as $shortcut => $name) {
			if (!$this->PDKManufacturer->hasAny(array('shortcut' => $shortcut))) {
				$save[] = array(
					'shortcut' => $shortcut,
					'name' => $name
				);
			}
		}

		$this->PDKManufacturer->saveAll($save);
		
		die();
	}
}
?>