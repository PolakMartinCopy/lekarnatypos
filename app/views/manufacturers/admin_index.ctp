<h1>Výrobci</h1>
<?php if (!empty($manufacturers)) { ?>
<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th><?php echo $this->Paginator->sort('ID', 'Manufacturer.id');?></th>
		<th><?php echo $this->Paginator->sort('Výrobce', 'Manufacturer.name');?></th>
		<th><?php echo $this->Paginator->sort('Web', 'Manufacturer.web')?></th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'manufacturers', 'action' => 'add'), array('escape' => false));
		?></td>
		<td colspan="3">&nbsp;</td>
	</tr>
	<?php foreach ($manufacturers as $manufacturer) { ?>
	<tr>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'manufacturers', 'action' => 'edit', $manufacturer['Manufacturer']['id']), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'manufacturers', 'action' => 'delete', $manufacturer['Manufacturer']['id']), array('escape' => false), 'Opravdu chcete výrobce odstranit?');
		?></td>
		<td><?php echo $manufacturer['Manufacturer']['id'] ?></td>
		<td><?php echo $this->Html->link($manufacturer['Manufacturer']['name'], array('controller' => 'manufacturers', 'action' => 'edit', $manufacturer['Manufacturer']['id'])) ?></td>
		<td><?php echo ($manufacturer['Manufacturer']['www_address'] ? $this->Html->link($manufacturer['Manufacturer']['www_address'], $manufacturer['Manufacturer']['www_address'], array('target' => 'blank')) : '') ?></td>
	</tr>
<?php } ?>
</table>

<table class='legenda'>
	<tr>
		<th align='left'><strong>LEGENDA:</strong></th>
	</tr>
	<tr>
		<td>
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/add.png' width='16' height='16' /> ... přidat výrobce<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/pencil.png' width='16' height='16' /> ... upravit výrobce<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/delete.png' width='16' height='16' /> ... smazat výrobce<br />
		</td>
	</tr>
</table>
<?php } ?>
<div class='prazdny'></div>