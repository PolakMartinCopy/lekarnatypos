<h2>Upravit způsob dopravy</h2>
<?php echo $form->Create('Shipping')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název</td>
		<td><?php echo $form->input('Shipping.name', array('label' => false, 'size' => 80))?></td>
	</tr>
	<tr>
		<td>Popis</td>
		<td><?php echo $this->Form->input('Shipping.description', array('label' => false, 'rows' => 15, 'cols' => 100))?></td>
	</tr>
	<tr class="nutne">
		<td>Cena za dopravu</td>
		<td><?php echo $form->input('Shipping.price', array('label' => false, 'size' => 80))?></td>
	</tr>
	<tr>
		<td>Zdarma od</td>
		<td><?php echo $form->input('Shipping.free', array('label' => false, 'size' => 80))?></td>
	</tr>
	<tr>
		<td>URL prefix</td>
		<td><?php echo $form->input('Shipping.tracker_prefix', array('label' => false, 'size' => 80))?></td>
	</tr>
	<tr>
		<td>URL postfix</td>
		<td><?php echo $form->input('Shipping.tracker_postfix', array('label' => false, 'size' => 80))?></td>
	</tr>
	<tr>
		<td>DPH</td>
		<td><?php echo $form->input('Shipping.tax_class_id', array('label' => false, 'empty' => true))?></td>
	</tr>
</table>
<br/>
<?
	echo $this->Form->hidden('Shipping.id');
	echo $form->submit('Uložit');
	echo $this->Form->end();
?>
<div class="prazdny"></div>