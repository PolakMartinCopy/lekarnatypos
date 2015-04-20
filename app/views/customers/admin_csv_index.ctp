<?php
echo iconv('utf-8', 'Windows-1250', 'ID;Jméno;Email;Telefon;Ulice;Město;PSČ') . "\n";
foreach ($customers as $customer) {
	$customer_street = '';
	if (!empty($customer['Address'])) {
		$customer_street = $customer['Address'][0]['street'];
		if (!empty($customer_street) && !empty($customer['Address'][0]['street_no'])) {
			$customer_street .= ' ' . $customer['Address'][0]['street_no'];
		}
	}
	
	$customer_city = (empty($customer['Address'][0]['city']) ? '' : $customer['Address'][0]['city']);
	$customer_zip = (empty($customer['Address'][0]['zip']) ? '' : $customer['Address'][0]['zip']);
	
	$line = array(
		$customer['Customer']['id'],
		$customer['Customer']['name'],
		$customer['Customer']['email'],
		$customer['Customer']['phone'],
		$customer_street,
		$customer_city,
		$customer_zip
	);
	$line = implode(';', $line);
	echo iconv('UTF-8', 'Windows-1250//IGNORE', $line) . "\n";
} ?>