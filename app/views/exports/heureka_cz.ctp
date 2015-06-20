<? 
foreach ($products as $product) { 
	$name = $product['Product']['heureka_name'];
	if (empty($name)) {
		$name = $product['Product']['name'];
	}
	
	$extended_name = $product['Product']['heureka_extended_name'];
	if (empty($extended_name)) {
		$extended_name = $name;
	}
?>
	<SHOPITEM>
		<ITEM_ID><?php echo $product['Product']['id']?></ITEM_ID>
		<PRODUCTNAME><![CDATA[<?php echo $name ?>]]></PRODUCTNAME>
		<PRODUCT><![CDATA[<?php echo $extended_name ?>]]></PRODUCT>
		<DESCRIPTION><![CDATA[<?php echo $product['Product']['short_description']?>]]></DESCRIPTION>
		<URL><![CDATA[http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?>]]></URL>
		<IMGURL><![CDATA[http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?>]]></IMGURL>
		<PRICE><![CDATA[<?php echo ceil($product['Product']['price'] * 100 / ($product['TaxClass']['value'] + 100)) ?>]]></PRICE>
		<PRICE_VAT><![CDATA[<?php echo $product['Product']['price']?>]]></PRICE_VAT>
		<VAT><![CDATA[<?php echo str_replace('.', ',', $product['TaxClass']['value'] / 100) ?>]]></VAT>
		<MANUFACTURER><![CDATA[<?php echo $product['Manufacturer']['name']?>]]></MANUFACTURER>
		<ITEM_TYPE><![CDATA[new]]></ITEM_TYPE>
		<CATEGORYTEXT><![CDATA[<?php echo $product['CATEGORYTEXT'] ?>]]></CATEGORYTEXT>
		<DELIVERY_DATE><![CDATA[0]]></DELIVERY_DATE>
<?php if (isset($product['Product']['ean']) && !empty($product['Product']['ean'])) { ?>
		<EAN><![CDATA[<?php echo $product['Product']['ean']?>]]></EAN>
<?php } ?>
<?php if (isset($product['ComparatorProductClickPrice']['click_price']) && !empty($product['ComparatorProductClickPrice']['click_price'])) { ?>
		<HEUREKA_CPC><?php echo number_format($product['ComparatorProductClickPrice']['click_price'], 2, ',', '')?></HEUREKA_CPC>
<?php } ?>
<?php foreach ($shippings as $shipping) { ?>
		<DELIVERY>
			<DELIVERY_ID><![CDATA[<?php echo $shipping['Shipping']['heureka_id']?>]]></DELIVERY_ID>
			<?php // pokud je cena produktu vyssi, nez cena objednavky, od ktere je tato doprava zdarma, cena je 0, jinak zadam cenu dopravy
			$shipping_price = 0;
			if ($shipping['Shipping']['free'] && $product['Product']['price'] < $shipping['Shipping']['free']) {
				$shipping_price = ceil($shipping[0]['min_price']);
			}
			?>
			<DELIVERY_PRICE><?php echo $shipping_price?></DELIVERY_PRICE>
		</DELIVERY>
<?php } ?>
	</SHOPITEM>
<? } ?>