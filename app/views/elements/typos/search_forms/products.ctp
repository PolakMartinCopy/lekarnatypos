<script type="text/javascript">
$(function() {
	$('#AdminProductFormCategoryId').change(function() {
		$('#AdminProductFormProductName').val('');
		$('#ProductAdminIndexForm').submit();
	});

	$('#AdminProductFormProductNameButton').click(function(e) {
		e.preventDefault();
		$('#AdminProductFormCategoryId option:selected').removeAttr('selected');
		$('#ProductAdminIndexForm').submit();
	});
});
</script>

<div id="search_form_products">
<?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'index')))?>
<table class="tabulka">
	<tr>
		<th>Vyberte kategorii</th>
		<td><?php echo $this->Form->input('AdminProductForm.Category.id', array('label' => false, 'type' => 'select', 'options' => $categories, 'empty' => true))?></td>
	</tr>
	<tr>
		<th>nebo vyhledejte</th>
		<td><?php
			echo $this->Form->input('AdminProductForm.Product.name', array('label' => false, 'type' => 'text', 'div' => false, 'size' => 50));
			echo $this->Form->submit('Vyhledat', array('div' => false, 'id' => 'AdminProductFormProductNameButton'));
		?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $this->Html->link('reset filtru a řazení', array('reset' => 'products')) ?></td>
	</tr>
</table>
<?php
	echo $form->hidden('AdminProductForm.Product.search_form', array('value' => true));
	echo $form->end();
?>
</div>