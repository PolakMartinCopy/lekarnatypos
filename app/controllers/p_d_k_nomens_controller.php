<?php
class PDKNomensController extends AppController {
	var $name = 'PDKNomens';
	
	function import() {
		$nomens = 'http://' . $_SERVER['HTTP_HOST'] .  '/' . PDK_DIAL_DIR . '/' . $this->PDKNomen->file_name;
	
		$nomens = download_url($nomens);
		$nomens = iconv('cp852', 'UTF-8', $nomens);
	
		$nomens = $this->PDKNomen->dial_2_array($nomens);
		foreach ($nomens as $shortcut => $name) {
			if (!$this->PDKNomen->hasAny(array('shortcut' => $shortcut))) {
				$save[] = array(
					'shortcut' => $shortcut,
					'name' => $name
				);
			}
		}

		$this->PDKNomen->saveAll($save);
	
		die();
	}
}