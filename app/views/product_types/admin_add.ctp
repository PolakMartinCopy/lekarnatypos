<h1>Nový typ produktu</h1>

<?php echo $this->Form->create('ProductType')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název</td>
		<td><?php echo $this->Form->input('ProductType.name', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Text</td>
		<td><?php echo $this->Form->input('ProductType.text', array('label' => false))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('ProductType.active', array('value' => true));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end()
?>