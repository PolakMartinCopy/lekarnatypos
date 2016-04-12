<h1>Oblíbené produkty</h1>

<script>
	$(document).ready(function(){
		$('#ProductName').select();
		
		$('#ProductName').autocomplete({
			source: '/products/autocomplete_list', 
			select: function(event, ui) {
				var selectedObj = ui.item;
				var gender = parseInt($('input[name="data[Product][gender]"]:checked').val());
				$.ajax({
					url: '/admin/most_sold_products/add',
					type: 'POST',
					data: {
						product_id: selectedObj.value,
						gender: gender
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
					url: '/admin/most_sold_products/sort',
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
	<div style="width:10%;float:left;line-height:50px"">
		Nový produkt <a href='/administrace/help.php?width=500&id=104' class='jTip' id='104' name='Vložení produktu do seznamu (104)'>
			<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
		</a>
	</div>
	<div style="width:25%;float:left">

		<?php echo $this->Form->input('Product.gender', array('label' => false, 'type' => 'radio', 'options' => array(0 => 'žena', 1 => 'muž'), 'legend' => 'Pohlaví', 'div' => false, 'value' => $defaultGender));?>
	</div>
	<div style="width:65%;float:left;height:50px;display:flex;align-items:center">
		<?php echo $form->input('Product.name', array('label' => false, 'type' => 'text', 'class' => 'ProductName', 'size' => 70, 'div' => false))?>
	</div>
	<?php echo $form->end() ?>
</div>
<div style="clear:both"></div>
<p><small>Pozn: V systému může být <?php echo $limit?> oblíbených produktů pro každé pohlaví.</small></p>

<?php if (empty($most_sold)) { ?>
<p><em>Nejsou zvoleny žádné produkty jako oblíbené.</em></p>
<?php } else { ?>
<a href='/administrace/help.php?width=500&id=105' class='jTip' id='105' name='Seznam produktů (105)'>
	<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
</a>
<table class="topHeading" cellpadding="5" cellspacing="3" id="products">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>ID</th>
			<th>Obrázek</th>
			<th>Název</th>
			<th>Aktivní?</th>
			<th>MO cena s DPH</th>
			<th>Pohlaví</th>
		</tr>
	</thead>
	<tbody>
<?
	foreach ( $most_sold as $product ){
		$style = '';
		if (!$product['Product']['active']) {
			$style = ' style="color:grey"';
		} elseif (!$product['Product']['Availability']['cart_allowed']) {
			$style = ' style="color:orange"';
		}
?>
	<tr <?php echo  $style?> rel="<?php echo $product['MostSoldProduct']['id']?>">
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $html->link($icon, array('controller' => 'most_sold_products', 'action' => 'delete', $product['MostSoldProduct']['id']), array('escape' => false, 'title' => 'Odstranit ze seznamu'));
		?></td>
		<td><?=$product['Product']['id']?></td>
		<td nowrap>
			<img src="/<?php echo $product['MostSoldProduct']['image']?>" />
<?php // OBRAZKY NEVYBIRAM, PROTO ZAKOMENTUJU
if (false) { ?>
			<div style="float:left;width:150px">
				<img src="/<?php echo $product['MostSoldProduct']['image']?>" />
				<?php if (!$product['MostSoldProduct']['has_image']) {?>
				<br/><em>Obrázek byl získán z miniatury.</em>
				<?php } ?>
			</div>
			<div style="float:left;margin: 10px 0">
			<span>Obrázek musí mít v rozměry 118px &times; 118px</span><br/><br/>
		<?php 
			echo $this->Form->create('MostSoldProduct', array('type' => 'file'));
			echo $this->Form->input('MostSoldProduct.image', array('label' => false, 'type' => 'file', 'div' => false));
			echo $this->Form->hidden('MostSoldProduct.id', array('value' => $product['MostSoldProduct']['id']));
			echo $this->Form->hidden('MostSoldProduct.action', array('value' => 'change_image'));
			echo $this->Form->submit('Změnit', array('div' => false));
			echo $this->Form->end();
		?></div>
<?php } // end of if (false)?>
		</td>
		<td><?=$product['Product']['name']?></td>
		<td><?php echo ($product['Product']['active'] ? 'ano' : 'ne') ?></td>
		<td><?=$product['Product']['retail_price_with_dph']?></td>
		<td><?php 
			echo $this->Form->create('MostSoldProduct');
			echo $this->Form->input('MostSoldProduct.gender', array('label' => false, 'type' => 'radio', 'options' => array(0 => 'žena', 1 => 'muž'), 'legend' => 'Pohlaví', 'div' => false, 'value' => $product['MostSoldProduct']['gender']));
			echo $this->Form->hidden('MostSoldProduct.id', array('value' => $product['MostSoldProduct']['id']));
			echo $this->Form->hidden('MostSoldProduct.action', array('value' => 'change_gender'));
			echo $this->Form->submit('Změnit', array('div' => false));
			echo $this->Form->end();
		?></td>
	</tr>
<?
	}
?>
	</tbody>
</table>
<?php } ?>