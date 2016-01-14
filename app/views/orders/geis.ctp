<ArrayOfPackage>
<?php foreach ($orders as $order) { ?>
	<Package>
		<isCargo>0</isCargo>
		<back>0</back>
		
		<sendName>Lékárna Typos</sendName>
		<sendStreet>Běhounská 15</sendStreet>
		<sendCity>Brno</sendCity>
		<sendZipCode>60200</sendZipCode>
		<sendCountry>CZ</sendCountry>
		<sendContactEmail>info@lekarnatypos.cz</sendContactEmail>
		<sendContactPhone>+420778437811</sendContactPhone>

<?php // pokud chci dorucit na vydejni misto 
if (in_array($order['Order']['shipping_id'], $geis_point_shipping_ids)) {
	if (preg_match('/.*VM-(\d+).*/',  $order['Order']['delivery_name'], $code)) {
		$code = $code[1];
?>
		<dpCode><?php echo $code?></dpCode>
		<notFillRecDP>1</notFillRecDP>
		<recContactName><?php echo $order['Order']['customer_name']?></recContactName>
<?php
	} else { ?>
		<recName><?php echo $order['Order']['delivery_name']?></recName>
		<recStreet><?php echo $order['Order']['delivery_street']?></recStreet>
		<recCity><?php echo $order['Order']['delivery_city']?></recCity>
		<recZipCode><?php echo $order['Order']['delivery_zip']?></recZipCode>
<?php
	}
} else { ?>
		<recName><?php echo $order['Order']['delivery_name']?></recName>
		<recStreet><?php echo $order['Order']['delivery_street']?></recStreet>
		<recCity><?php echo $order['Order']['delivery_city']?></recCity>
		<recZipCode><?php echo $order['Order']['delivery_zip']?></recZipCode>
<?php
	$country_code = 'CZ';
	if ($order['Order']['delivery_state'] == 'Slovensko') {
		$country_code = 'SK';
?>
		<recCountry><?php echo $country_code?></recCountry>
<?php
	}
} ?>
		
		<recContactPhone><?php echo $order['Order']['customer_phone']?></recContactPhone>
		<recContactEmail><?php echo $order['Order']['customer_email']?></recContactEmail>
		
		<services>
<?php // DOBIRKA
	if ($order['Order']['payment_id'] == 1) {
?>
			<COD>
				<codValue><?php echo $order['Order']['orderfinaltotal']?></codValue>
				<codValueCur>CZK</codValueCur>
				<varCode><?php echo $order['Order']['variable_symbol']?></varCode>
			</COD>
<?php } ?>
		</services>
		
		<rows>
			<row>
				<count>1</count>
				<weight></weight>
			</row>
		</rows>
	</Package>
	
<?php } ?>
</ArrayOfPackage>