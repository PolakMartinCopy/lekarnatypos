jQuery(document).ready(function ($) {
	// prepnuti mezi KATEGORIE a PODLE NEMOCI
	$('.categories-bothers-switch').click(function(e) {
		var tab = $(this).attr('aria-controls');
		$.ajax({
			url: '/tools/categories_bothers_tab/',
			data: {
				tab: tab
			},
			type: 'POST'
		});
	});
	
	function hideFilters() {
		$('.module-filters .select').each(function($data) {
			$(this).find('span').removeClass('expanded');
			// schovam filtr
			$(this).find('.items').hide();
		});
	}
	
	// sprava filtru produktu na detailu kategorie
	$('.module-filters .select .filter-selector').click(function(e) {
		// zapamatuju si puvodni stav filtru, na ktery jsem klikl
		var prevClass = $(this).attr('class');
		// skryt vsechny filtry
		hideFilters();
		if (prevClass == 'filter-selector') {
			// nastavit u toho, na ktery jsem klikl
			$(this).addClass('expanded');
			$(this).parent().find('.items').show();
		}
	});
	
	$(document).mouseup(function (e) {
		var container = $('.module-filters .select');
		// if the target of the click isn't the container...
		// ... nor a descendant of the container
	    if (!container.is(e.target) && container.has(e.target).length === 0) {
	        hideFilters();
	    }
	});
	
	// zaklikavani vyrobcu ve filtru na detailu kategorie
	$('#brand .items ul li a').click(function(e) {
		e.preventDefault();
		var className = this.className;
		var manufacturerId = $(this).attr('data-manufacturer-id');
		var selected = $("#brand .items select option:selected").map(function(){ return this.value }).get();
		// vyrobce byl zatrzeny
		if (className == 'checked') {
			// zrusim zatrzeni
			$(this).removeClass('checked');
			// odstranim ho z filtru
			index = selected.indexOf(manufacturerId);
			if (index > -1) {
				selected.splice(index, 1);
			}
		} else {
			// zatrhnu ho
			$(this).addClass('checked');
			// pridam do filtru
			selected.push(manufacturerId);
		}
		$('#brand .items select').val(selected);
		$('#filterDefaultTab').val('brand');
	});
	
	// zaklikavani razeni ve filtru na detailu kategorie
	$('#sorting .items ul li a').click(function(e) {
		e.preventDefault();
		var className = this.className;
		var sortingId = $(this).attr('data-sorting-id');
		// razeni nebylo zatrzeno
		if (className != 'checked') {
			// zrusim zatrzeni vsech
			$('#sorting .items ul li a').removeClass('checked');
			// zatrhnu ten jeden
			$(this).addClass('checked');
			// oznacim ten jeden jako selected
			$('#sorting .items select').val(sortingId);			
		}
		$('#filterDefaultTab').val('sorting');
	});

	// odeslani formulare s filtraci a razenim
	$body = $("body");
	$('#filterForm .items ul li a').click(function(e) {
		// zobrazim loading spinner
		$body.addClass("loading");
		// odeslu form
		$('#filterForm').submit();
	});
	
	$('.clear-filter').click(function(e) {
		e.preventDefault();
		var identificator = $(this).attr('data-identificator');
		$('#resetFilter').val(identificator);
		// zobrazim loading spinner
		$body.addClass("loading");
		// odeslu form
		$('#filterForm').submit();
	});
	
    if ($('.floating-labels').length > 0) floatLabels();

    function floatLabels() {
        var inputFields = $('.floating-labels label').next();
        inputFields.each(function () {
            var singleInput = $(this);
            //check if user is filling one of the form fields 
            checkVal(singleInput);
            singleInput.on('change keyup', function () {
                checkVal(singleInput);
            });
        });
    }

    function checkVal(inputField) {
        (inputField.val() == '') ? inputField.prev('label').removeClass('float') : inputField.prev('label').addClass('float');
    }

    $(".user-info .fa").click(function () {
        $(this).parent().find(".user-info-box").toggleClass("hidden-xs");
    });

    $(".basket-info .fa").click(function () {
        $(this).parent().find(".basket-info-box").toggleClass("hidden-xs");
    });

    $(document).click(function (e) {
        if (e.target.class != 'user-info' && !$(".user-info").find(e.target).length) {
            $(".user-info-box").addClass("hidden-xs");
        }
        if (e.target.class != 'basket-info' && !$(".basket-info").find(e.target).length) {
            $(".basket-info-box").addClass("hidden-xs");
        }
    });

    function addCount() {
        var countInput = $(this).parent().find("input");
        var currentVal = parseInt(countInput.val());
        if (countInput.val() == "") {
            countInput.val(1);
        }
        else {
            countInput.val(currentVal + 1);
        }
    }

    function removeCount() {
        var countInput = $(this).parent().find("input");
        var currentVal = parseInt(countInput.val());
        if (currentVal > 1) {
            countInput.val(currentVal - 1);
        }
    }

    $(".count-add").click(addCount);
    $(".count-remove").click(removeCount);
    
    // JEDNOKROKOVA OBJEDNAVKA
	if ($('#CustomerIsRegistered1').is(':checked')) {
		$('#CustomerOneStepOrderDiv').show();
	}
	$('input.customer-is-registered').change(function() {
		if (this.id == 'CustomerIsRegistered1') {
			$('#CustomerOneStepOrderDiv').show();
		} else {
			$('#CustomerOneStepOrderDiv').hide();
		}
	});

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
	
	$('#isCompany').change(function() {
		// pokud mam dorucovaci adresu ruznou od fakturacni
		if ($(this).is(':checked')) {
			// zobrazim tabulku pro dorucovaci adresu
			$('#companyTable').show();
		} else {
			// schovam tabulku pro dorucovaci adresu
			$('#companyTable').hide();
		}
	});

    // slider pro vyber rozmezi podle ceny definuju pouze, pokud je na strance spravny element
	if ($('.slider').length) {
	    $('.slider').slider()
	    .on('slideStop', function(ev) {
	    	// zjistim navolene hodnoty
	    	var values = $(this).val();
	    	values = values.split(',');
	    	var minValue = values[0];
	    	var maxValue = values[1];
	    		
	    	// nastavim formularova pole
	    	$('#filterPriceMin').val(minValue);
	    	$('#filterPriceMax').val(maxValue);
	    		
	    	// nastavim tab, ktery se ma po natazeni produktu otevrit
	    	$('#filterDefaultTab').val('price');
	    		
	    	// zobrazim loading spinner
	    	$body.addClass("loading");
	    	// odeslu form
	    	$('#filterForm').submit();
	    });
	}
	
	// ODESILANI ZMEN V KOSIKU
	$('#cart #CartsProductQuantity').change(function(e) {
		// zobrazim loading spinner
		$body.addClass("loading");
		$(this).parent().parent().submit();
	});
	
	$('#cart .count-add').click(function(e) {
		// zobrazim loading spinner
		$body.addClass("loading");
		$(this).parent().parent().submit();
	});
	
	$('#cart .count-remove').click(function(e) {
		// zobrazim loading spinner
		$body.addClass("loading");
		$(this).parent().parent().submit();
	});
});