<eform version="1.3">
  <order version="1.2">
    <document number="<?php echo $order['Order']['id']?>" date="<?php echo $order['Order']['date']?>">Objednavka <?php echo $order['Order']['id']?></document>
    <!-- polozky -->
    <?php
    	foreach ($order['OrderedProduct'] as $ordered_product) {
			$ordered_product_name = $ordered_product['product_name'];
			$attribute_info = array();
			if (!empty($ordered_product['OrderedProductsAttribute'])) {
				foreach ($ordered_product['OrderedProductsAttribute'] as $attribute) {
					$attribute_info[] = $attribute['Attribute']['Option']['name'] . ': ' . $attribute['Attribute']['value'];
				}
			}
			$attribute_info = implode(', ', $attribute_info);
			if (!empty($attribute_info)) {
				$attribute_info = ' ' . $attribute_info;
			}
			$ordered_product_name .= $attribute_info;
    ?>
    <orderItem quantity="<?php echo $ordered_product['product_quantity']?>" code="<?php echo $ordered_product['Product']['pohoda_id']?>" payVAT="yes" price="<?php echo $ordered_product['product_price_with_dph']?>" rateVAT="<?php echo ($ordered_product['Product']['tax_class_id'] == 1 ? 'high' : 'low')?>"><?php echo $ordered_product_name?></orderItem>
    <?php } ?>
    <?php if (!empty($order['DiscountCoupon']['id'])) { ?>
    <orderItem quantity="1" code="" payVAT="no" price="-<?php echo $order['DiscountCoupon']['value']?>" rateVAT="none">Slevový kupón</orderItem>
    <?php }?>
	<orderItem quantity="1" code="" payVAT="yes" price="<?php echo $order['Order']['shipping_cost']?>" rateVAT="<?php echo $order['Order']['shipping_tax_class']?>"><?php echo $order['Shipping']['name']?></orderItem>
	<orderItem quantity="1" code="" payVAT="yes" price="0" rateVAT="none"><?php echo $order['Payment']['name']?></orderItem>

	<!-- dodavatel -->
	<supplier>
		  <company><?php echo CUST_NAME?></company>
			<division></division>
			<street><?php echo CUST_STREET?></street>
			<city><?php echo CUST_CITY?></city>
			<psc><?php echo CUST_ZIP?></psc>
			<ico><?php echo CUST_ICO?></ico>
			<dic><?php echo CUST_DIC?></dic>
			<tel><?php echo CUST_PHONE?></tel>
			<email><?php echo CUST_MAIL?></email>
		</supplier> 
		<!-- odberatel -->
		<customer>
		  	<company><?php echo $order['Order']['customer_name']?></company>
			<division></division>
			<name><?php echo full_name($order['Order']['customer_first_name'], $order['Order']['customer_last_name'])?></name>
			<street><?php echo $order['Order']['customer_street']?></street>
			<city><?php echo $order['Order']['customer_city']?></city>
			<psc><?php echo $order['Order']['customer_zip']?></psc>
			<ico><?php echo $order['Order']['customer_ico']?></ico>
			<dic><?php echo $order['Order']['customer_dic']?></dic>
			<tel><?php echo $order['Order']['customer_phone']?></tel>
			<fax></fax>
			<email><?php echo $order['Order']['customer_email']?></email>
			<www></www>
			<remark></remark>
			<consignee>
				<company></company>
				<division></division>
				<name></name>
				<street></street>
				<city></city>
				<psc></psc>
			</consignee>
		</customer>
		<payment payType="delivery" payVAT="yes"></payment>
  </order>
</eform>