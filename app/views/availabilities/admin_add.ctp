<h2>Nová dostupnosti</h2>
<?=$form->Create('Availability')?>
<table class="tabulkaedit">
	<tr>
		<th>Název</th>
		<td><?php echo $this->Form->input('Availability.name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<th>Barva (RGB)</th>
		<td><?php echo $this->Form->input('Availability.color', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Povolit vložení do košíku?</th>
		<td><?php echo $this->Form->input('Availability.cart_allowed', array('label' => false))?></td>
	</tr>
</table>
<?
	echo $this->Form->hidden('Availability.active', array('value' => true));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end()
?>