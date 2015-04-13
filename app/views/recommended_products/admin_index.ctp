<h1>Doporučujeme</h1>

<script>
	$(document).ready(function(){
		$('ProductName').select();
		
		$('#ProductName').autocomplete({
			source: '/products/autocomplete_list', 
			select: function(event, ui) {
				var selectedObj = ui.item;
				$.ajax({
					url: '/admin/recommended_products/add',
					type: 'POST',
					data: {
						product_id: selectedObj.value
					},
					dataType: 'json',
					success: function(data) {
						if (data.success) {
							// prekreslim tabulku s produkty
							location.reload();
						} else {
							// vycistim autocomplete pole
							$('#ProductName').val('');
							alert(data.message);
						}

					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert(textStatus);
					}
				});
			}
		});

		//Return a helper with preserved width of cells
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		$("#products > tbody").sortable({
			helper: fixHelper,
			update: function(event, ui) {
				var movedId = ui.item.attr('rel');
				var prevId = ui.item.prev().attr('rel');
				$.ajax({
					url: '/admin/recommended_products/sort',
					type: 'POST',
					data: {
						movedId : movedId,
						prevId : prevId
					},
					dataType: 'json',
					success: function(data) {
						if (!data.success) {
							alert(data.message);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert(textStatus);
					}
				});
			}
		}).disableSelection();
	});
</script>

<div class="ui-widget">
	<?php echo $form->create('Product', array('url' => '#'))?>
	Nový produkt <a href='/administrace/help.php?width=500&id=104' class='jTip' id='104' name='Vložení produktu do seznamu (104)'>
		<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
	</a>
	<?php echo $form->input('Product.name', array('label' => false, 'type' => 'text', 'class' => 'ProductName', 'size' => 70, 'div' => false))?>
	<?php echo $form->end() ?>
</div>
<p><small>Pozn: V systému může být <?php echo $limit?> doporučených produktů.</small></p>

<?php if (empty($recommended)) { ?>
<p><em>Nejsou zvoleny žádné produkty jako doporučené.</em></p>
<?php } else { ?>
<a href='/administrace/help.php?width=500&id=105' class='jTip' id='105' name='Seznam produktů (105)'>
	<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
</a>
<table class="tabulka" cellpadding="5" cellspacing="3" id="products">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>ID</th>
			<th>Název</th>
			<th>Aktivní?</th>
			<th>MO cena s DPH</th>
		</tr>
	</thead>
	<tbody>
<?
	foreach ( $recommended as $product ){
		$style = '';
		if (!$product['Product']['active']) {
			$style = ' style="color:grey"';
		} elseif (!$product['Product']['Availability']['cart_allowed']) {
			$style = ' style="color:orange"';
		}
?>
	<tr <?php echo  $style?> rel="<?php echo $product['RecommendedProduct']['id']?>">
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $html->link($icon, array('controller' => 'recommended_products', 'action' => 'delete', $product['RecommendedProduct']['id']), array('escape' => false, 'title' => 'Odstranit ze seznamu'));
		?></td>
		<td><?=$product['Product']['id']?></td>
		<td><?=$product['Product']['name']?></td>
		<td><?php echo ($product['Product']['active'] ? 'ano' : 'ne') ?></td>
		<td><?=$product['Product']['retail_price_with_dph']?></td>
	</tr>
<?
	}
?>
	</tbody>
</table>
<?php } ?>
<div class="prazdny"></div>