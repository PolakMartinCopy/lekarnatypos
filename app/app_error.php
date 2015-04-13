<?php
class AppError extends ErrorHandler {
	function __construct($method, $messages) {
		parent::__construct($method, $messages);
	}

	function _outputMessage($template) {
		$this->controller->layout = REDESIGN_PATH . 'content';
		parent::_outputMessage($template);
	}
	
}  
?>