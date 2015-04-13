<a href="/<?php echo $product_url?>">
    <img src="<?php echo $image?>" alt="<?php echo $product_name?>" />
    <span class="product-title"><?php echo $product_name?></span>
    <span class="product-price"><?php echo number_format($product_price, 0, ',', ' ')?> Kč</span>
<?php if (isset($product_description)) { ?>
	<span class="desc"><?php echo $product_description?></span>
<?php } ?>
</a>