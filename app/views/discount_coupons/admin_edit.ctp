<h1>Upravit slevový kupón</h1>
<?php echo $this->Form->create('DiscountCoupon')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td style="width:20%">Hodnota</td>
		<td style="width:80%"><?php echo $this->Form->input('DiscountCoupon.value', array('label' => false, 'size' => 20, 'after' => '&nbsp;Kč'))?></td>
	</tr>
	<tr>
		<td>Platnost</td>
		<td><?php echo $this->Form->input('DiscountCoupon.valid_until', array('label' => false, 'size' => 20, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<td>Min. hodnota objednávky</td>
		<td><?php echo $this->Form->input('DiscountCoupon.min_amount', array('label' => false, 'size' => 20, 'after' => '&nbsp;Kč'))?></td>
	</tr>
<?php
	// pridavani uzivatelu / zakazniku
	if (empty($this->data['DiscountCouponsCustomer'])) {
		$this->data['DiscountCouponsCustomer'][1] = array();
	}
	$count = 1;
	foreach ($this->data['DiscountCouponsCustomer'] as $index => $customer) {
		$class = 'customer-name';
		if ($count == count($this->data['DiscountCouponsCustomer'])) {
			$class = 'last-customer ' . $class;
		}
?>
	<tr class="customer-row" rel-counter="<?php echo $index?>">
		<td>Zákazník</td>
		<td><?php
			echo $this->Form->input('DiscountCouponsCustomer.' . $index . '.customer_name', array('label' => false, 'size' => 60, 'div' => false, 'class' => $class));
			echo $this->Form->hidden('DiscountCouponsCustomer.' . $index . '.customer_id');
			if ($count != 1) {
				echo $this->Html->link('Zrušit', '#', array('class' => 'remove-row'));
			}
		?></td>
	</tr>
<?php
		$count++;	
	}

	// pridavani produktu
	if (empty($this->data['DiscountCouponsProduct'])) {
		$this->data['DiscountCouponsProduct'][1] = array();
	}
	$count = 1;
	foreach ($this->data['DiscountCouponsProduct'] as $index => $product) {
		$class = 'product-name';
		if ($count == count($this->data['DiscountCouponsProduct'])) {
			$class = 'last-product ' . $class;
		}
?>
	<tr class="product-row" rel-counter="<?php echo $index?>">
		<td>Produkt</td>
		<td><?php
			echo $this->Form->input('DiscountCouponsProduct.' . $index . '.product_name', array('label' => false, 'size' => 60, 'div' => false, 'class' => $class));
			echo $this->Form->hidden('DiscountCouponsProduct.' . $index . '.product_id');
			if ($count != 1) {
				echo $this->Html->link('Zrušit', '#', array('class' => 'remove-row'));
			}
		?></td>
	</tr>
<?php 
		$count++;
	}
	
	// pridavani kategorii
	if (empty($this->data['DiscountCouponsCategory'])) {
		$this->data['DiscountCouponsCategory'][1] = array();
	}
	$count = 1;
	foreach ($this->data['DiscountCouponsCategory'] as $index => $category) {
		$class = 'category-name';
		if ($count == count($this->data['DiscountCouponsCategory'])) {
			$class = 'last-category ' . $class;
		}
?>
	<tr class="category-row" rel-counter="<?php echo $index?>">
		<td>Kategorie</td>
		<td><?php
			echo $this->Form->input('DiscountCouponsCategory.' . $index . '.category_name', array('label' => false, 'size' => 60, 'div' => false, 'class' => $class));
			echo $this->Form->hidden('DiscountCouponsCategory.' . $index . '.category_id');
			if ($count != 1) {
				echo $this->Html->link('Zrušit', '#', array('class' => 'remove-row'));
			}
		?></td>
	</tr>
<?php 
		$count++;
	}
?>
</table>
<br/>
<?php 
	echo $this->Form->hidden('DiscountCoupon.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>
<div class="prazdny"></div>

<script>
	$(function() {
		var productCounter = 1;
		var categoryCounter = 1;
		var dates = $('#DiscountCouponValidUntil').datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = "minDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});

		$('table').delegate('.customer-name', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			var row = $(this).closest('tr');
			customerCounter = $(row).attr('rel-counter');
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/customers/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#DiscountCouponsCustomer' + customerCounter + 'CustomerId').val(ui.item.value);
					$('#DiscountCouponsCustomer' + customerCounter + 'CustomerName').val(ui.item.label);
					return false;
				}
			});
		});

		// sprava poli pro produkt
		$(document).delegate('.last-product', 'focus', function(e) {
			// zjistit cislo posledniho radku
			var row = $(this).closest('tr');
			productCounter = $(row).attr('rel-counter');
			productCounter++;
			// odstranim tridu last
			$(this).removeClass('last-product');
			// pridat novy s vyssim cislem za posledni radek tridy customer-row
			var productRow = '<tr class="product-row" rel-counter="' + productCounter + '">';
			productRow += '<td>Produkt</td>';
			productRow += '<td>';
			productRow += '<input name="data[DiscountCouponsProduct][' + productCounter + '][product_name]" type="text" size="60" id="DiscountCouponsProduct' + productCounter + 'ProductName" class="last-product product-name"/>';
			productRow += '<input type="hidden" name="data[DiscountCouponsProduct][' + productCounter + '][product_id]" id="DiscountCouponsProduct' + productCounter + 'ProductId">';
			productRow += '<a href="#" class="remove-row">Zrušit</a>';
			productRow += '</td>';
			productRow += '</tr>';
			$('.product-row').last().after(productRow);
		});

		$(document).delegate('.remove-row', 'click', function(e) {
			// pred smazanim se musim podivat, jestli neni last a pokud jo, oznacit novy last - item dane tridy s nejvyssim counterem
			var row = $(this).closest('tr');
			var input = $(row).find('.product-name');
			var isLast = false;
			if (input.hasClass('last-product')) {
				isLast = true;
			}
			$(this).closest('tr').remove();
			if (isLast) {
				var last = null;
				var maxCounter = 0;
				$('.product-row').each(function(index, value) {
					var theCounter = $(value).attr('rel-counter')
					if (theCounter > maxCounter) {
						maxCounter = theCounter;
						last = value;
					}
				});
				$(last).find('.product-name').addClass('last-product');
			}
		});

		$('table').delegate('.product-name', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			var row = $(this).closest('tr');
			productCounter = $(row).attr('rel-counter');
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/products/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#DiscountCouponsProduct' + productCounter + 'ProductId').val(ui.item.value);
					$('#DiscountCouponsProduct' + productCounter + 'ProductName').val(ui.item.label);
					return false;
				}
			});
		});

		// sprava poli pro kategorie
		$(document).delegate('.last-category', 'focus', function(e) {
			// zjistit cislo posledniho radku
			var row = $(this).closest('tr');
			categoryCounter = $(row).attr('rel-counter');
			categoryCounter++;
			// odstranim tridu last
			$(this).removeClass('last-category');
			// pridat novy s vyssim cislem za posledni radek tridy customer-row
			var productRow = '<tr class="category-row" rel-counter="' + categoryCounter + '">';
			productRow += '<td>Kategorie</td>';
			productRow += '<td>';
			productRow += '<input name="data[DiscountCouponsCategory][' + categoryCounter + '][category_name]" type="text" size="60" id="DiscountCouponsCategory' + categoryCounter + 'CategoryName" class="last-category category-name"/>';
			productRow += '<input type="hidden" name="data[DiscountCouponsCategory][' + categoryCounter + '][product_id]" id="DiscountCouponsCategory' + categoryCounter + 'CategoryId">';
			productRow += '<a href="#" class="remove-row">Zrušit</a>';
			productRow += '</td>';
			productRow += '</tr>';
			$('.category-row').last().after(productRow);
		});

		$(document).delegate('.remove-row', 'click', function(e) {
			// pred smazanim se musim podivat, jestli neni last a pokud jo, oznacit novy last - item dane tridy s nejvyssim counterem
			var row = $(this).closest('tr');
			var input = $(row).find('.category-name');
			var isLast = false;
			if (input.hasClass('last-category')) {
				isLast = true;
			}
			$(this).closest('tr').remove();
			if (isLast) {
				var last = null;
				var maxCounter = 0;
				$('.category-row').each(function(index, value) {
					var theCounter = $(value).attr('rel-counter')
					if (theCounter > maxCounter) {
						maxCounter = theCounter;
						last = value;
					}
				});
				$(last).find('.category-name').addClass('last-category');
			}
		});

		$('table').delegate('.category-name', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			var row = $(this).closest('tr');
			categoryCounter = $(row).attr('rel-counter');
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/categories/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#DiscountCouponsCategory' + categoryCounter + 'CategoryId').val(ui.item.value);
					$('#DiscountCouponsCategory' + categoryCounter + 'CategoryName').val(ui.item.label);
					return false;
				}
			});
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>