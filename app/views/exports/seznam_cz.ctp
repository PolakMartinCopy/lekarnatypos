<? foreach ( $products as $product ){ ?>
	<SHOPITEM>
<?php 
$zbozi_name = $product['Product']['zbozi_name'];
if (empty($zbozi_name)) {
	$zbozi_name = $product['Product']['name'];
}
?>
		<PRODUCTNAME><![CDATA[<?=$zbozi_name?>]]></PRODUCTNAME>
		<DESCRIPTION><![CDATA[<?=$product['Product']['short_description']?>]]></DESCRIPTION>
		<URL><![CDATA[http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?>]]></URL>
		<PRICE_VAT><?=$product['Product']['price']?></PRICE_VAT>
<?php // vychozi dostupnost produktu je ihned
	$availability = 0;
	switch($product['Availability']['id']) {
		case 1: $availability = 0; break;
		case 2: $availability = 7; break;
		case 3: $availability = 14; break;
		case 6: $availability = 21; break;
		case 7: $availability = 30; break;
		case 9: $availability = 2; break;
		case 10: $availability = 3; break;
	}
?>
		<DELIVERY_DATE><?php echo $availability?></DELIVERY_DATE>
		<ITEM_ID><?php echo $product['Product']['id']?></ITEM_ID>
<?php if (file_exists('product-images/' . $product['Image']['name'])) { ?>
		<IMGURL>http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']))?></IMGURL>
<?php } ?>
<?php if (isset($product['Product']['ean']) && !empty($product['Product']['ean'])) {
	$ean = trim($product['Product']['ean']);
	if (strlen($ean) >= 12 && strlen($ean) <= 14) {
?>
		<EAN><![CDATA[<?php echo $ean?>]]></EAN>
<?php 
	}
} ?>
		<MANUFACTURER><![CDATA[<?php echo $product['Manufacturer']['name']?>]]></MANUFACTURER>
<?php if (isset($product['ComparatorProductClickPrice']['click_price']) && !empty($product['ComparatorProductClickPrice']['click_price'])) { ?>
		<MAX_CPC><?php echo number_format($product['ComparatorProductClickPrice']['click_price'], 2, '.', '')?></MAX_CPC>
<?php } ?>
	</SHOPITEM>
<? } ?>