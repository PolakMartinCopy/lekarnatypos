<h1>Seznam emailových šablon</h1>

<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>Předmět</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td colspan="4"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'mail_templates', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat emailovou šablonu'));
		?></td>
	</tr>
<? foreach ($mail_templates as $mail_template) { ?>
	<tr>
		<td><?php echo $mail_template['MailTemplate']['id'] ?></td>
		<td><?php echo $this->Html->link($mail_template['MailTemplate']['subject'], array('controller' => 'mail_templates', 'action' => 'edit', $mail_template['MailTemplate']['id']), array('escape' => false, 'title' => 'Upravit šablonu')); ?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'mail_templates', 'action' => 'edit', $mail_template['MailTemplate']['id']), array('escape' => false, 'title' => 'Upravit šablonu'));
		?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'mail_templates', 'action' => 'del', $mail_template['MailTemplate']['id']), array('escape' => false, 'title' => 'Smazat šablonu'), 'Opravdu chcete tuto šablonu smazat?');
		?></td>
	</tr>
<?php } ?>
</table>