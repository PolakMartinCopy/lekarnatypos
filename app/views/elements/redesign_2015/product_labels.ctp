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
    <?php } ?>
</div>