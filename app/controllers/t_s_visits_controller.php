<?php
class TSVisitsController extends AppController {
	var $name = 'TSVisits';
	
	function recount_duration() {
		$this->TSVisit->recountDuration();
		die();
	}
	
	function close_expired() {
		if (!$this->TSVisit->closeExpired()) {
			debug('chyba pri uzavirani expirovanych navstev');
		}
		// TODO - spravovat chyby
		die();
	}
}