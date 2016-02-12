		<item>
<?php if ($item_group_id) { ?>
			<g:item_group_id><?php echo $item_group_id?></g:item_group_id>
<?php } ?>
			<g:id><?php echo $id?></g:id>
			<title><![CDATA[<?php echo $title?>]]></title>
			<description><![CDATA[<?php echo $description ?>]]></description>
			<g:google_product_category><![CDATA[<?php echo $google_product_category ?>]]></g:google_product_category>
			<link><![CDATA[<?php echo $link ?>]]></link>
			<g:image_link><![CDATA[<?php echo $image_link ?>]]></g:image_link>
			<g:condition>new</g:condition>
			<g:identifier_exists>false</g:identifier_exists>
			<g:availability>in stock</g:availability>
			<g:price><?php echo $price ?> CZK</g:price>
			<g:brand><![CDATA[<?php echo $brand ?>]]></g:brand>
			<g:product_type><![CDATA[<?php echo $product_type ?>]]></g:product_type>
<?php if (isset($ean) && !empty($ean)) { ?>
			<g:gtin><![CDATA[<?php echo $ean?>]]></g:gtin>
<?php } ?>
		</item>