<? foreach ($products as $product) { ?>
	<SHOPITEM>
		<ITEM_ID><?php echo $product['AllianceProduct']['id']?></ITEM_ID>
		<PRODUCTNAME><![CDATA[<?php echo $product['AllianceProduct']['all_prod_title'] ?>]]></PRODUCTNAME>
		<DESCRIPTION><![CDATA[<?php echo $product['AllianceProduct']['pd_short_description']?>]]></DESCRIPTION>
		<HTML_DESCRIPTION><![CDATA[<?php echo $product['AllianceProduct']['pd_description']?>]]></HTML_DESCRIPTION>
		<IMGURL><![CDATA[<?php echo $product['AllianceProduct']['pd_image']?>]]></IMGURL>
		<PRICE_VAT><![CDATA[<?php echo $product['AllianceProduct']['all_price']?>]]></PRICE_VAT>
		<VAT><![CDATA[<?php echo $product['AllianceProduct']['vat'] ?>]]></VAT>
		<MANUFACTURER><![CDATA[<?php echo $product['AllianceProduct']['manufacturer']?>]]></MANUFACTURER>
		<ITEM_TYPE><![CDATA[new]]></ITEM_TYPE>
		<CATEGORYTEXT><![CDATA[<?php echo $product['AllianceProduct']['nomen'] ?>]]></CATEGORYTEXT>
		<DELIVERY_DATE><![CDATA[0]]></DELIVERY_DATE>
		<EAN><![CDATA[<?php echo $product['AllianceProduct']['ean']?>]]></EAN>
	</SHOPITEM>
<? } ?>