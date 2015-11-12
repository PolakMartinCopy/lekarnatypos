<h1>Upravit aktualitu</h1>
<?php echo $this->Form->create('News', array('type' => 'file'))?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>META Titulek</td>
		<td><?php echo $this->Form->input('News.title', array('label' => false, 'size' => 50))?></td>
	</tr>
	<tr class="nutne">
		<td>Nadpis</td>
		<td><?php echo $this->Form->input('News.heading', array('label' => false, 'size' => 50))?></td>
	</tr>
	<tr>
		<td>Podnadpis</td>
		<td><?php echo $this->Form->input('News.subheading', array('label' => false, 'size' => 150))?></td>
	</tr>
	<tr>
		<td>Perex</td>
		<td><?php echo $this->Form->input('News.perex', array('label' => false, 'cols' => 150, 'rows' => 5))?></td>
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