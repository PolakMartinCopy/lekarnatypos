<h2>Přesun kategorie do jiného uzlu</h2>
<ul>
	<li><?php echo $this->Html->link('zpět na seznam kategorií', array('controller' => 'categories', 'action' => 'index'))?></li>
</ul>
<p>Zvolte do kterého uzlu chcete kategorii <strong><?=$this->data['Category']['name']?></strong> přesunout.</p>
<?
	echo $form->create('Category', array('url' => array('controller' => 'categories', 'action' => 'movenode')));
	echo $this->element(REDESIGN_PATH . 'admin/combobox', array('name' => 'Category.parent_id', 'options' => $categories));
	echo $form->hidden('Category.id');
	echo $form->submit('Přesunout');
	echo $form->end();
?>
<div class='prazdny'></div>