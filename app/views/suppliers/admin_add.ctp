<h1>Nový dodavatel</h1>
<ul>
	<li><?php echo $this->Html->link('Zpět na seznam dodavatelů', array('controller' => 'suppliers', 'action' => 'index'))?></li>
</ul>

<fieldset>
	<legend>Dodavatel</legend>
	<?php echo $this->Form->create('Supplier')?>
	<table class="tabulkaedit">
		<tr class="nutne">
			<th><abbr title="Název dodavatele">Název</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.name', array('label' => false))?></td>
		</tr>
		<tr class="nutne">
			<th><abbr title="Internetová adresa XML feedu">URL</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.url', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr class="nutne">
			<th><abbr title="Typ feedu (heureka, google)">Typ feedu</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.feed_type', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém jsou v XML data katalogu (pokud neni zaroven korenovym tagem)">Pole s katalogem</abbr></th>
			<td><?php echo $this->Form->input('Supplier.catalog_root_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr class="nutne">
			<th><abbr title="Název tagu, ve kterém jsou v XML data o produktu">Pole s produktem</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.product_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr class="nutne">
			<th><abbr title="Název tagu, ve kterém je v XML feedu ID produktu dodavatele">Pole s ID</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.id_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr class="nutne">
			<th><abbr title="Název tagu, ve kterém je v XML feedu název produktu">Pole s názvem</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.name_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr class="nutne">
			<th><abbr title="Název tagu, ve kterém je v XML feedu krátký popis produktu">Pole s krátkým popisem</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.short_description_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu popis produktu">Pole s popisem</abbr></th>
			<td><?php echo $this->Form->input('Supplier.description_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu nadpis produktu">Pole s nadpisem</abbr></th>
			<td><?php echo $this->Form->input('Supplier.heading_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu breadcrumb produktu">Pole s breadcrumb</abbr></th>
			<td><?php echo $this->Form->input('Supplier.breadcrumb_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu název produktu v souvisejících">Pole s názvem v souvisejících</abbr></th>
			<td><?php echo $this->Form->input('Supplier.related_name_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu názvem produktu pro heureku">Pole s názvem pro heureku</abbr></th>
			<td><?php echo $this->Form->input('Supplier.heureka_name_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu názvem produktu pro zbozi">Pole s názvem pro zbozi</abbr></th>
			<td><?php echo $this->Form->input('Supplier.zbozi_name_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu title produktu">Pole s title</abbr></th>
			<td><?php echo $this->Form->input('Supplier.title_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr class="nutne">
			<th><abbr title="Název tagu, ve kterém je v XML feedu cena produktu">Pole s cenou</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.price_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu nákupní cena produktu">Pole s nákupní cenou</abbr><br/>bez DPH</th>
			<td><?php echo $this->Form->input('Supplier.wholesale_price_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu obrázek produktu">Pole s obrázkem</abbr></th>
			<td><?php echo $this->Form->input('Supplier.image_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu daňová třída produktu">Pole s daňovou třídou</abbr></th>
			<td><?php echo $this->Form->input('Supplier.vat_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu zlevněná cena produktu">Pole se zlevněnou cenou</abbr></th>
			<td><?php echo $this->Form->input('Supplier.discount_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu EAN produktu">Pole s EAN</abbr></th>
			<td><?php echo $this->Form->input('Supplier.ean_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu kategorie produktu">Pole s kategorií</abbr></th>
			<td><?php echo $this->Form->input('Supplier.category_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu dostupnost produktu">Pole s dostupností</abbr></th>
			<td><?php echo $this->Form->input('Supplier.availability_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu výrobce produktu">Pole s výrobcem</abbr></th>
			<td><?php echo $this->Form->input('Supplier.manufacturer_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Procentuální sleva z původní ceny dodavatele">Sleva</abbr></th>
			<td><?php echo $this->Form->input('Supplier.discount', array('label' => false, 'after' => '%'))?></td>
		</tr>
		<tr>
			<th><abbr title="Procentuální navýšení původní ceny dodavatele">Navýšení ceny</abbr></th>
			<td><?php echo $this->Form->input('Supplier.price_increase', array('label' => false, 'after' => '%'))?></td>
		</tr>
	</table>
	<?php echo $this->Form->hidden('Supplier.active', array('value' => true))?>
	<?php echo $this->Form->submit('Uložit')?>
	<?php echo $this->Form->end()?>
</fieldset>