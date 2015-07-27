<?php 
class GoSMS {
	
	var $logLevel = 0;
	
	function get_access_token() {
		$token = false;
		// nejdriv se podivam, jestli mam token
		if (defined('GOSMS_ACCESS_TOKEN')) {
			$token = GOSMS_ACCESS_TOKEN;
		}
		if (!($token && $this->test_access_token($token))) {
			// a pokud ne, stahnu si novy
			if (!$token = $this->download_access_token()) {
				return false;
			}
			App::import('Model', 'Setting');
			$this->Setting = &new Setting;
				
			if (!$this->Setting->updateValue('GOSMS_ACCESS_TOKEN', $token)) {
				trigger_error('Nepodarilo se ulozit access token do settings', E_USER_ERROR);
			}
		}
		return $token;
	}
	
	function download_access_token() {
		if (!defined('GOSMS_CLIENT_ID') || !defined('GOSMS_CLIENT_SECRET')) {
			$message = 'Není definováno CLIENT_ID nebo CLIENT_SECRET';
			debug($message);
			CakeLog::write('gosms', $message);
			return false;
		} else {
			//set POST variables
			$url = 'https://app.gosms.cz/oauth/v2/token';
			$fields = array(
				'client_id' => urlencode(GOSMS_CLIENT_ID),
				'client_secret' => urlencode(GOSMS_CLIENT_SECRET),
				'grant_type' => 'client_credentials'
			);
	
			//url-ify the data for the POST
			$fields_string = '';
			foreach($fields as $key=>$value) {
				$fields_string .= $key . '=' . $value . '&';
			}
			$fields_string = rtrim($fields_string, '&');
	
	
			//open connection
			$ch = curl_init();
	
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
			//execute post
			$result = curl_exec($ch);
	
			//close connection
			curl_close($ch);
	
			$response = json_decode($result);
			return $response->access_token;
		}
	}
	
	// TODO - dodelat test na platnost tokenu, pokud nechci pro kazde rozesilani tahat novy...
	// mohlo by se udelat tak, ze z casoveho udaje vygenernovani tokenu a delky platnosti vypocist,
	// jestli jsem jeste v rozmezi nebo ne
	function test_access_token($token) {
		return false;
	}
	
	
	function send($phone, $message) {
		if (!$token = $this->get_access_token()) {
			$logMessage = 'Nepodařilo se získat token.';
			debug($logMessage);
			if ($this->logLevel) {
				CakeLog::write('gosms', $logMessage);
			}
			return false;
		}
	
		$url = 'https://app.gosms.cz/api/v1/messages/';
		$headers = array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $token
		);
	
		if (!defined('GOSMS_CHANNEL')) {
			$logMessage = 'Není definovaný kanál. Telefon: ' . $phone . ', zprava: ' . $logMessage;
			debug($logMessage);
			if ($this->logLevel) {
				CakeLog::write('gosms', $logMessage);
			}
			return false;
		}
		$channel = GOSMS_CHANNEL;
	
		$fields = array(
			'message' => $message,
			'recipients' => $phone,
			'channel' => $channel
		);
		$fields = json_encode($fields);
	
		//open connection
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
		//execute post
		$result = curl_exec($ch);

		//odchytit, ze se zprava neposlala
		$result = json_decode($result);
		if (isset($result->status) && $result->status == 400) {
			$logMessage = $result->title;
			if (isset($result->detail)) {
				$logMessage .= ' - ' . $result->detail;
			}
			$logMessage .= ' - Telefon: ' . $phone . ', zprava: ' . $message;
			debug($logMessage);
			if ($this->logLevel) {
				CakeLog::write('gosms', $logMessage);
			}
			return false;
		} elseif (!empty($result->recipients->invalid)) {
			$logMessage = 'Nepodařilo se odeslat zprávu. Telefon: ' . $phone . ', zprava: ' . $message;
			debug($logMessage);
			if ($this->logLevel) {
				CakeLog::write('gosms', $logMessage);
			}
			return false;
		}
	
		//close connection
		curl_close($ch);
	
	}
}
?>