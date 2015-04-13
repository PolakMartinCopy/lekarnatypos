<h2>Editace produktu</h2>
<div class="product">
<?php echo $form->create('Product', array('url' => array($opened_category_id)));?>
	<fieldset>
 		<legend>Produkt</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Název:</th>
				<td><?php echo $this->Form->input('Product.name', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Nadpis:</th>
				<td><?php echo $form->input('Product.heading', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Breadcrumb:</th>
				<td><?php echo $form->input('Product.breadcrumb', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Název v souvisejících</th>
				<td><?php echo $form->input('Product.related_name', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Název - zbozi.cz</th>
				<td><?php echo $form->input('Product.zbozi_name', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Výrobce:</th>
				<td><?=$form->input('Product.manufacturer_id', array('label' => false))?></td>
			</tr>
			<tr>
				<th>Dostupnost:</th>
				<td><?=$form->input('Product.availability_id', array('label' => false))?></td>
			</tr>
			<tr>
				<th>Poznámka</th>
				<td>
					<?=$form->input('Product.note', array(
						'label' => '',
						'after' => '<br /><span style="font-size:9px">Poznámka se zobrazí při objednávacím formuláři. Použít např. když produkt není skladem.</span>',
						'style' => 'width:600px;height:40px;'
					))?>
				</td>
			</tr>
			<tr>
				<th>Krátký popis:</th>
				<td><?php echo $this->Form->input('Product.short_description', array('label' => false, 'style' => 'width:600px;height:40px;', 'type' => 'textarea'))?></td>
			</tr>
			<tr>
				<th>Popis:</th>
				<td><?php echo $this->Form->input('Product.description', array('label' => false, 'style' => 'width:600px;height:350px;'))?></td>
			</tr>
			<tr>
				<th>Typ produktu (doplněk &times; výživa)</th>
				<td><?php echo $this->Form->input('Product.product_type', array('label' => false, 'type' => 'select', 'options' => $product_types))?></td>
			</tr>
			<tr>
				<th>Daňová skupina:</th>
				<td><?php echo $this->Form->input('Product.tax_class_id', array('label' => false))?></td>
			</tr>
			<tr>
				<th>Cena bez DPH:</th>
				<td><input type="text" name="price_without_tax" id="ProductPriceWithoutTax" onkeyup="return countPrice('with')" /></td>
			</tr>
			<tr>
				<th>Základní cena:</th>
				<td><?php echo $this->Form->input('Product.retail_price_with_dph', array('label' => false))?></td>
			</tr>
			<tr>
				<td colspan="2">Slevové ceny</td>
			</tr>
			<tr>
				<th><abbr title="Běžná sleva z ceny">Běžná sleva</abbr></th>
				<td><?=$form->input('Product.discount_common', array('label' => false)) ?></td>
			</tr>
			<?php foreach ($customer_types as $customer_type) { ?>
			<tr>
				<th><abbr title="Sleva pro zákazníka typu <?php echo $customer_type['CustomerType']['name']?>">Cena pro <?php echo $customer_type['CustomerType']['name']?></abbr></th>
				<td><?php
					echo $this->Form->input('CustomerTypeProductPrice.' . $customer_type['CustomerType']['id'] . '.price', array('label' => false));
					echo $this->Form->hidden('CustomerTypeProductPrice.' . $customer_type['CustomerType']['id'] . '.id');
					echo $this->Form->hidden('CustomerTypeProductPrice.' . $customer_type['CustomerType']['id'] . '.customer_type_id', array('value' => $customer_type['CustomerType']['id']));
				?></td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="2" align="center">
					----------- níže uvedené nevyplňujte -----------
				</td>
			</tr>
			<tr>
				<th>Titulek</th>
				<td><?=$form->input('Product.title', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>URL</th>
				<td><?=$form->input('Product.url', array('label' => false, 'size' => 60))?></td>
			</tr>
		</table>
		<?php echo $form->hidden('Product.id'); ?>
	</fieldset>
	<?php echo $form->end('Uložit změny');?>
	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('Zpět na seznam produktů', true), array('controller'=> 'categories', 'action'=>'list_products', $opened_category_id)); ?> </li>
		</ul>
	</div>
</div>