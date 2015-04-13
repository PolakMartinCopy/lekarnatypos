<h1>Vložit nový produkt</h1>

<a href='/administrace/help.php?width=500&id=13' class='jTip' id='13' name='Produkty (13)'>
	<img src='/img/help.png' width='16' height='16' />
</a>

<?php echo $form->create('Product', array('url' => array($opened_category_id)));?>
<table class="tabulkaedit">
	<tr class="nutne" valign="top">
		<td>Název:
			<a href='/administrace/help.php?width=500&id=4' class='jTip' id='4' name='Název (4)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr valign="top">
		<td>Nadpis:
			<a href='/administrace/help.php?width=500&id=5' class='jTip' id='5' name='Nadpis (5)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $form->input('Product.heading', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr valign="top">
		<td>Breadcrumb:</td>
		<td><?php echo $form->input('Product.breadcrumb', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr valign="top">
		<td>Název v souvisejících</td>
		<td><?php echo $form->input('Product.related_name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr valign="top">
		<td>Název - zbozi.cz</td>
		<td><?php echo $form->input('Product.zbozi_name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr valign="top">
		<td>Název - heureka.cz</td>
		<td><?php echo $form->input('Product.heureka_name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr valign="top" class="nutne">
		<td>Krátký popis: 
			<a href='/administrace/help.php?width=500&id=8' class='jTip' id='8' name='SEO Description (8)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?=$form->input('Product.short_description', array('label' => false, 'type' => 'texrarea', 'style' => 'width:600px;height:40px;'))?></td>
	</tr>
	<tr valign="top">
		<td>Popis:
			<a href='/administrace/help.php?width=500&id=23' class='jTip' id='23' name='Popis (23)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?=$form->input('Product.description', array('label' => false, 'style' => 'width:600px;height:350px;'))?></td>
	</tr>
	<tr valign="top">
		<td>Aktivní:
			<a href='/administrace/help.php?width=500&id=3' class='jTip' id='3' name='Aktivní (3)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.active', array('label' => false))?></td>
	</tr>
	<tr valign="top">
		<td>Výrobce:
			<a href='/administrace/help.php?width=500&id=21' class='jTip' id='21' name='Výrobce (21)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.manufacturer_id', array('label' => false, 'empty' => true))?></td>
	</tr>
	<tr valign="top">
		<td>Dostupnost:</td>
		<td><?=$form->input('Product.availability_id', array('label' => false))?></td>
	</tr>
	<tr valign="top">
		<td>Poznámka:</td>
		<td>
			<?=$form->input('Product.note', array(
				'label' => false,
				'after' => '<br /><span style="font-size:9px">Poznámka se zobrazí při objednávacím formuláři. Použít např. když produkt není skladem.</span>',
				'style' => 'width:600px;height:40px;'
			))?>
		</td>
	</tr>
	<tr valign="top">
		<td>Typ produktu (doplněk &times; výživa)</td>
		<td><?php echo $this->Form->input('Product.product_type_id', array('label' => false, 'empty' => true))?></td>
	</tr>
	<tr>
		<td>Pohoda ID</td>
		<td><?php echo $this->Form->input('Product.pohoda_id', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<td>EAN</td>
		<td><?php echo $this->Form->input('Product.ean', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Kód</td>
		<td><?php echo $this->Form->input('Product.code', array('label' => false))?></td>
	</tr>
	<tr>
		<td>SUKL</td>
		<td><?php echo $this->Form->input('Product.sukl', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Skupina</td>
		<td><?php echo $this->Form->input('Product.group', array('label' => false))?></td>
	</tr>
	<tr class="nutne" valign="top">
		<td>Základní cena:</td>
		<td><?php echo $this->Form->input('Product.retail_price_with_dph', array('label' => false, 'after' => '&nbspKč'))?></td>
	</tr>
	<tr valign="top">
		<td colspan="2"><h2>Slevové ceny</h2></td>
	</tr>
	<?php foreach ($customer_types as $customer_type) { ?>
	<tr valign="top">
		<td><abbr title="Sleva pro zákazníka typu <?php echo $customer_type['CustomerType']['name']?>">Cena pro <?php echo $customer_type['CustomerType']['name']?></abbr></td>
		<td><?php
			echo $this->Form->input('CustomerTypeProductPrice.' . $customer_type['CustomerType']['id'] . '.price', array('label' => false, 'after' => '&nbspKč'));
			echo $this->Form->hidden('CustomerTypeProductPrice.' . $customer_type['CustomerType']['id'] . '.customer_type_id', array('value' => $customer_type['CustomerType']['id']));
		?></td>
	</tr>
	<?php } ?>
	<tr valign="top" class='nutne'>
		<td>Daňová skupina
			<a href='/administrace/help.php?width=500&id=24' class='jTip' id='24' name='DPH (24)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?=$form->input('Product.tax_class_id', array('label' => false))?></td>
	</tr>
	<tr valign="top">
		<td>Recyklační poplatky:
			<a href='/administrace/help.php?width=500&id=25' class='jTip' id='25' name='Recyklační poplatky (25)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.recycle_fees', array('label' => false, 'after' => '&nbsp;Kč'))?></td>
	</tr>
	<tr valign="top">
		<td>Sleva:
			<a href='/administrace/help.php?width=500&id=26' class='jTip' id='26' name='Sleva (26)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.discount', array('label' => false, 'after' => '&nbsp;%'))?></td>
	</tr>
	<tr valign="top">
		<td>Záruka:
			<a href='/administrace/help.php?width=500&id=27' class='jTip' id='27' name='Záruka (27)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.guarantee', array('label' => false, 'after' => '&nbsp;měsíců (999 = doživotní)'))?></td>
	</tr>
	<tr valign="top">
		<td>Priorita:
			<a href='/administrace/help.php?width=500&id=28' class='jTip' id='28' name='Priorita (28)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.priority', array('label' => false, 'after' => '&nbsp;(0 = nejvyšší, 999 = nejnižší)'))?></td>
	</tr>
	<tr valign="top">
		<td>Váha:</td>
		<td><?php echo $this->Form->input('Product.weight', array('label' => false, 'after' => '&nbsp;kg'))?></td>
	</tr>
	<tr valign="top">
		<td>Video:
			<a href='/administrace/help.php?width=500&id=29' class='jTip' id='29' name='Video (29)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.video', array('label' => false, 'cols' => 100, 'rows' => 4))?></td>
	</tr>
	<tr valign="top">
		<td>Atributy:</td>
		<td>
			<?php echo  $this->Form->input('Product.is_top_produkt', array('label' => false, 'div' => false))?>
			TOP PRODUKT
			<a href='/administrace/help.php?width=500&id=30' class='jTip' id='30' name='TOP PRODUKT (30)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
			<br />
			<?php echo $this->Form->input('Product.is_akce', array('label' => false, 'div' => false))?>
			AKCE
			<a href='/administrace/help.php?width=500&id=31' class='jTip' id='31' name='AKCE (31)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
			<br />
			<?php echo $this->Form->input('Product.is_doprava_zdarma', array('label' => false, 'div' => false))?>
			DOPRAVA ZDARMA
			<a href='/administrace/help.php?width=500&id=32' class='jTip' id='32' name='DOPRAVA ZDARMA (32)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
			<br />
			<?php echo $this->Form->input('Product.is_novinka', array('label' => false, 'div' => false))?>
			NOVINKA
			<a href='/administrace/help.php?width=500&id=33' class='jTip' id='33' name='NOVINKA (33)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
			<br />
			<?php echo $this->Form->input('Product.is_sleva', array('label' => false, 'div' => false))?>
			SLEVA
			<a href='/administrace/help.php?width=500&id=34' class='jTip' id='34' name='SLEVA (34)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
			<br />
			<?php echo $this->Form->input('Product.is_doprodej', array('label' => false, 'div' => false))?>
			DOPRODEJ
			<a href='/administrace/help.php?width=500&id=35' class='jTip' id='35' name='DOPRODEJ (35)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
			<br />
			<?php echo $this->Form->input('Product.is_montaz', array('label' => false, 'div' => false))?>
			SLEVA NA MONTÁŽ + OSOBNÍ ODBĚR
			<a href='/administrace/help.php?width=500&id=36' class='jTip' id='36' name='SLEVA NA MONTÁŽ + OSOBNÍ ODBĚR (36)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
			<br />
			<?php echo $this->Form->input('Product.is_firmy_cz', array('label' => false, 'div' => false))?>
			ZOBRAZENÍ NA FIRMY.CZ
			<br />
			<?php echo $this->Form->input('Product.is_slide_akce', array('label' => false, 'div' => false))?>
			SLIDE AKCE
			<br />
		</td>
	</tr>
	<tr valign="top">
		<td>Generovat do feedů:</td>
		<td><?php echo $this->Form->input('Product.feed', array('label' => false))?></td>
	</tr>
	<tr valign="top">
		<td>SEO Title:
			<a href='/administrace/help.php?width=500&id=7' class='jTip' id='7' name='SEO Title (7)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?=$form->input('Product.title', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr valign="top">
		<td>SEO Keywords:
			<a href='/administrace/help.php?width=500&id=9' class='jTip' id='9' name='SEO Keywords (9)'>
				<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
			</a>
		</td>
		<td><?php echo $this->Form->input('Product.keywords', array('label' => false, 'cols' => 70, 'rows' => 2))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('CategoriesProduct.0.category_id', array('value' => $opened_category_id));
	echo $this->Form->submit('VLOŽIT');
	echo $this->Form->end();
?>
<div class='prazdny'></div>