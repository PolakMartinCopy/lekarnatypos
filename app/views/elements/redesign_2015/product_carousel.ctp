<?php if (isset($products) && !empty($products)) { ?>
<div class="<?php echo $module_class?>">
    <div class="module-headline"><?php echo $title?></div>
    <div id="<?php echo $element_id?>" class="carousel slide" data-ride="carousel" data-interval="false">
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <ul class="row">
                <?php 
                	$i = 1;
                	foreach ($products as $product) { ?>
                    <li class="col-lg-6">
                        <a href="/<?php echo $product['Product']['url']?>" class="img"><img src="/product-images/medium/<?php echo $product['Image']['name']?>" alt="<?php echo $product['Product']['name']?>" /></a>
                        <span class="title"><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></span>
                        <span class="price-box">Naše cena: <span class="price"><?php echo front_end_display_price($product['Product']['price'])?> Kč</span></span>
                        <?php 
                        	echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
                        	echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
                        	echo $this->Form->hidden('Product.quantity', array('value' => 1));
                        	echo $this->Form->button('Přidat do košíku', array('id' => 'AddToCartButton', 'class' => 'btn btn-warning btn-sm'));
                        	echo $this->Form->end();
                        ?>
                         <?php if (isset($product['Product']['discount']) && $product['Product']['discount']) { ?>
                    	<span class="discount">-<?php echo $product['Product']['discount'] ?> %</span>
                    	<?php } ?>
                    </li>
				<?php 	if (($i % 2 == 0) && ($i < count($products))) { ?>
                </ul>
			</div>
			<div class="item">
				<ul class="row">
				<?php 	}
						$i++;
                	} ?>
                </ul>
            </div>
        </div>

        <a class="left carousel-control" href="#<?php echo $element_id?>" role="button" data-slide="prev">
            <i class="fa fa-chevron-left icon-prev" aria-hidden="true"></i>
        </a>
        <a class="right carousel-control" href="#<?php echo $element_id?>" role="button" data-slide="next">
            <i class="fa fa-chevron-right icon-next" aria-hidden="true"></i>
        </a>
    </div>
</div>
<?php } ?>