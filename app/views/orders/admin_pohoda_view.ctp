<dat:dataPack id="3478" ico="42956391" application="e-shop" version="2.0" note="Import Objednavky"
	xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd"
	xmlns:ord="http://www.stormware.cz/schema/version_2/order.xsd"
	xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd"
>
	<dat:dataPackItem id="<?php echo $order['Order']['id']?>" version="2.0">
		<ord:order version="2.0">
			<ord:orderHeader>
				<ord:orderType>receivedOrder</ord:orderType>
				<ord:numberOrder><?php echo $order['Order']['id'] ?></ord:numberOrder>
				<ord:date><?php echo $order['Order']['date'] ?></ord:date>
				<ord:text><?php echo $order['Order']['comments'] ?></ord:text>
				<ord:partnerIdentity>
					<typ:address>
						<typ:company><?php echo $order['Order']['customer_name'] ?></typ:company>
						<typ:name><?php echo $order['Order']['customer_name'] ?></typ:name>
						<typ:city><?php echo $order['Order']['customer_city'] ?></typ:city>
						<typ:street><?php echo $order['Order']['customer_street'] ?></typ:street>
						<typ:zip><?php echo $order['Order']['customer_zip'] ?></typ:zip>
						<typ:ico><?php echo $order['Order']['customer_ico'] ?></typ:ico>
						<typ:dic><?php echo $order['Order']['customer_dic'] ?></typ:dic>
						<typ:phone><?php echo $order['Order']['customer_phone'] ?></typ:phone>
						<typ:email><?php echo $order['Order']['customer_email'] ?></typ:email>
					</typ:address>
					<typ:shipToAddress>
			            <typ:name></typ:name>
			            <typ:city></typ:city>
			            <typ:street></typ:street>
			            <typ:zip></typ:zip>
					</typ:shipToAddress>
				</ord:partnerIdentity>
				<ord:paymentType>
<?php 
	$paymentType = null;
	if ($order['PaymentType']['name'] == 'cash') {
		$paymentType = 'Hotově';
	} elseif ($order['PaymentType']['name'] == 'delivery') {
		$paymentType = 'Dobírka';
	}
?>
					<typ:ids><?php echo $paymentType ?></typ:ids>
				</ord:paymentType>
			</ord:orderHeader>
			<ord:orderDetail>
<?php foreach ($order['OrderedProduct'] as $ordered_product) { ?>
				<ord:orderItem>
					<ord:text><?php echo $ordered_product['Product']['name']?>, <?php echo $ordered_product['Product']['id']?> (<?php echo $ordered_product['Product']['Manufacturer']['name']?>)</ord:text> 
					<ord:quantity><?php echo $ordered_product['product_quantity']?></ord:quantity> 
					<ord:payVAT>true</ord:payVAT> 
					<ord:rateVAT><?php echo ($ordered_product['Product']['tax_class_id'] == 1 ? 'high' : 'low') ?></ord:rateVAT> 
					<ord:homeCurrency>
						<typ:unitPrice><?php echo round($ordered_product['product_price_with_dph'])?></typ:unitPrice> 
					</ord:homeCurrency>
					<ord:stockItem>
						<typ:stockItem>
							<typ:ids><?php echo $ordered_product['Product']['id'] ?></typ:ids> 
						</typ:stockItem>
					</ord:stockItem>
				</ord:orderItem>
<?php } ?>
				<ord:orderItem>
					<ord:text><?php echo $order['Shipping']['name']?></ord:text> 
					<ord:quantity>1</ord:quantity>
					<ord:payVAT>true</ord:payVAT> 
					<ord:rateVAT>none</ord:rateVAT> 
					<ord:homeCurrency>
						<typ:unitPrice><?php echo $order['Order']['shipping_cost']?></typ:unitPrice> 
					</ord:homeCurrency>
					<ord:stockItem>
						<typ:stockItem>
							<typ:ids></typ:ids> 
						</typ:stockItem>
					</ord:stockItem>
				</ord:orderItem>
				<ord:orderItem>
					<ord:text><?php echo $order['Payment']['name']?></ord:text> 
					<ord:quantity>1</ord:quantity> 
					<ord:payVAT>true</ord:payVAT> 
					<ord:rateVAT>none</ord:rateVAT> 
					<ord:homeCurrency>
						<typ:unitPrice>0</typ:unitPrice> 
					</ord:homeCurrency>
					<ord:stockItem>
						<typ:stockItem>
							<typ:ids></typ:ids>
						</typ:stockItem>
					</ord:stockItem>
				</ord:orderItem>
			</ord:orderDetail>
			<ord:orderSummary>
				<ord:roundingDocument>math2one</ord:roundingDocument> 
			</ord:orderSummary>
		</ord:order>
	</dat:dataPackItem>
</dat:dataPack>