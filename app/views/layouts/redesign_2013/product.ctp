<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->element(REDESIGN_PATH . 'default_head2')?>
		<?php if (!$product['Product']['active']) { ?>
			<meta name="robots" content="noindex" />
		<?php } ?>
	</head>
<body>

<div id="body">
	<div id="header">
		<a id="logo" href="/"><img src="/images/redesign_2013/logo_snv.png" width="240px" height="125px" alt="SNV - sportovní výživa pro Vás" /></a>
		<?php
			echo $this->element(REDESIGN_PATH . 'login_box');
			echo $this->element(REDESIGN_PATH . 'horizontal_menu');
		?>
	</div>
	<?php
		echo $this->element(REDESIGN_PATH . 'sidebox');
		echo $this->element(REDESIGN_PATH . 'submenu');
		echo $this->element(REDESIGN_PATH . 'search_box');
	?>
	<hr class="cleaner" />

	<div id="sidebar"><?php
		echo $this->element(REDESIGN_PATH . 'categories_menu');
		echo $this->element(REDESIGN_PATH . 'manufacturer_select');
		echo $this->element(REDESIGN_PATH . 'awards');
		echo $this->element(REDESIGN_PATH . 'facebook');
	?></div>

	<div id="main">
		<?php echo $this->element(REDESIGN_PATH . 'breadcrumbs'); ?>
		<?php echo $this->element(REDESIGN_PATH . 'category_banner')?>
		<div class="left">
			<?php 
				if ($session->check('Message.flash')){
					echo $session->flash();
				}
				echo $content_for_layout;
			?>
		</div>
		<div class="right">
			<?php 
				if (isset($similar_products) && !empty($similar_products)) { ?>
				<div>
				<h2><span>Lidé co koupili tento <br />produkt koupili také:</span></h2>
			<?php
				foreach ($similar_products as $similar_product) {
					$image = '/img/na_small.jpg';
					if (isset($similar_product['Image']) && !empty($similar_product['Image'])) {
						$path = 'product-images/small/' . $similar_product['Image']['name'];
						if ($_SERVER['REMOTE_ADDR'] == IMAGE_IP) {
							$path = 'product-images-new/small/' . $similar_product['Image']['name'];
						}
						if (file_exists($path) && is_file($path) && getimagesize($path)) {
							$image = '/' . $path;
						}
					} ?>
				<div class="right_sidebar_product">
					<h3><a href="/<?php echo $similar_product['Product']['url']?>"><?php echo $similar_product['Product']['name']?></a></h3>
					<div class="image_holder">
						<a href="/<?php echo $similar_product['Product']['url']?>">
							<img src="<?php echo $image?>" alt="Obrázek <?php echo $similar_product['Product']['name']?>" width="45px"/>
						</a>
					</div>
					<div class="prices_holder">
						běžná cena: <span class="old_price"><?php echo $similar_product['Product']['retail_price_with_dph']?> Kč</span><br />
						<span class="regular_price">cena: <?php echo $similar_product[0]['price']?> Kč</span>
						<?php 
							echo $this->Form->create('Product', array('url' => '/' . $similar_product['Product']['url'], 'encoding' => false));
							echo $this->Form->hidden('Product.id', array('value' => $similar_product['Product']['id']));
							echo $this->Form->hidden('Product.quantity', array('value' => 1));
							echo $this->Form->submit('Vložit do košíku', array('class' => 'right_sidebar_cart_add'));
							echo $this->Form->end();
						?>
					</div>
				</div>
			<?php	} ?>
				<div style="clear:both"></div>
				</div>
			<?php } ?>
			
			<?php
				if (isset($right_sidebar_products) && !empty($right_sidebar_products)) { ?>
				<div>
				<h2><span>Podobné produkty</span></h2>
			<?php	foreach ($right_sidebar_products as $right_sidebar_product) {
				$image = '/img/na_small.jpg';
				if (isset($right_sidebar_product['Image']) && !empty($right_sidebar_product['Image'])) {
					$path = 'product-images/small/' . $right_sidebar_product['Image']['name'];
					if ($_SERVER['REMOTE_ADDR'] == IMAGE_IP) {
						$path = 'product-images-new/small/' . $right_sidebar_product['Image']['name'];
					}
					if (file_exists($path) && is_file($path) && getimagesize($path)) {
						$image = '/' . $path;
					}
				}
			?>
				<div class="right_sidebar_product">
					<h3><a href="/<?php echo $right_sidebar_product['Product']['url']?>"><?php echo $right_sidebar_product['Product']['name']?></a></h3>
					<div class="image_holder">
						<a href="/<?php echo $right_sidebar_product['Product']['url']?>">
							<img src="<?php echo $image?>" alt="Obrázek <?php echo $right_sidebar_product['Product']['name']?>" width="45px" />
						</a>
					</div>
					<div class="prices_holder">
						běžná cena: <span class="old_price"><?php echo $right_sidebar_product['Product']['retail_price_with_dph']?> Kč</span><br />
						<span class="regular_price">cena: <?php echo $right_sidebar_product[0]['price']?> Kč</span>
						<?php 
							echo $this->Form->create('Product', array('url' => '/' . $right_sidebar_product['Product']['url'], 'encoding' => false));
							echo $this->Form->hidden('Product.id', array('value' => $right_sidebar_product['Product']['id']));
							echo $this->Form->hidden('Product.quantity', array('value' => 1));
							echo $this->Form->submit('Vložit do košíku', array('class' => 'right_sidebar_cart_add'));
							echo $this->Form->end();
						?>
					</div>
				</div>
			<?php	} ?>
				<div style="clear:both"></div>
				</div>
			<?php } ?>
		</div>
		<hr class="cleaner" />
	</div>
	<hr class="cleaner" />
	<?php echo $this->element(REDESIGN_PATH . 'footer')?>
</div>
<?php
	echo $this->element(REDESIGN_PATH . 'heureka_overeno');
	echo $this->element(REDESIGN_PATH . 'facebook_prava');
	echo $this->element(REDESIGN_PATH . 'default_foot2');
?>
</body>
</html>
<?php echo $this->element('sql_dump')?>