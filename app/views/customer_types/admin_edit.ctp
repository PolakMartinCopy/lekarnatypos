<h1>Upravit cenovou kategorii</h1>

<?php echo $this->Form->create('CustomerType')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název</td>
		<td><?php echo $this->Form->input('CustomerType.name', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Nahrazuje</td>
		<td><?php echo $this->Form->input('CustomerType.substitute_id', array('label' => false, 'options' => $customer_type_substitutions, 'empty' => true))?></td>
	</tr>
</table>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>
<div class="prazdny"></div>