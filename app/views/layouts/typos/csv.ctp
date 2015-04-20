<?php
header('Content-Type: text/csv');
if (!isset($file_name)) {
	$file_name = 'file.csv';
}
header('Content-Disposition: attachment; filename="' . $file_name . '"');
echo $content_for_layout;
?>