<?
	if ( !empty($most_sold) ){
?>
		<div class="leftBoxHeading">
			Nejprodávanější produkty
		</div>
		<div class="leftBox">
			<ol>
			<?
				foreach ( $most_sold as $product ){
					echo '<li>' . $html->link($product['Product']['name'], '/' . $product['Product']['url']) . '</li>';
				}
			?>
			</ol>
		</div>
<?
	}
?>