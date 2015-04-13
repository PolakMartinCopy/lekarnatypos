<?
	$ru = str_replace('-', '_', $_SERVER['REQUEST_URI']);
	$ru = str_replace('/', 'nutrishop_', $ru);
	require_once '__linker/uploader.php';
	if ( file_exists($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/__linker/' . $ru) ){
		echo upload_links($ru, base64_encode($_SERVER['REQUEST_URI']));
	} else {
		echo upload_links('nutrishop_left_side_box', base64_encode($_SERVER['REQUEST_URI']));
	}
?>