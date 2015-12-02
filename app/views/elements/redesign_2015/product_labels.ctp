<div class="product-labels">
	<?php if ($product['Product']['is_akce']) { ?>
    <span class="product-label action">
        Akce
    </span>
    <?php }
    if ($product['Product']['is_novinka']) { ?>
    <span class="product-label new">
        Novinka
    </span>
    <?php }
    if ($product['Product']['is_doprodej']) { ?>
    <span class="product-label sale">
        Výprodej
    </span>
    <?php }
    if ($product['Product']['is_bestseller']) { ?>
    <span class="product-label bestseller">
        Bestseller
    </span>
    <?php }
    if ($product['Product']['is_darek_zdarma']) { ?>
    <span class="product-label gift">
        Dárek
    </span>
    <?php }
	if (isset($product['Product']['free_shipping_min_quantity']) && !empty($product['Product']['free_shipping_min_quantity'])) {
		$out = '<span class="icon"><i class="fa fa-fw fa-truck"></i></span>';
		if ($product['Product']['free_shipping_min_quantity'] == 1) {
			$out .= ' zdarma';
		} else {
			$out .= ' ' . $product['Product']['free_shipping_min_quantity'] . ' ks';
		}
		?>
		<span class="product-label free-shipping"><?php echo $out?></span>
	<?php 
	}
    ?>
</div>