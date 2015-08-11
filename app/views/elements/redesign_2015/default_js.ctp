<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>bootstrap-slider.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>bootstrap.min.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>custom-scripts.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	var firstClick = true;
	$(document).click(function() {
		if (firstClick) {
			firstClick = false;
			// ajaxem prepocitam delku navstevy
			$.ajax({
				url: '/t_s_visits/recount_duration',
			});
		}
	});
});
</script>


 <?php if ($this->params['controller'] == 'orders' && $this->params['action'] == 'one_step_order') { ?>
 <script type="text/javascript">
 $(document).ready(function() {
	// JEDNOKROKOVA OBJEDNAVKA
	PERSONAL_PURCHASE_SHIPPING_ID = parseInt(<?php echo PERSONAL_PURCHASE_SHIPPING_ID?>);
	GEIS_POINT_SHIPPING_IDS = JSON.parse(<?php echo json_encode(GEIS_POINT_SHIPPING_IDS)?>);
	// zobrazit form pro prihlaseni, pokud jsem zaskrtnul, ze zakaznik je jiz registrovany
	if ($('#CustomerIsRegistered1').is(':checked')) {
		$('#CustomerOneStepOrderDiv').show();
	}

	// zobrazit / skryt form pro prihlaseni, pokud jsem zaskrtnul, ze zakaznik je jiz registrovany
	$('input.customer-is-registered').change(function() {
		if (this.id == 'CustomerIsRegistered1') {
			$('#CustomerOneStepOrderDiv').show();
		} else {
			$('#CustomerOneStepOrderDiv').hide();
		}
	});

	// pokud je rovnou vybrana doprava osobnim odberem
	if ($('#OrderShippingId' + PERSONAL_PURCHASE_SHIPPING_ID).is(':checked')) {
		$('#InvoiceAddressBox').hide();
		$('#DeliveryAddressBox').hide();
	}

	// pokud je vybrana doprava na geis point
	if (geisPointIsChecked()) {
		// schovam box pro zadani dorucovaci adresy
 		$('#DeliveryAddressBox').hide();
 		// zobrazim box pro zadani fakturacni adresy
 		$('#InvoiceAddressBox').show();
 		// schovam box s checboxem pro rozhodnuti, ze fakturacni adresa je jina nez dorucovaci
 		$('#InvoiceAddressChoiceLabel').hide();
 		$('#InvoiceAddressChoiceLabelAlt').show();
		// zobrazim tabulku pro fakturacni adresu
		$('#InvoiceAddressTable').show();
	}

	// pri zmene dopravy
 	$('input[name="data[Order][shipping_id]"]').change(function(e) {
 		var shippingId = this.value;
 		// zobrazit / skryt elementy pro zadani adres, pokud jsem zaskrtnul, ze chci / nechci doruceni osobnim odberem
		if (shippingId == PERSONAL_PURCHASE_SHIPPING_ID) {
			$('#InvoiceAddressBox').hide();
			$('#DeliveryAddressBox').hide();
		} else if ($.inArray(shippingId, GEIS_POINT_SHIPPING_IDS) != -1) {
			// schovam box pro zadani dorucovaci adresy
 			$('#DeliveryAddressBox').hide();
 			// zobrazim box pro zadani fakturacni adresy
 			$('#InvoiceAddressBox').show();
 			// schovam box s checboxem pro rozhodnuti, ze fakturacni adresa je jina nez dorucovaci
 			$('#InvoiceAddressChoiceLabel').hide();
 			$('#InvoiceAddressChoiceLabelAlt').show();
			// zobrazim tabulku pro fakturacni adresu
			$('#InvoiceAddressTable').show();
		} else {
			$('#InvoiceAddressBox').show();
			// pokud mam zaskrtnuto, ze je fakturacni stejna jako dorucovaci
			if ($('#isDifferentAddressCheckbox').is(':checked')) {
				// zobrazim tabulku pro fakturacni adresu
				$('#InvoiceAddressTable').show();
			} else {
				// schovam tabulku pro fakturacni adresu
				$('#InvoiceAddressTable').hide();
			}
			// zobrazim box s checboxem pro rozhodnuti, ze fakturacni adresa je jina nez dorucovaci
			$('#InvoiceAddressChoiceLabel').show();
			$('#InvoiceAddressChoiceLabelAlt').hide();
			$('#DeliveryAddressBox').show();
		}
 	});


 	function geisPointIsChecked() {
 		var checked = false;
 		$.each(GEIS_POINT_SHIPPING_IDS, function(index, value) {
			checked = checked || $('#OrderShippingId' + value).is(':checked');
 		});
 		return checked;
 	}
});
</script>
<?php } elseif ($this->params['controller'] == 'products' && $this->params['action'] == 'view') { ?>
<script type="text/javascript">
$(document).ready(function() {
	// TRACKOVANI ZAKAZNIKU
	$('a[data-role="showDescription"]').click(function() {
		$.ajax({
			url: '/t_s_visit_products/product_description_shown/<?php echo $product['Product']['id']?>'
		});
	});
	
	$('a[data-role="showComments"]').click(function() {
		$.ajax({
			url: '/t_s_visit_products/product_comments_shown/<?php echo $product['Product']['id']?>'
		});
	});
});
</script>
	
<?php } ?>