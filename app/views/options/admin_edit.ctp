<h2>Vložit nový název atributu</h2>
<?php echo $form->create('Option');?>
<table class="tabulkaedit">
	<tr class="nutne">
		<th>Název atributu</th>
		<td><?php echo $this->Form->input('Option.name', array('label' => false, 'size' => 80))?></td>
	</tr>
</table>
<?
	echo $this->Form->hidden('Option.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>