<table id="productsList" cellspacing="0" cellpadding="0">
	<tr>
		<th>název produktu</th>
		<th style="width:50%">krátký popis</th>
		<th>cena</th>
	</tr>
<?
	$i = 0;
	foreach ( $products as $product ){
		$bg = ' style="background-color:#F5F5F5"';
		$i++;
		if ( $i % 2 == 0 ){
			$bg = '';
		}
?>
	<tr<?=$bg?>>
		<th valign="top">
			<h2><a href="/<?=$product['Product']['url']?>"><?=$product['Product']['name']?></a></h2>
		</th>
		<td>
			<p class="productsShortDescription"><?=$product['Product']['short_description']?></p>
		</td>
		<td align="right">
			<?
				if ( $product['Product']['discount_price'] < $product['Product']['retail_price_with_dph'] ){
			?>
					<span class="old_price"><?=$product['Product']['retail_price_with_dph']?> Kč</span><br />
           			<strong><?=$product['Product']['discount_price']?> Kč</strong>
           	<?
				} else {
			?>
					<strong><?=$product['Product']['retail_price_with_dph']?> Kč</strong>
			<?
				}
			?>
		</td>
	</tr>
<?
	}
?>
</table>