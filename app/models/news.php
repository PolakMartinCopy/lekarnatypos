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
		'text' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte text aktuality'
			)
		)
	);
	
	var $virtualFields = array(
		'first_sentence' => 'SUBSTRING_INDEX(News.text, \'.\', 1)',
		'czech_date' => 'DATE_FORMAT(News.created, \'%e. %c. %Y\')'
	);
	
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
			'fields' => array('News.id', 'News.title', 'News.first_sentence', 'News.czech_date'),
			'order' => array('News.order' => 'desc'),
			'limit' => 3
		));
		
		return $news;
	}
}
?>