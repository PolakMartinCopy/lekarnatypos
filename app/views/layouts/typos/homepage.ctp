<!DOCTYPE html>

<html lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
</head>
<body>
    <div class="container">
		<?php
			echo $this->element(REDESIGN_PATH . 'header');
			echo $this->element(REDESIGN_PATH . 'nav');
		?>

        <div class="main-content clearfix">
			<?php echo $this->element(REDESIGN_PATH . 'left_sidebar')?>
            <div class="col-right nopadding">
            	<?php 
            	if ($this->Session->check('Message.flash')) {
            		echo $this->Session->flash();
            	}
            	?>
                <div class="product-list new-products">
                	<?php 
                		$carousel_id = 'carousel-example-generic';
                		$header = 'Nové produkty';
                		$options = array(
							'carousel_id' => $carousel_id,
							'header' => $header,
							'products' => $newest
						);
                		echo $this->element(REDESIGN_PATH . 'products_carousel', $options);
                	?>
                </div>

                <div class="product-list favorite-products">
                   <?php 
                		$carousel_id = 'carousel-example-generic2';
                		$header = 'Oblíbené produkty';
                		$options = array(
							'carousel_id' => $carousel_id,
							'header' => $header,
							'products' => $favourite
						);
                		echo $this->element(REDESIGN_PATH . 'products_carousel', $options);
                	?>
                </div>
            </div>
        </div>
    </div>
    <?php
    	echo $this->element(REDESIGN_PATH . 'footer');
    	echo $this->element(REDESIGN_PATH . 'default_js');
    ?>
</body>
</html>
<?php echo $this->element('sql_dump')?>