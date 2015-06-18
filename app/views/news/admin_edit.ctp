<h1>Upravit aktualitu</h1>
<?php echo $this->Form->create('News', array('type' => 'file'))?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Titulek</td>
		<td><?php echo $this->Form->input('News.title', array('label' => false, 'size' => 50))?></td>
	</tr>
	<tr>
		<td>Obrázek</td>
		<td><?php echo $this->Form->input('News.image', array('label' => false, 'type' => 'file'))?></td>
	</tr>
	<tr class="nutne">
		<td colspan="2">Text</td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $this->Form->input('News.text', array('label' => false, 'style' => 'width:600px;height:350px;'))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('News.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>