<!DOCTYPE html>

<html lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element('default_head')?>
</head>
<body>
    <div class="container">
		<?php
			echo $this->element('header');
			echo $this->element('nav');
		?>

        <div class="main-content clearfix">
			<?php echo $this->element('left_sidebar')?>
            <div class="col-right nopadding">
            	<?php 
            	if ($this->Session->check('Message.flash')) {
            		echo $this->Session->flash();
            	}
            	?>
<?php
// video chceme schovat
if (false) {?>
                <div class="intro-video">
                    <div class="video-headline">Vaše oblíbená <strong>lékárna na dosah</strong>.</div>
                    <a href="#" class="video-play-btn">Spustit prohlídku &nbsp;&nbsp;<i class="fa fa-play"></i></a>
                    <iframe src="http://player.vimeo.com/video/105547617?title=0&amp;byline=0&amp;portrait=0&amp;color=c9ff23&amp;autoplay=1" width="840" height="440" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </div>
<?php } ?>
                <div class="product-list new-products">
                	<?php 
                		$carousel_id = 'carousel-example-generic';
                		$header = 'Nové produkty';
                		$options = array(
							'carousel_id' => $carousel_id,
							'header' => $header,
							'products' => $newest
						);
                		echo $this->element('products_carousel', $options);
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
                		echo $this->element('products_carousel', $options);
                	?>
                </div>
            </div>
        </div>
    </div>
    <?php
    	echo $this->element('footer');
    	echo $this->element('default_js');
    ?>
</body>
</html>
<?php echo $this->element('sql_dump')?>