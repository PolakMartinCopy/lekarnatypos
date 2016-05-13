<script src="/js/<?php echo REDESIGN_PATH ?>bootstrap-slider.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>bootstrap.min.js"></script>
<script src="/js/<?php echo REDESIGN_PATH ?>custom-scripts.js"></script>

<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>
  function initialize() {
	  drawMap('map', 16);
	  drawMap('mapSmall', 15);
  }

  function drawMap(elementId, zoom) {
	    var mapCanvas = document.getElementById(elementId);
	    var mapOptions = {
	      center: new google.maps.LatLng(49.197295, 16.608908),
	      zoom: zoom,
	      mapTypeId: google.maps.MapTypeId.ROADMAP,
	      mapTypeControl: false
	    }
	    var map = new google.maps.Map(mapCanvas, mapOptions)
	    var marker = new google.maps.Marker({
	        position: new google.maps.LatLng(49.197295, 16.608908),
	        map: map,
	    });
	    return true;
  }
  
  google.maps.event.addDomListener(window, 'load', initialize);
</script>

<script type="text/javascript" src="/plugins/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
<script type="text/javascript" src="/plugins/fancybox/source/jquery.fancybox.pack.js"></script>

 <?php if ($this->params['controller'] == 'orders' && $this->params['action'] == 'one_step_order') { ?>
 <script type="text/javascript">
 $(document).ready(function() {
	// JEDNOKROKOVA OBJEDNAVKA
	PERSONAL_PURCHASE_SHIPPING_ID = parseInt(<?php echo PERSONAL_PURCHASE_SHIPPING_ID?>);
	GEIS_POINT_SHIPPING_IDS = JSON.parse(<?php echo json_encode(GEIS_POINT_SHIPPING_IDS)?>);
	ZASILKOVNA_SHIPPING_IDS = JSON.parse(<?php echo json_encode(ZASILKOVNA_SHIPPING_IDS)?>);
	var prevShippingId = null;
	var shippingId = null;
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

	if (zasilkovnaIsChecked()) {
		// schovam box pro zadani dorucovaci adresy
		$('#DeliveryAddressBox').hide();
		// zobrazim box pro zadani fakturacni adresy
		$('#InvoiceAddressBox').show();
		// schovam box s checboxem pro rozhodnuti, ze fakturacni adresa je jina nez dorucovaci
		$('#InvoiceAddressChoiceLabel').hide();
		$('#InvoiceAddressChoiceLabelAlt').show();
		// zobrazim tabulku pro fakturacni adresu
		$('#InvoiceAddressTable').show();
		// dovyplnim link pro vyber pobocky
		name = $('#Address0Name').val().split('||', 1);
		var branchZasilkovnaAddress = name + ', ' + $('#Address0Street').val() + ', ' + $('#Address0City').val() + ' ' + $('#Address0Zip').val();
		$('.zasilkovna-choice-link').text(branchZasilkovnaAddress);
		shippingId = $('input[name="data[Order][shipping_id]"]:checked').val();
	}

	// pri zmene dopravy
 	$('input[name="data[Order][shipping_id]"]').focus(function() {
		prevShippingId = shippingId;
 	}).change(function(e) {
 	 	shippingId = this.value;
 		// pokud jsem mel vybranou zasilkovnu a ted jsem to zmenil na neco jineho, musim vycistit pole pro dorucovaci adresu
 		if ($.inArray(prevShippingId, ZASILKOVNA_SHIPPING_IDS) != -1 && $.inArray(shippingId, ZASILKOVNA_SHIPPING_IDS) == -1) {
 			$('#Address0Name').val('');
 			$('#Address0Street').val('');
 			$('#Address0City').val('');
 			$('#Address0Zip').val('');
 			// link pro vyber pobocky nastavim na default
 			$('.zasilkovna-choice-link').text('vyberte pobočku');
 		}		 		
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
		} else if ($.inArray(shippingId, ZASILKOVNA_SHIPPING_IDS) != -1) {
			// schovam box pro zadani dorucovaci adresy
 			$('#DeliveryAddressBox').hide();
 			// zobrazim box pro zadani fakturacni adresy
 			$('#InvoiceAddressBox').show();
 			// schovam box s checboxem pro rozhodnuti, ze fakturacni adresa je jina nez dorucovaci
 			$('#InvoiceAddressChoiceLabel').hide();
 			$('#InvoiceAddressChoiceLabelAlt').show();
			// zobrazim tabulku pro fakturacni adresu
			$('#InvoiceAddressTable').show();
			// okno pro vyber pobocky zobrazim jen v pripade, ze predchozi typ dopravy nebyl zasilkovna
			if ($.inArray(prevShippingId, ZASILKOVNA_SHIPPING_IDS) == -1) {
				zasilkovnaChoice();
			}
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

 	// pokud chci vybrat pobocku zasilkovny
	$('.zasilkovna-choice-link').focus(function() {
		prevShippingId = shippingId;
	}).click(function(e) {
		e.preventDefault();
		// skryt pole pro zadani dorucovaci adresy
		$('#DeliveryAddressBox').hide();
		// zobrazit pole pro zadani fakturacni adresy
		$('#InvoiceAddressBox').show();
		// schovam box s checboxem pro rozhodnuti, ze fakturacni adresa je jina nez dorucovaci
		$('#InvoiceAddressChoiceLabel').hide();
		$('#InvoiceAddressChoiceLabelAlt').show();
		// zobrazim tabulku pro fakturacni adresu
		$('#InvoiceAddressTable').show();
		// zapamatuju si typ vyberu pobocek
		var shippingId = $(this).attr('data-shipping-id');
		// vyberu dane radio		
		$('#OrderShippingId' + shippingId).prop('checked', true);
		// okno pro vyber pobocky zobrazim jen v pripade, ze predchozi typ dopravy nebyl zasilkovna
		zasilkovnaChoice();
	});


 	function geisPointIsChecked() {
 		var checked = false;
 		$.each(GEIS_POINT_SHIPPING_IDS, function(index, value) {
			checked = checked || $('#OrderShippingId' + value).is(':checked');
 		});
 		return checked;
 	}

 	function zasilkovnaIsChecked() {
 	 	var checked = false;
 	 	$.each(ZASILKOVNA_SHIPPING_IDS, function(index, value) {
 	 		checked = checked || $('#OrderShippingId' + value).is(':checked');
 	 	});
 	 	return checked;
 	}

	function zasilkovnaChoice() {
		// zobrazit form pro vyber pobocky
		$.fancybox(
			$('#ZasilkovnaChoice').html(), {
				'autoSize'		    : true,
				'transitionIn'      : 'none',
				'transitionOut'     : 'none',
				'hideOnContentClick': false,
				'autoResize': true,
			}
        );

		$('.zasilkovna-detail').empty();
	}

	// odeslani formulare pro vyber pobocky posty
	$(document).on('change', '#ZasilkovnaChoiceSelect', function(e) {
		e.preventDefault();
		$('.zasilkovna-detail').empty();

		var id = $(this).val()

		$.ajax({
			url: '/zasilkovna_branches/ajax_get',
			method: 'POST',
			dataType: 'json',
			data: {
				id: id
			},
			success: function(data) {
				if (data.success) {
					$('.zasilkovna-detail').empty();
					$('.zasilkovna-detail').append(drawZasilkovnaDetail(data.data));
				} else {
					alert(data.message);
				}
			},
			beforeSend: function() {
				$('body').addClass('loading');
			},
			complete: function(jqXHR, textStatus) {
				$.fancybox.update();
				$('body').removeClass('loading');
			}
		});
	});

	function drawZasilkovnaDetail(branch) {
		var content = '<h4>' + branch.ZasilkovnaBranch.name + '</h4>';
		content += '<div>';
		content += '<div style="float:left"><img src="' + branch.ZasilkovnaBranch.thumb + '" /></div>';
		content += '<div style="float:left;margin-left:10px">'
		content += '<p>' + branch.ZasilkovnaBranch.place + '<br/>';
		content += branch.ZasilkovnaBranch.street + '<br/>';
		content += branch.ZasilkovnaBranch.city + ', ' + branch.ZasilkovnaBranch.zip + '</p>';
		content += '<button id="ZasilkovnaBranchChoiceLink" data-zasilkovna-id="' + branch.ZasilkovnaBranch.zasilkovna_id + '"data-zasilkovna-name="' + branch.ZasilkovnaBranch.name + '" data-zasilkovna-street="' + branch.ZasilkovnaBranch.street + '" data-zasilkovna-city="' + branch.ZasilkovnaBranch.city + '" data-zasilkovna-zip="' + branch.ZasilkovnaBranch.zip + '" class="btn btn-warning">zvolit pobočku</button>';
		content += '</div>';
		content += '</div>';
		content += '<div style="clear:both"></div>';
		content += '<div style="margin-top:10px" class="branch-choice-table">';
		content += branch.ZasilkovnaBranch.opening_hours;
		content += '</div>';
		return content;
	}

	$(document).on('click', '#ZasilkovnaBranchChoiceLink', function(e) {
		e.preventDefault();
		var branchZasilkovnaId = $(this).attr('data-zasilkovna-id');
		var branchZasilkovnaName = $(this).attr('data-zasilkovna-name');
		var branchZasilkovnaStreet = $(this).attr('data-zasilkovna-street');
		var branchZasilkovnaCity = $(this).attr('data-zasilkovna-city');
		var branchZasilkovnaZip = $(this).attr('data-zasilkovna-zip');
		var branchZasilkovnaAddress = branchZasilkovnaName + ', ' + branchZasilkovnaStreet + ', ' + branchZasilkovnaCity + ' ' + branchZasilkovnaZip;
		// prekreslim link pro vyber zasilkovny
		$('.zasilkovna-choice-link').text(branchZasilkovnaAddress);
		// naplnim dorucovaci adresu daty z pobocky
		var branchZasilkovnaInfo = branchZasilkovnaName + ' || ' + branchZasilkovnaId;
		$('#Address0Name').val(branchZasilkovnaInfo);
		$('#Address0Street').val(branchZasilkovnaStreet);
		$('#Address0City').val(branchZasilkovnaCity);
		$('#Address0Zip').val(branchZasilkovnaZip);
		$('#isDifferentAddressCheckbox').prop('checked', true);
		$.fancybox.close();
	});

	// validace formu pro odeslani objednavky (doprava, platba, info o zakaznikovi)
	$('#OrderOneStepOrderForm').submit(function(e) {
		// pokud mam zvolenou dopravu zasilkovnou
		if ($.inArray(shippingId, ZASILKOVNA_SHIPPING_IDS) != -1) {
			// a nemam nastavenou spravne adresu
			if ($('#Address0Street').val() == '' || $('#Address0City').val() == '' || $('#Address0Zip').val() == '') {
				// vypisu alert a nepovolim odeslani objednavky
				alert('Vyberte prosím pobočku záslikovny, kam si přejete Vaši objednávku doručit');
				$(document).scrollTop($('#ShippingInfo').offset().top);
				e.preventDefault();
			}
		}
	});
});
</script>
<?php }?>