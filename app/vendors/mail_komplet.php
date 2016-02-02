<?php 
class MailKomplet {
	private $userName;
	private $password;
	private $server;
	private $baseURL;
	private $token;
	private $cryptData;
	
	function __construct() {
		$this->setUserName();
		$this->setPassword();
		$this->setServer();
		$this->setBaseUrl();
	}
	
	public function setUserName() {
		$this->userName = 'polak';
	}
	
	public function setPassword() {
		$this->password = 'brko11';
	}
	
	public function setServer() {
		$this->server = '01';
	}
	
	public function setBaseURL() {
		$this->baseURL = 'https://admin.webkomplet.cz/api';
	}
	
	public function setToken($token) {
		$this->token = $token;
	}
	
	public function setCryptData($cryptData) {
		$this->cryptData = $cryptData;
	}
	
	public function getUserName() {
		return $this->userName;
	}
	
	public function getPassword() {
		return $this->password;
	}
	
	public function getServer() {
		return $this->server;
	}
	
	public function getBaseURL() {
		return $this->baseURL;
	}
	
	public function getToken() {
		return $this->token;
	}
	
	public function getCryptData() {
		return $this->cryptData;
	}
	
	function login() {
		$url = $this->getBaseURL() . '/authentication/signIn';
		$data = array('userName' => 'polak', 'password' => 'brko11', 'server' => '01');
		
		// Setup cURL
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"Content-Type: application/json;charset=UTF-8",
				"Accept-Encoding: gzip,deflate,sdch"
			),
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HEADER => TRUE
		));
		
		// Send the request
		$response = curl_exec($ch);
		
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) { 
		
			// vytahnu si cookie z hlavicky
			$this->setToken(self::__parseToken($ch, $response));
			// vytahnu si cryptCode z tela
			$this->setCryptData(self::__parseCryptData($ch, $response));
		}
		//close connection
		curl_close($ch);
	}
	
	function logout() {
		if ($this->getToken()) {
			$url = $this->getBaseURL() . '/' . $this->getCryptData() . '/authentication/signOut';
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_COOKIE, $this->getToken());
			
			$response = curl_exec($ch);
			
			// mel bych vynulovat token a cryptData...
			//debug(curl_getinfo($ch, CURLINFO_HTTP_CODE));
			
//			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
//			$header = substr($response, 0, $header_size);
//			debug($header);
			
			curl_close($ch);
			
			return true;
		}
		return false;
	}
	
	function getBusinessUnits() {
		$ch = curl_init();
		$url = $this->getBaseURL() . '/' . $this->getCryptData() . '/businessUnits/get';
		$curl_log = fopen("curl.txt", 'a+');

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_COOKIE, $this->getToken());
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_STDERR, $curl_log);
		
		$response = curl_exec($ch);

		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
			debug($response);
			// tady je property cryptData, kterou asi nekde potrebuju dal...
		} else {
			debug(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		}
		
		fclose($curl_log);
		curl_close($ch);
		
		return true;
	}
	
	private static function __parseToken($ch, $response) {
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		//$body = substr($response, $header_size);
		
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
		
		$cookies = array();
		foreach($matches[1] as $item) {
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}
			
		if (isset($cookies['Webkomplet'])) {
			return 'Webkomplet=' . $cookies['Webkomplet'];
//			$this->setCryptData('xtjv3opyJDo4W4vfVhnrD4a36efUhEy1');
		}
		return false;
	}
	
	private static function __parseCryptData($ch, $response) {
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$body = substr($response, $header_size);
		
		$body = json_decode($body);
		if (isset($body->BaseCrypt)) {
			return $body->BaseCrypt;
		}
		return false;
	}
}
?>