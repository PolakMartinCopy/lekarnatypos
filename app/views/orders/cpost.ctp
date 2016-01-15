<?php
foreach ($orders as $order) {
	$services = array(34);
	if ($order['Order']['payment_id'] == 1) {
		$services[] = 41;
	}
	$services = implode('+', $services);
	$line = array(
		0 => $order['Order']['delivery_name'],
		$order['Order']['delivery_city'],
		$order['Order']['delivery_street'],
		$order['Order']['delivery_zip'],
		$order['Order']['customer_phone'],
		$order['Order']['customer_email'],
		'DR',
		$order['Order']['orderfinaltotal'],
		$services,
		($order['Order']['payment_id'] == 1 ? $order['Order']['orderfinaltotal'] : 0),
	);
	echo implode(';', $line) . "\n";
} ?>