<?
	header("Content-type: text/csv; charset=windows-1250");
	header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
	echo iconv('utf-8', 'windows-1250', $content_for_layout);
?>