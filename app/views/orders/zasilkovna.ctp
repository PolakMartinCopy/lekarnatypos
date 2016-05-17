verze 4
Vyhrazeno;Číslo objednávky;Jméno;Příjmení;Firma;E-mail;Mobil;Dobírková částka;Měna;Hodnota zásilky;Hmotnost zásilky;Cílová pobočka;Odesilatel;Obsah 18+;Zpožděný výdej;Dodání poštou Ulice;Dodání poštou Číslo domu;Dodání poštou Obec;Dodání poštou PSČ;Expedovat zboží
<?php
foreach ($orders as $order) {
	$line = array(
		1 => '',
		2 => $order['Order']['id'],
		3 => $order['Order']['customer_first_name'],
		4 => $order['Order']['customer_last_name'],
		5 => $order['Order']['company_name'],
		6 => $order['Order']['customer_email'],
		7 => $order['Order']['customer_phone'],
		8 => $order['Order']['cash_on_delivery'],
		9 => 'CZK',
		10 => $order['Order']['subtotal_with_dph'],
		11 => '',
		12 => $order['Order']['zasilkovna_branch_id'],
		13 => CUST_ROOT,
		14 => '',
		15 => '',
		16 => '',
		17 => '',
		18 => '',
		19 => '',
		20 => ''
	);
	echo implode(';', $line) . "\n";
} ?>