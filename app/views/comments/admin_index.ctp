<h2>Seznam komentářů</h2>
<ul style="font-size:11px;">
	<li>"Notifikace" - Ukazuje, zda byla odpověď odeslána mailem dotazovateli. Kliknutím na `NE` odešlete zákazníkovi mail s odpovědí.</li>
	<li>"Odpověď" - Ukazuje, zda byla ke komentari jiz vlozena odpoved. Kliknutím na `NE` můžete vložit odpověď.</li>
	<li>"Publikován" - Ukazuje, zda je dotaz/komentar viditelny na internetovych strankach. Kliknutím na `NE` zobrazíte komentář na webu.</li>
</ul>
<table class="topHeading">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Vytvořen</th>
		<th>Předmět</th>
		<th>Autor</th>
		<th>Odpověď</th>
		<th>Notifikace</th>
		<th>Publikován</th>
	</tr>
	<? foreach ($comments as $comment) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'comments', 'action' => 'edit', $comment['Comment']['id']), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'comments', 'action' => 'delete', $comment['Comment']['id']), array('escape' => false), 'Opravdu chcete komenář smazat?');
		?></td>
		<td><?=$comment['Comment']['id'] ?></td>
		<td><?=$comment['Comment']['created'] ?></td>
		<td><?=$comment['Comment']['subject'] ?></td>
		<td><?=$comment['Comment']['author'] ?></td>
		<td><?=( empty($comment['Comment']['reply']) ? $this->Html->link('<span style="color:red">NE</span>', array('controller' => 'comments', 'action' => 'edit', $comment['Comment']['id']), array('escape' => false)) : '<span style="color:green">ANO</span>' ) ?>
		<td><?=( $comment['Comment']['sent'] == '0' ? $html->link('<span style="color:red">NE</span>', array('controller' => 'comments', 'action' => 'notify', $comment['Comment']['id']), array('escape' => false), false) : '<span style="color:green">ANO</span>' ) ?>
		<td><?=( $comment['Comment']['confirmed'] == '0' ? $this->Html->link('<span style="color:red">NE</span>', array('controller' => 'comments', 'action' => 'confirm', $comment['Comment']['id']), array('escape' => false)) : '<span style="color:green">ANO</span>' ) ?>
	</tr>
		
	<? } ?>
</table>