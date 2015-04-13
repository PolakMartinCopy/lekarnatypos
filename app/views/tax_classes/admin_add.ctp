<h2>Vložení daňové třídy</h2>
<?php echo $form->create('TaxClass');?>
<table class="tabulkaedit" cellpadding="5" cellspacing="3">
	<tr class="nutne">
		<td>Název daňové třídy</td>
		<td><?php echo $this->Form->input('TaxClass.name', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Hodnota daně</td>
		<td><?php echo $this->Form->input('TaxClass.value', array('label' => false))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('TaxClass.active', array('value' => true));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end()
?>
<div class="prazdny"></div>