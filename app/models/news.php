<?php 
class News extends AppModel {
	var $name = 'News';
	
	var $actsAs = array(
		'Containable',
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => false
		)
	);
	
	var $validate = array(
		'title' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte titulek aktuality'
			)
		),
		'heading' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte nadpis aktuality'
			)
		),
		'text' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte text aktuality'
			)
		)
	);
	
	var $virtualFields = array(
		'first_sentence' => 'SUBSTRING_INDEX(News.text, \'.\', 1)',
		'first_paragraph' => 'SUBSTRING_INDEX(News.text, \'</p>\', 1)',
		'czech_date' => 'DATE_FORMAT(News.created, \'%e. %c. %Y\')'
	);
	
	var $image_path = 'images/news';
	
	/** Vykona se po kazdem vyhledavani **/
	function afterFind($results) {
		foreach ($results as &$result) {
			// z atributu first_sentence chci odstranit html tagy
			if (isset($result['News']['first_sentence'])) {
				$result['News']['first_sentence'] = strip_tags($result['News']['first_sentence']);
			}
		}
		return $results;
	}
	
	/**
	 * Pro vypis aktualit na hlavni strance vrati 3 posledni zaznamy
	 * @return News
	 */
	function hp_list() {
		$news = $this->find('all', array(
			'contain' => array(),
			'order' => array('News.order' => 'desc'),
			'limit' => 2
		));
		
		$default_image = '/img/' . REDESIGN_PATH . 'article.png';
		foreach ($news as &$actuality) {
			if (empty($actuality['News']['image'])) {
				$actuality['News']['image'] = $default_image;
			} else {
				$image_path = $this->image_path . '/' . $actuality['News']['image'];
				if (file_exists($image_path)) {
					$actuality['News']['image'] = '/' . $image_path;
				} else {
					$actuality['News']['image'] = $default_image;
				}
			}
		}

		return $news;
	}
	
	function loadImage($image_data) {
		// pokud neni zadan obrazek, nahraje se bez nej
		if (empty($image_data['name']) && empty($image_data['tmp_name'])) {
			return '';
		}
	
		$file_name = $this->image_path . DS . $image_data['name'];
		$file_name_arr = explode('.', $file_name);
		$file_name_ext = $file_name_arr[count($file_name_arr)-1];
		unset($file_name_arr[count($file_name_arr)-1]);
		$file_name_prefix = implode('.' , $file_name_arr);
		$counter = '';
		$file_name = $file_name_prefix . $counter . '.' . $file_name_ext;
		$i = 1;
		while (file_exists($file_name)) {
			$counter = '_' . $i;
			$file_name = $file_name_prefix . $counter . '.' . $file_name_ext;
			$i++;
		}
	
		$tmp_name = $image_data['tmp_name'];
		$width = 78;
		$height = 62;
	
		// zmenim velikost obrazku
		App::import('Model', 'Image');
		$this->Image = new Image;
	
		if ($this->Image->resize($tmp_name, $file_name, $width, $height)) {
			$file_name = str_replace($this->image_path . DS, '', $file_name);
			return $file_name;
		}
		return false;
	}
}
?>
