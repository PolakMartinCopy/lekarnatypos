<h1>Nové zboží</h1>
<?php echo $this->Form->create('CSProduct')?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $this->Form->input('CSProduct.name', array('label' => false, 'size' => 70))?></td>
	</tr>
	<tr>
		<th>Kód VZP</th>
		<td><?php echo $this->Form->input('CSProduct.vzp_code', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Kód skupiny</th>
		<td><?php echo $this->Form->input('CSProduct.group_code', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jednotka</th>
		<td><?php echo $this->Form->input('CSProduct.unit_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>DPH</th>
		<td><?php echo $this->Form->input('CSProduct.tax_class_id', array('label' => false, 'options' => $tax_classes, 'after' => '%'))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('CSProduct.active', array('value' => true))?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>