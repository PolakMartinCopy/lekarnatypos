<h2>Nová vlastnost produktu</h2>
<?php echo $form->Create('ProductProperty')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název</td>
		<td><?php echo $form->input('ProductProperty.name', array('label' => false, 'size' => 80))?></td>
	</tr>
</table>
<br/>
<?
	echo $form->submit('Uložit');
	echo $this->Form->end();
?>
<div class="prazdny"></div>