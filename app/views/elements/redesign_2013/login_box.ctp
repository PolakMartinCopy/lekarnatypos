<ul class="accordion">
	<li class="login<?php echo ($login_box_tab == 'login') ? ' active' : ''?>"><a href="#login">Přihlášení</a></li>
	<li class="basket<?php echo ($login_box_tab == 'basket') ? ' active' : ''?>"><a href="#basket">Nákupní košík</a></li>
	<li class="info<?php echo ($login_box_tab == 'info') ? ' active' : ''?>"><a href="#info">Vše o nákupu</a></li>
</ul>
<div id="login"<?php echo ($login_box_tab != 'login') ? ' style="display:none"' : ''?>>
	<div id="login_wrapper">
<?php 
	$is_logged_in = false;
	if ($this->Session->check('Customer')) {
		$customer = $this->Session->read('Customer');
		if (isset($customer['id']) && !empty($customer['id']) && !isset($customer['noreg'])) {
			$is_logged_in = true;
		}
	}
	if (!$is_logged_in) {
		echo $this->Form->create('Customer', array('url' => array('controller' => 'customers', 'action' => 'login'), 'id' => 'login_form_top', 'encoding' => false));
		echo $this->Form->input('Customer.login', array('label' => false, 'placeholder' => 'Login', 'div' => false, 'after' => '&nbsp;'));
		echo $this->Form->input('Customer.password', array('label' => false, 'placeholder' => 'Heslo', 'div' => false, 'after' => '&nbsp;', 'type' => 'password'));
		echo '<button name="login">OK</button>';
		echo $this->Form->hidden('Customer.backtrace_url', array('value' => $_SERVER['REQUEST_URI']));
		echo $this->Form->end();
		echo '<p>' . $this->Html->link('Zapomněl(a) jsem heslo', '/obnova-hesla?back=' . urlencode(base64_encode($_SERVER['REQUEST_URI']))) . ' | ' . $this->Html->link('Chci se zaregistrovat', '/registrace') . '</p>';
	} else {
		$customer = $this->Session->read('Customer');
?>
		<p>Jste přihlášen jako <strong><?php echo $customer['first_name']?> <?php echo $customer['last_name']?></strong>.</p>
		<p><a href="/customers">Můj účet</a> | <a href="/customers/logout">Odhlásit</a></p>	
<?php } ?>
	</div>
</div>

<div id="basket"<?php echo ($login_box_tab != 'basket') ? ' style="display:none"' : ''?>>
<?php if ($carts_stats['products_count']) { ?>
<p>Máte <strong><?=$carts_stats['products_count']?></strong> produktů v košíku. Celková cena je <span class="price"><?php echo front_end_display_price($carts_stats['total_price'])?> Kč</span></p>
<p><?php echo $this->Html->link('Zobrazit košík', array('controller' => 'carts_products', 'action' => 'index'), array('class' => 'to_cart'))?> | <?php echo $this->Html->link('Objednat', array('controller' => 'customers', 'action' => 'order_personal_info'), array('class' => 'to_order'))?></p>
<?php } else { ?>
<p>Košík je prázdný.</p>
<?php } ?>
</div>

<div id="info"<?php echo ($login_box_tab != 'info') ? ' style="display:none"' : ''?>>
	<div class="left">
		<ul>
			<li><a href="/jak-nakupovat.htm">Jak nakupovat</a></li>
			<li><a href="/obchodni-podminky.htm">Obchodní podmínky</a></li>
			<!-- 
			<li><a href="/doprava.htm">Způsoby a ceny dopravy</a></li>
			<li><a href="/osobni-odber.htm">Osobní odběr</a></li>
 			-->
		</ul>
	</div>
	<div class="left">
		<ul>
			<li><a href="/firma.htm#provozovatel">Informace o provozovateli</a></li>
			<li><a href="/firma.htm#prodejna">Naše prodejna</a></li>
			<!-- 
			<li><a href="/reklamacni-rad.htm">Reklamační řád</a></li>
			<li><a href="/firma.htm">Jak nás kontaktovat</a></li>
			 -->
		</ul>
	</div>
	<hr class="cleaner" />
</div>