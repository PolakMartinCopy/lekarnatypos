<?php
class ZasilkovnaBranchesController extends AppController {
	var $name = 'ZasilkovnaBranches';
	
	function load() {
		$xml = simplexml_load_file($this->ZasilkovnaBranch->xmlFile);
		if ($xml != FALSE) {
			foreach ($xml->children() AS $child) {
				$elementName = $child->getName();
				if ($elementName == 'branches') {
					$save = array();
					foreach ($child->children() as $xmlBranch) {
						$country = $xmlBranch->country->__toString();
						// preskakuju NEceske pobocky
						if ($country == 'cz') {
							$branch = array(
								'zasilkovna_id' => $xmlBranch->id->__toString(),
								'name' => $xmlBranch->name->__toString(),
								'name_street' => $xmlBranch->nameStreet->__toString(),
								'place' => $xmlBranch->place->__toString(),
								'street' => $xmlBranch->street->__toString(),
								'city' => $xmlBranch->city->__toString(),
								'zip' => $xmlBranch->zip->__toString()
							);
							
							if ($dbBranchId = $this->ZasilkovnaBranch->getIdByField('zasilkovna_id', $branch['zasilkovna_id'])) {
								$branch['id'] = $dbBranchId;
							}
							$save[] = $branch;
						}
					}
					if (!$this->ZasilkovnaBranch->saveAll($save)) {
						debug($save);
						trigger_error('Nepodarilo se ulozit pobocky', E_USER_NOTICE);
					}
				}
			}
		}
		else {
			echo "DB se nepodařilo naimportovat! Chybí vstupní XML formát.";
		}
		die('here');
	}
	
	function ajax_search() {}
}