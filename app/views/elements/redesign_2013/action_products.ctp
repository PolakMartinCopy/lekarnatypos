<?php if (isset($action_products) && !empty($action_products)) { ?>
<div>
	<h2><span>Akční výrobky:</span></h2>
	<?php foreach ($action_products as $action_product) {
		$image = '/img/na_small.jpg';
		if (isset($action_product['Image']) && !empty($action_product['Image'])) {
			$path = 'product-images/small/' . $action_product['Image']['name'];
			if ($_SERVER['REMOTE_ADDR'] == IMAGE_IP) {
				$path = 'product-images-new/small/' . $action_product['Image']['name'];
			}
			if (file_exists($path) && is_file($path) && getimagesize($path)) {
				$image = '/' . $path;
			}
		}
	?>
	<div class="right_sidebar_product">
		<h3><a href="/<?php echo $action_product['Product']['url']?>"><?php echo $action_product['Product']['name']?></a></h3>
		<div class="image_holder">
			<a href="/<?php echo $action_product['Product']['url']?>">
				<img src="<?php echo $image?>" alt="Obrázek <?php $action_product['Product']['name']?>" width="45px" />
			</a>
		</div>
		<div class="prices_holder">
			běžná cena: <span class="old_price"><?php echo front_end_display_price($action_product['Product']['retail_price_with_dph'])?> Kč</span><br />
			<span class="regular_price">cena: <?php echo front_end_display_price($action_product['Product']['price'])?> Kč</span>
			<?php 
				echo $this->Form->create('Product', array('url' => '/' . $action_product['Product']['url'], 'encoding' => false));
				echo $this->Form->hidden('Product.id', array('value' => $action_product['Product']['id']));
				echo $this->Form->hidden('Product.quantity', array('value' => 1));
				echo $this->Form->submit('Vložit do košíku', array('class' => 'right_sidebar_cart_add'));
				echo $this->Form->end();
			?>
		</div>
		<div style="clear:both"></div>
	</div>
	<?php } ?>
	<div style="clear:both"></div>
</div>
<?php } ?>