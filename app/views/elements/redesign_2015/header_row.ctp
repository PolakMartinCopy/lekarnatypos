 <div class="header row">
     <div class="logo">
         <a href="/">
             <img src="/img/<?php echo REDESIGN_PATH ?>lekarnatypos.svg" width="216" />
         </a>
         <div class="logo-slogan">Vaše oblíbená lékárna na dosah</div>
     </div>
     <div class="shop-basket-info">
         <div class="user-info">
			<?php echo $this->Html->link('<i class="fa fa-user"></i>', array('controller' => 'customers', 'action' => 'index'), array('escape' => false, 'style' => 'color:#000'))?>
             <div class="hidden-xs user-info-box">
<?php if ($is_logged_in) {
	$customer = $this->Session->read('Customer');
?>
				Jste přihlášen jako <strong><?php echo $customer['first_name']?> <?php echo $customer['last_name']?></strong>.<br/>
				<a href="/customers"><strong>Můj účet</strong></a>  |  <a href="/customers/logout"><strong>Odhlásit se</strong></a>
<?php } else { ?>
                 Nepřihlášený zákazník<br />
                <a href="/registrace"><strong>Registrace</strong></a>  |  <a href="/prihlaseni"><strong>Přihlášení</strong></a>
<?php } ?>
             </div>
         </div>
		<div class="basket-info">
			<?php echo $this->Html->link('<i class="fa fa-shopping-cart"></i>', array('controller' => 'carts_products'), array('escape' => false))?>
			
			<div class="hidden-xs basket-info-box">
			<?php if ($carts_stats['products_count']) { ?>
			    V košíku máte<br />
                <strong><?php echo $carts_stats['products_count']?> ks</strong> zboží za <strong><?php echo number_format($carts_stats['total_price'], 0, ',', ' ')?> Kč</strong><br />
                <?php if ($carts_stats['free_shipping']) { ?>
				<span class="small"><strong>Doprava je zdarma!</strong></span>
				<?php } else { ?>
                <span class="small">(už jen <strong><?php echo number_format($carts_stats['free_shipping_remaining'], 0, ',', ' ')?> Kč</strong> a máte <strong>dopravu zdarma</strong>)</span>
                <a href="/kosik" class="hidden-sm hidden-md hidden-lg btn btn-warning">K objednávce</a>
                <?php } ?>
			<?php } else { ?>
				<br/>Košík je prázdný.
			<?php } ?>
             </div>
         </div>
         <!--<button type="button" class="categories-toggle hidden-md hidden-lg" data-toggle="collapse" data-target="#categories-navigation">
             <i class="fa fa-fw fa-bars"></i>
         </button>-->
     </div>
     <div class="search-box">
     	<?php echo $this->Form->create('Search', array('url' => array('controller' => 'searches', 'action' => 'do_search'), 'encoding' => false, 'type' => 'get', 'id' => 'SearchViewForm', 'class' => 'form-inline floating-labels'));?>
             <div class="input-group">
                 <?php echo $this->Form->input('Search.q', array('label' => 'Hledejte produkty nebo příznaky', 'class' => 'form-control input-lg', 'type' => 'text', 'div' => false));?>
                 <span class="input-group-btn">
                     <button class="btn btn-lg btn-success" type="submit" name="action">
                         <i class="fa fa-search"></i>
                         Hledat
                     </button>
                 </span>
             </div>
             <p class="search-help hidden-xs hidden-sm">např. <a href="/searches/do_search?q=bolest+v+krku">Bolest v krku</a>, <a href="/searches/do_search?q=walmark+spektrum">Walmark Spektrum</a>, <a href="/searches/do_search?q=coldrex">Coldrex</a>, ...</p>
		<?php echo $this->Form->end(); ?>
     </div>
</div>