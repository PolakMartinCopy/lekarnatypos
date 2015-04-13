		<div id="slides" class="slidorion">
			<div class="accordion">
				<div class="header"><div style="padding-top: 10px"><b>BCAA zdarma</b><br/>ke 2 produktům XXLabs</div></div>
				<div class="content"></div>
				<div class="header"><div style="padding-top: 10px"><b>MusclePharm</b><br/>za super ceny</div></div>
				<div class="content"></div>
				<div class="header"><div style="padding-top: 10px"><b>PreCre</b><br/>pro větší růst</div></div>
				<div class="content"></div>
				<div class="header"><div style="padding-top: 10px">Předtréninkové formule<br/><b>Cellucor</b></div></div>
				<div class="content"></div>
			</div>
			<div class="slider">
				<div class="slide"><a href="/xxtreme-nut-v19"><img src="/images/hp-banner/banner-2-produkty-bcaa-zdarma.jpg" alt=""></a></div>
				<div class="slide"><a href="/manufacturer/muscle-pharm-usa:94/"><img src="/images/hp-banner/musclepharm.jpg" alt=""></a></div>
				<div class="slide"><a href="/precre-720g-30-davek-p4202"><img src="/images/hp-banner/PreCre_AmiNo_websnvjpg.jpg" alt=""></a></div>
				<div class="slide"><a href="/manufacturer/cellucor:119/"><img src="/images/hp-banner/cellucor.jpg" alt=""></a></div>
			</div>
		</div>

		<h2><span>Doporučujeme</span></h2>
		
		<?php foreach ($hp_recommended as $suggested_product) {?>
		<div class="product card">
			<h3><a href="/<?php echo $suggested_product['Product']['url']?>"><?php echo $suggested_product['Product']['name']?></a></h3>
			<a class="image_holder" href="/<?php echo $suggested_product['Product']['url']?>">
				<img src="/product-images/small/<?php echo $suggested_product['Image']['name']?>" alt="<?php $suggested_product['Product']['title']?>" />
			</a>
			<div class="rating" data-average="<?php echo $suggested_product['Product']['rate']?>" data-id="<?php echo $suggested_product['Product']['id']?>"></div>
			<p class="comments"><a href="<?php echo $suggested_product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="<?php echo $suggested_product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
			<?php 
				echo $this->Form->create('Product', array('url' => '/' . $suggested_product['Product']['url'], 'encoding' => false));
				echo $this->Form->hidden('Product.id', array('value' => $suggested_product['Product']['id']));
				echo $this->Form->hidden('Product.quantity', array('value' => 1));
				echo $this->Form->submit('Vložit do košíku', array('class' => 'cart_add'));
				echo $this->Form->end();
			?>
			<p class="prices">
				<span class="common">Běžná cena: <?php echo front_end_display_price($suggested_product['Product']['retail_price_with_dph'])?> Kč</span><br />
				<span class="price">Cena: <?php echo front_end_display_price($suggested_product['Product']['price'])?> Kč</span>
			</p>
			<p class="guarantee">
				<a href="/garance-nejnizsi-ceny.htm"><span class="first_line">Garance nejnižší ceny!</span></a><br />
				<span class="second_line">Pro více informací pokračujte <a href="/garance-nejnizsi-ceny.htm">zde</a>.</span>
			</p>
		</div>
		<?php } ?>

		<div class="left">
			<h2><span>Nejprodávanější</span></h2>
			<div id="best_products" class="slidorion">
				<div class="slider">
<?php			foreach ($hp_most_sold as $product) { ?>
					<div class="slide product card big" style="z-index: 2; left: 0px; top: 0px;">
						<h3><a href="<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
						<a class="image_holder" href="/<?php echo $product['Product']['url']?>">
							<img src="/product-images/small/<?php echo $product['Image']['name']?>" alt="<?php echo $product['Product']['title']?>"/>
						</a>
						<div class="g_rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div>
						<p class="comments"><a href="<?php echo $product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="<?php echo $product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
						<?php 
							echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
							echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
							echo $this->Form->hidden('Product.quantity', array('value' => 1));
							echo $this->Form->submit('Vložit do košíku', array('class' => 'cart_add'));
							echo $this->Form->end();
						?>
						<p class="prices">
							<span class="common">Běžná cena: <?php echo front_end_display_price($product['Product']['retail_price_with_dph'])?> Kč</span><br />
							<span class="price">Cena: <?php echo front_end_display_price($product['Product']['price'])?> Kč</span>
						</p>
					</div>
<?php			} ?>
				</div>
				<div class="accordion right best">
