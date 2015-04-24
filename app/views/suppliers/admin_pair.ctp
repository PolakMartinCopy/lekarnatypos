<h1>Párování produktů <?php echo $supplier['Supplier']['name']?></h1>
<ul>
	<li><?php echo $this->Html->link('Zpět na seznam dodavatelů', array('controller' => 'suppliers', 'action' => 'index'))?></li>
</ul>
<?php if (empty($this->data['Product'])) { ?>
<p><em>Nejsou žádné produkty ke spárování</em></p>
<?php } else { ?>
<?php echo $this->Form->create('Supplier', array('url' => $this->params['pass']))?>
<table class="topHeading">
	<tr>
		<th style="width:10%">ID</th>
		<th style="width:50%">Název</th>
		<th style="width:40%">Feed alternativa</th>
	</tr>
	<?php
	$style = ' style="background-color:#efefef"';
	$odd = true;
	foreach ($this->data['Product'] as $db_product) { ?>
	<tr rel="<?php echo $db_product['id']?>"<?php echo ($odd ? $style : '') ?>>
		<td><?php echo $db_product['id']?></td>
		<td><?php echo $this->Html->link($db_product['name'], '/' . $db_product['url'])?></td>
		<td><?php
			echo $this->Form->hidden('Product.' . $db_product['id'] . '.id');
			echo $this->Form->hidden('Product.' . $db_product['id'] . '.supplier_product_id');
			echo $this->Form->input('Product.' . $db_product['id'] . '.supplier_product_name', array('label' => false, 'size' => 70, 'class' => 'supplier-product-name'));
			echo $this->Form->hidden('Product.' . $db_product['id'] . '.url');
			echo $this->Form->hidden('Product.' . $db_product['id'] . '.supplier_id');
		?></td>
	<?php $odd = !$odd; 
	} ?>
</table>

<script>
$(function() {
	var availableTags = null;
	$.ajax({
		url: '/suppliers/xml_autocomplete_list/<?php echo $supplier['Supplier']['id']?>',
		async: false,
		dataType: 'json',
		method: 'POST',
		success: function(data) {
			if (data.success) {
				availableTags = data.data;
			} else {
				alert(data.message);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert('xx' + textStatus);
		}
	});
	
	console.log(availableTags);

	if (availableTags) {
		$('.supplier-product-name').autocomplete({
			delay: 500,
			minLength: 2,
			source: availableTags,
			select: function(event, ui) {
				var tableRow = $(this).closest('tr');
				var count = tableRow.attr('rel');
				$(this).val(ui.item.label);
				$('#Product' + count + 'SupplierProductId').val(ui.item.value);
				$('#Product' + count + 'SupplierProductName').val(ui.item.name);
				return false;
			}
		});
	}
});
  </script>
  
  

<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>
<?php } ?>