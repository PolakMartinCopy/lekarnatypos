<?php
$res_line = array(
	'customer_id', 'visit_created', 'visit_duration', 'category1_name', 'category2_name', 'category3_name', 'product_name', 'product_description_show', 'product_comments_show', 'product_cart_inserted', 'product_ordered', 'category_actions'
);

$output = implode(';', $res_line) . "\n";

foreach ($visits as $visit) {
	$visit_line = array();
	foreach ($res_line as $res_column) {
		$visit_line[] = $visit['Visit'][$res_column];
	}
	$output .= implode(';', $visit_line) . "\n";
}
echo iconv('utf-8', 'windows-1250', $output);
?>