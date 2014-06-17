<?php 
	if ($session->check('Message.flash')){
		echo $session->flash();
	}

	echo $content_for_layout;
?>