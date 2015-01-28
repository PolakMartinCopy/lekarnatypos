<h1>Nový dodavatel</h1>
<ul>
	<li><?php echo $this->Html->link('Zpět na seznam dodavatelů', array('controller' => 'suppliers', 'action' => 'index'))?></li>
</ul>

<fieldset>
	<legend>Dodavatel</legend>
	<?php echo $this->Form->create('Supplier')?>
	<table class="leftHeading">
		<tr>
			<th><abbr title="Název dodavatele">Název</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.name', array('label' => false))?></td>
		</tr>
		<tr>
			<th><abbr title="Internetová adresa XML feedu">URL</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.url', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Kategorie, do které chcete nahrát produkty z feedu">Kategorie</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.category_id', array('label' => false, 'options' => $categories, 'empty' => false))?></td>
		</tr>
		<tr>
			<th><abbr title="Název tagu, ve kterém je v XML feedu cena produktu">Pole s cenou</abbr><sup>*</sup></th>
			<td><?php echo $this->Form->input('Supplier.price_field', array('label' => false, 'size' => 70))?></td>
		</tr>
		<tr>
			<th><abbr title="Procentuální sleva z původní ceny dodavatele">Sleva</abbr></th>
			<td><?php echo $this->Form->input('Supplier.discount', array('label' => false, 'after' => '%'))?></td>
		</tr>
	</table>
	<?php echo $this->Form->hidden('Supplier.active', array('value' => true))?>
	<?php echo $this->Form->submit('Uložit')?>
	<?php echo $this->Form->end()?>
</fieldset>