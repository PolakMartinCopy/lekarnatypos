<h1>Seznam SMS šablon</h1>

<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td colspan="4"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 's_m_s_templates', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat SMS šablonu'));
		?></td>
	</tr>
<? foreach ($templates as $template) { ?>
	<tr>
		<td><?php echo $this->Html->link($template['SMSTemplate']['id'], array('controller' => 's_m_s_templates', 'action' => 'edit', $template['SMSTemplate']['id']), array('escape' => false, 'title' => 'Upravit šablonu')); ?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 's_m_s_templates', 'action' => 'edit', $template['SMSTemplate']['id']), array('escape' => false, 'title' => 'Upravit šablonu'));
		?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 's_m_s_templates', 'action' => 'delete', $template['SMSTemplate']['id']), array('escape' => false, 'title' => 'Smazat šablonu'), 'Opravdu chcete tuto šablonu smazat?');
		?></td>
	</tr>
<?php } ?>
</table>