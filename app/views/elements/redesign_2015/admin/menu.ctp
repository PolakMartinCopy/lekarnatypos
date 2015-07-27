<div id="admin_hlavni_menu">
	<ul class="sf-menu">
		<li>
			<?php echo $this->Html->link('Objednávky', array('controller' => 'orders', 'action' => 'index', 'status_id' => 'active'))?>
			<?php if (isset($statuses_menu)) { ?>
			<ul>
				<?php foreach ($statuses_menu as $status) { ?>
				<li><?php echo $this->Html->link($status['Status']['name'], array('controller' => 'orders', 'action' => 'index', 'status_id' => $status['Status']['id']))?></li>
				<?php } ?>
				<li><?php echo $this->Html->link('všechny', array('controller' => 'orders', 'action' => 'index', 'status_id' => 'all'))?></li>
				<li>---</li>
				<li><?php echo $this->Html->link('Kontrola dor. objednávek', array('controller' => 'orders', 'action' => 'track'))?></li>
			</ul>
			<?php } ?>
		</li>
		<li><a href="#">Zboží</a>
			<ul>
				<li><?php echo $this->Html->link('Produkty', array('controller' => 'products', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Produkty na HP', array('controller' => 'most_sold_products', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Kategorie', array('controller' => 'categories', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Dodavatelé', array('controller' => 'suppliers', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Výrobci', array('controller' => 'manufacturers', 'action' => 'index'))?></li>
			</ul>
		</li>
		<li>
			<a href="#">Texty</a>
			<ul>
				<li><?php echo $this->Html->link('Webstránky', array('controller' => 'contents', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Aktuality', array('controller' => 'news', 'action' => 'index'))?></li>
			</ul>
		</li>
		<li><?php echo $this->Html->link('Uživatelé', array('controller' => 'customers', 'action' => 'index'))?></li>
		<li><?php echo $this->Html->link('Diskuze', array('controller' => 'comments', 'action' => 'index'))?></li>
		<li>
			<a href="#">Nastavení</a>
			<ul>
				<li><?php echo $this->Html->link('E-shop', array('controller' => 'settings', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Dopravy', array('controller' => 'shippings', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Platby', array('controller' => 'payments', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('DPH', array('controller' => 'tax_classes', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Cenové kategorie', array('controller' => 'customer_types', 'action' => 'index'))?></li>
<?php if (false) { ?>
				<li><?php echo $this->Html->link('Doporučujeme', array('controller' => 'recommended_products', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Nové produkty', array('controller' => 'discounted_products', 'action' => 'index'))?></li>
<?php } ?>
				<li><?php echo $this->Html->link('Typy produktů', array('controller' => 'product_types', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Dostupnosti produktů', array('controller' => 'availabilities', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Vlastnosti produktů', array('controller' => 'product_properties', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Stavy objednávek', array('controller' => 'statuses', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Emailové šablony', array('controller' => 'mail_templates', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('SMS šablony', array('controller' => 's_m_s_templates', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Třídy atributů', array('controller' => 'options', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('Přesměrování', array('controller' => 'redirects', 'action' => 'index'))?></li>
			</ul>
		</li>
		<li>
			<a href="#">Přehledy</a>
			<ul>
				<li><?php echo $this->Html->link('Statistiky', array('controller' => 'statistics', 'action' => 'index'))?></li>
				<li><?php echo $this->Html->link('E-maily pro novinky', array('controller' => 'customers', 'action' => 'newsletter_emails'))?></li>
			</ul>
		</li>
		<li><?php echo $this->Html->link('Odhlásit', array('controller' => 'administrators', 'action' => 'logout'))?></li>
	</ul>
</div>