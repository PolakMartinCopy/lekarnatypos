<?php //debug($products); die();?>
<div class="product-list-headline"><?php echo $header?></div>
<div id="<?php echo $carousel_id?>" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
<?php 	for ($pos = 1; $pos <= count($products); $pos++) {
			$product = $products[$pos - 1];

			if (($pos) % 3 == 1) { ?>
        <div class="item<?php echo ($pos == 1 ? ' active' : '')?>">
            <ul class="clearfix">
<?php 		}
			$image = '/img/na_250_250.jpg';
			if (isset($product['Image'][0]) && !empty($product['Image'][0])) {
				$path = 'product-images/medium/' . $product['Image'][0]['name'];
//				if (file_exists($path) && is_file($path) && getimagesize($path)) {
					$image = '/' . $path;
//				}
			}
?>
                <li>
                	<?php
                		$options = array(
                			'product_url' => $product['Product']['url'],
							'product_name' => $product['Product']['name'],
							'product_price' => $product['Product']['discount_price'],
							'image' => $image
						);
						echo $this->element('product_box', $options);
                	?>
                </li>
<?php 		if ($pos % 3 == 0 || $pos == count($products)) { ?>
            </ul>
        </div>
<?php 		} ?>
<?php 	}?>
	</div>
     <a class="left carousel-control" href="#<?php echo $carousel_id?>" role="button" data-slide="prev">
        <i class="fa fa-arrow-circle-o-left"></i>
    </a>
    <a class="right carousel-control" href="#<?php echo $carousel_id?>" role="button" data-slide="next">
        <i class="fa fa-arrow-circle-o-right"></i>
    </a>
</div>