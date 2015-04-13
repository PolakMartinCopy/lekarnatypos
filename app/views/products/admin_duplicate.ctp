<h1>Duplikace produktu</h1>

<?php 
$back_link = array('controller' => 'products', 'action' => 'index');
if (isset($opened_category_id)) {
	$back_link['category_id'] = $opened_category_id;
}
echo $this->Html->link('ZPĚT NA SEZNAM PRODUKTŮ', $back_link)?>
<br /><br />
<h2><?php echo $product['Product']['name']?></h2>
<?php if (isset($category)) { ?>
<h4><?php echo $category['Category']['name']?></h4>
<?php } ?>

<?php echo $this->element(REDESIGN_PATH . 'admin/product_menu')?>
<p>Opravdu si přejete duplikovat produkt včetně obrázků, cen, ... ? <?php echo $this->Html->link('ANO', array('controller' => 'products', 'action' => 'copy', $product['Product']['id'], (isset($opened_category_id) ? $opened_category_id : null))) ?></p>