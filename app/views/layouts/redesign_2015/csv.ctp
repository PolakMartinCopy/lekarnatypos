<?php
	if (!isset($file_name)) {
		$file_name = 'MyVerySpecial';
	}
	
	if (!isset($charset)) {
		$charset = 'utf-8';
	}

	header('Content-type: text/csv; charset=' . $charset);
	header('Content-disposition: attachment;filename=' . $file_name . '.csv');
	echo iconv('UTF-8', $charset, $content_for_layout);
?>