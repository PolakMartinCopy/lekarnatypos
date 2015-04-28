<h2>Upravit vlastnost produktu</h2>
<ul>
	<li><?php echo $this->Html->link('Zpět na seznam vlasností produktu', array('controller' => 'product_properties', 'action' => 'index'))?></li>
</ul>
<?php echo $form->Create('ProductProperty')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název</td>
		<td><?php echo $form->input('ProductProperty.name', array('label' => false, 'size' => 80))?></td>
	</tr>
</table>
<br/>
<?
	echo $this->Form->hidden('ProductProperty.id');
	echo $form->submit('Uložit');
	echo $this->Form->end();
?>
<div class="prazdny"></div>