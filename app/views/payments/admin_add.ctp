<h2>Nový způsob platby</h2>
<?php echo $form->Create('Payment')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název</td>
		<td><?php echo $form->input('Payment.name', array('label' => false, 'size' => 80))?></td>
	</tr>
	<tr>
		<td>Popis</td>
		<td><?php echo $this->Form->input('Payment.description', array('label' => false, 'rows' => 15, 'cols' => 100))?></td>
	</tr>
	<tr>
		<td>Typ</td>
		<td><?php echo $this->Form->input('Payment.payment_type_id', array('label' => false))?></td>
	</tr>
</table>
<br/>
<?
	echo $this->Form->hidden('Payment.active', array('value' => true));
	echo $form->submit('Uložit');
	echo $this->Form->end();
?>
<div class="prazdny"></div>