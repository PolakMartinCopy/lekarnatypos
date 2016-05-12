<?php
class ZasilkovnaBranchesController extends AppController {
	var $name = 'ZasilkovnaBranches';
	
	function load() {
		if ($xmlContent = download_url($this->ZasilkovnaBranch->xmlFile)) {
			$xml = simplexml_load_string($xmlContent);
			if ($xml != FALSE) {
				foreach ($xml->children() AS $child) {
					$elementName = $child->getName();
					if ($elementName == 'branches') {
						$save = array();
						$activeIds = array();
						foreach ($child->children() as $xmlBranch) {
							$country = $xmlBranch->country->__toString();
							$branch = array();
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
								if ($dbBranchId = $this->ZasilkovnaBranch->getIdByField($branch['zasilkovna_id'], 'zasilkovna_id')) {
									$activeIds[] = $branch['id'] = $dbBranchId;
								}
								$save[] = $branch;
							}
						}
						// smazu ty, ktere jsou navic oproti aktualnimu importu
						$uselessBranchIds = $this->ZasilkovnaBranch->find('all', array(
							'conditions' => array('ZasilkovnaBranch.id NOT IN (' . implode(', ', $activeIds) . ')'),
							'contain' => array(),
							'fields' => array('ZasilkovnaBranch.id')
						));
						$uselessBranchIds = Set::extract('/ZasilkovnaBranch/id', $uselessBranchIds);
						$this->ZasilkovnaBranch->deleteAll(array('ZasilkovnaBranch.id' => $uselessBranchIds));
						// ulozim update
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
		} else {
			echo "DB se nepodařilo naimportovat! Nepodařilo se stáhnout XML z dané URL.";
		}
		die('here');
	}
	
	function ajax_search() {}
}