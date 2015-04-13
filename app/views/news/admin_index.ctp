<h1>Aktuality</h1>
<?php if (empty($news)) { ?>
<p><em>V systému nejsou žádné aktuality.</em></p>
<?php }?>
<table class="topHeading">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Titulek</th>
		<th>Text</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'news', 'action' => 'add'), array('escape' => false));
		?></td>
		<td colspan="5">&nbsp;</td>
	</tr>
	<?php foreach ($news as $actuality) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'news', 'action' => 'edit', $actuality['News']['id']), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('action' => 'delete', $actuality['News']['id']), array('escape' => false), 'Opravdu chcete aktualitu odstranit?');
		?></td>
		<td><?php echo $actuality['News']['id']?></td>
		<td><?php echo $actuality['News']['title']?></td>
		<td><?php echo $actuality['News']['first_sentence']?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'news', 'action' => 'move_up', $actuality['News']['id']), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'news', 'action' => 'move_down', $actuality['News']['id']), array('escape' => false));
		?></td>
	</tr>
	<?php } ?>
</table>
<div class="prazdny"></div>