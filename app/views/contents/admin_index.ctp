<h1>Webstránky</h1>
<a href='/administrace/help.php?width=500&id=16' class='jTip' id='16' name='Webstránky (16)'><img src='/images/<?php echo REDESIGN_PATH ?>icons/help.png' width='16' height='16' /></a>
<?php if (empty($contents)) { ?>
<p><em>V systému nejsou žádné webstránky.</em></p>
<?php }?>
<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Název</th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'contents', 'action' => 'add'), array('escape' => false));
		?></td>
		<td colspan="2">&nbsp;</td>
	</tr>
	<?php foreach ($contents as $content) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'contents', 'action' => 'edit', $content['Content']['id']), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'contents', 'action' => 'delete', $content['Content']['id']), array('escape' => false), 'Opravdu chcete webstránku smazat?');
		?></td>
		<td><?php echo $this->Html->link($content['Content']['id'], array('controller' => 'contents', 'action' => 'edit', $content['Content']['id']))?></td>
		<td><?php echo $this->Html->link($content['Content']['title'], array('controller' => 'contents', 'action' => 'edit', $content['Content']['id']))?></td>
	</tr>
	<?php } ?>
</table>
<div class='prazdny'></div>
<table class='legenda'>
	<tr>
		<th align='left'><strong>LEGENDA:</strong></th>
	</tr>
	<tr>
		<td>
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/add.png' width='16' height='16' /> ... přidat webstránku<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/pencil.png' width='16' height='16' /> ... upravit webstránku<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/delete.png' width='16' height='16' /> ... smazat webstránku<br />
		</td>
	</tr>
</table>
<div class='prazdny'></div>