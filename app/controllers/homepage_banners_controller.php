<?php
class HomepageBannersController extends AppController {
	var $name = 'HomepageBanners';
	
	function admin_index() {
		$active = $this->HomepageBanner->findValue('HOMEPAGE_BANNER_ACTIVE');
		$image = $this->HomepageBanner->findValue('HOMEPAGE_BANNER_IMAGE');
		$url = $this->HomepageBanner->findValue('HOMEPAGE_BANNER_URL');
		if (isset($this->data)) {
			$dataSource = $this->HomepageBanner->getDataSource();
			$dataSource->begin($this->HomepageBanner);
			$success = true;
			// pokud jsem zmenil active
			if ($this->data['HomepageBanner']['active'] != $active) {
				// upravim nastaveni v systemu
				$success = $success && $this->HomepageBanner->updateValue('HOMEPAGE_BANNER_ACTIVE', $this->data['HomepageBanner']['active']);
			}
			// pokud jsem zmenil obrazek
			if (!empty($this->data['HomepageBanner']['image']['name'])) {
				if (is_uploaded_file($this->data['HomepageBanner']['image']['tmp_name'])) {
					$required_width = $this->HomepageBanner->width;
					$required_height = $this->HomepageBanner->height;
					$imagesize = getimagesize($this->data['HomepageBanner']['image']['tmp_name']);
					$image_width = $imagesize[0];
					$image_height = $imagesize[1];
					if ($image_width != $required_width || $image_height != $required_height) {
						$this->Session->setFlash('Obrázek se nepodařilo změnit, musí mít rozměry ' . $required_width . ' x ' . $required_height . ' px!', REDESIGN_PATH . 'flash_failure');
						$success = false;
					} else {
						$this->data['HomepageBanner']['image']['name'] = strip_diacritic($this->data['HomepageBanner']['image']['name'], false);
						$this->data['HomepageBanner']['image']['name'] = 'images/hp-banner/' . $this->data['HomepageBanner']['image']['name'];
						App::import('Model', 'Image');
						$this->HomepageBanner->Image = &new Image;
						// zkontroluju, jestli nemam obrazek s danym jmenem a pripadne ocisluju
						$this->data['HomepageBanner']['image']['name'] = $this->HomepageBanner->Image->checkName($this->data['HomepageBanner']['image']['name']);
						// zlopiruju obrazek z tmp do pozadovaneho souboru
						if (move_uploaded_file($this->data['HomepageBanner']['image']['tmp_name'], $this->data['HomepageBanner']['image']['name'])) {
							// potrebuju zmenit prava u obrazku
							chmod($this->data['HomepageBanner']['image']['name'], 0644);
							// upravim udaj db
							if ($this->HomepageBanner->updateValue('HOMEPAGE_BANNER_IMAGE', $this->data['HomepageBanner']['image']['name'])) {
								if ($image && file_exists($image)) {
									unlink($image);
								}
								$this->Session->setFlash('Obrázek byl upraven', REDESIGN_PATH . 'flash_success');
							} else {
								$success = false;
								$this->Session->setFlash('Obrázek se nepodařilo nahrát do systému, opakujte prosím akci!', REDESIGN_PATH . 'flash_failure');
							}
						} else {
							$success = false;
							$this->Session->setFlash('Obrázek se nepodařilo nahrát do systému, opakujte prosím akci!', REDESIGN_PATH . 'flash_failure');
						}
					}
				} else {
					$success = false;
					$this->Session->setFlash('Obrázek se nepodařilo nahrát do systému, opakujte prosím akci!', REDESIGN_PATH . 'flash_failure');
				}
			}
			// pokud jsem zmenil cil banneru
			if ($this->data['HomepageBanner']['url'] != $url) {
				// hodnota URL banneru muze byt prazdna
				unset($this->HomepageBanner->validate['value']['notEmpty']);
				if (!$this->HomepageBanner->updateValue('HOMEPAGE_BANNER_URL', $this->data['HomepageBanner']['url'])) {
					$success = false;
					$this->Session->setFlash('Nepodařilo se upravit URL banneru', REDESIGN_PATH . 'flash_failure');
				}
			}
			if ($success) {
				$this->Session->setFlash('Údaje o banneru byly upraveny', REDESIGN_PATH . 'flash_success');
				$dataSource->commit($this->HomepageBanner);
				$this->redirect(array('controller' => 'homepage_banners', 'action' => 'index'));
			} else {
				$dataSource->rollback($this->HomepageBanner);
			}
		} else {
			$this->data['HomepageBanner']['active'] = $active;
			$this->data['HomepageBanner']['image'] = $image;
			$this->data['HomepageBanner']['url'] = $url;
		}
		
		$this->set(compact('image', 'url'));
		
		$this->layout = REDESIGN_PATH . 'admin';
	}
}