<?php			foreach ($hp_most_sold as $product) { ?>
					<div class="header"><?php echo $product['Product']['name']?></div>
					<div class="content" style="display: none;"></div>
<?php 			} ?>
				</div>
			</div>
		</div>

		<div class="right news">
			<h2><span>Aktuality</span></h2>
<?php 		if (empty($hp_news)) { ?>
			<p><em>Nemáme pro Vás žádné aktuality.</em></p>
<?php 		} else {
				foreach ($hp_news as $actuality) { ?>
			<h3><?php echo $this->Html->link($actuality['News']['title'], array('controller' => 'news', 'action' => 'view', $actuality['News']['id']))?></h3>
			<p><?php echo $actuality['News']['first_sentence']?> ...</p>
			<span class="date"><?php echo $actuality['News']['czech_date']?></span>
<?php 			}
			echo $this->Html->link('Všechny aktuality', '/aktuality', array('class' => 'open'));
	 		} ?>
		</div>

		<div class="left">
			<h2><span>Kde nás najdete</span></h2>
			<div id="map" style="width:585px;height:300px"></div>
		</div>
		<div class="right open">
			<h2><span>Otevírací doba prodejny v Olomouci</span></h2>
<?php 
	$weekday = date('N');
?>
			<table>
				<tr<?php echo ( $weekday == 1 ) ? ' class="active_weekday"' : ''?>><th>Pondělí</th><td><?php echo $opening_hours[1]?></td></tr>
				<tr<?php echo ( $weekday == 2 ) ? ' class="active_weekday"' : ''?>><th>Úterý</th><td><?php echo $opening_hours[2]?></td></tr>
				<tr<?php echo ( $weekday == 3 ) ? ' class="active_weekday"' : ''?>><th>Středa</th><td><?php echo $opening_hours[3]?></td></tr>
				<tr<?php echo ( $weekday == 4 ) ? ' class="active_weekday"' : ''?>><th>Čtvrtek</th><td><?php echo $opening_hours[4]?></td></tr>
				<tr<?php echo ( $weekday == 5 ) ? ' class="active_weekday"' : ''?>><th>Pátek</th><td><?php echo $opening_hours[5]?></td></tr>
				<tr<?php echo ( $weekday == 6 ) ? ' class="active_weekday"' : ''?>><th>Sobota</th><td><?php echo $opening_hours[6]?></td></tr>
				<tr<?php echo ( $weekday == 7 ) ? ' class="active_weekday"' : ''?>><th>Neděle</th><td><?php echo $opening_hours[7]?></td></tr>
			</table>
			<p><a href="/firma.htm">Kontakty a další informace o prodejně naleznete <strong>zde</strong></a>.</p>
		</div>
		
		<div style="clear:both;"></div>
		
		<h2><span>Akční zboží</span></h2>
		
		<?php foreach ($hp_discounted as $product) {?>
		<div class="product card">
			<h3><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
			<a class="image_holder" href="/<?php echo $product['Product']['url']?>"><img src="/product-images/small/<?php echo $product['Image']['name']?>" alt="<?php $product['Product']['title']?>" /></a>
			<div class="rating" data-average="<?php echo $product['Product']['rate']?>" data-id="<?php echo $product['Product']['id']?>"></div>
			<p class="comments"><a href="<?php echo $product['Product']['url']?>#comment_list">Přečíst komentáře</a> | <a href="<?php echo $product['Product']['url']?>#tabs-2">Přidat komentář</a></p>
			<?php 
				echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
				echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
				echo $this->Form->hidden('Product.quantity', array('value' => 1));
				echo $this->Form->submit('Vložit do košíku', array('class' => 'cart_add'));
				echo $this->Form->end();
			?>
			<p class="prices">
				<span class="common">Běžná cena: <?php echo front_end_display_price($product['Product']['retail_price_with_dph'])?> Kč</span><br />
				<span class="price">Cena: <?php echo front_end_display_price($product['Product']['price'])?> Kč</span>
			</p>
			<p class="guarantee">
				<a href="/garance-nejnizsi-ceny.htm"><span class="first_line">Garance nejnižší ceny!</span></a><br />
				<span class="second_line">Pro více informací pokračujte <a href="/garance-nejnizsi-ceny.htm">zde</a>.</span>
			</p>
		</div>
		<?php } ?>