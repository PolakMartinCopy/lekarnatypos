<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>bootstrap-slider.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>bootstrap.min.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>custom-scripts.js"></script>


 <?php if ($this->params['controller'] == 'orders' && $this->params['action'] == 'one_step_order') { ?>
 <script type="text/javascript">
 $(document).ready(function() {
	// JEDNOKROKOVA OBJEDNAVKA
	PERSONAL_PURCHASE_SHIPPING_ID = parseInt(<?php echo PERSONAL_PURCHASE_SHIPPING_ID?>);

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

	// zobrazit / skryt form pro druhou adresu, pokud jsem zaskrtnul, ze ho chci
	$('#isDifferentAddressCheckbox').change(function() {
		// pokud mam dorucovaci adresu ruznou od fakturacni
		if ($(this).is(':checked')) {
			// zobrazim tabulku pro dorucovaci adresu
			$('#InvoiceAddressTable').show();
		} else {
			// schovam tabulku pro dorucovaci adresu
			$('#InvoiceAddressTable').hide();
		}
	});
	
	// pokud je rovnou vybrana doprava osobnim odberem
	if ($('#OrderShippingId' + PERSONAL_PURCHASE_SHIPPING_ID).is(':checked')) {
		$('#InvoiceAddressBox').hide();
		$('#DeliveryAddressBox').hide();
	}

	// pri zmene dopravy
 	$('input[name="data[Order][shipping_id]"]').change(function(e) {
 		var shippingId = this.value;
 		// zobrazit / skryt elementy pro zadani adres, pokud jsem zaskrtnul, ze chci / nechci doruceni osobnim odberem
		if (shippingId == PERSONAL_PURCHASE_SHIPPING_ID) {
			$('#InvoiceAddressBox').hide();
			$('#DeliveryAddressBox').hide();
		} else {
			$('#InvoiceAddressBox').show();
			$('#DeliveryAddressBox').show();
		}
 	});
});
</script>
<?php } ?>