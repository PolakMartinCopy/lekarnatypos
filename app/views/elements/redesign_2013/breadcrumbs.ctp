<?php
if (isset($breadcrumbs)) {
	$arr2link = function ($item) {
		return '<a href="' . $item['href'] . '">' . $item['anchor'] . '</a>';
	};
	
	$breadcrumbs = array_map($arr2link, $breadcrumbs);
	$breadcrumbs = implode('</li><li>', $breadcrumbs);
	//debug($breadcrumbs); die();
	$breadcrumbs = '<ul class="breadcrumbs"><li><a href="/">Úvodní stránka</a></li><li>' . $breadcrumbs . '</li></ul>';
	echo $breadcrumbs;
}
?>
<hr class="cleaner" />