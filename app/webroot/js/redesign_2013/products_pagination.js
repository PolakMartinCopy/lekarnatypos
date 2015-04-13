$(function() {
	$('.paging').change(function() {
		$('#body').mask('Načítám...');
		this.form.submit();
	});
	
	$('.sorting').change(function() {
		$('#body').mask('Načítám...');
		this.form.submit();
	});

	selected_man_str = $('#CategoriesProductManufacturerId').val();
	if (!selected_man_str) {
		selected_man_str = '';
	}
	if (selected_man_str != '') {
/*		$('.filter_manufacturer').attr('checked', 'checked');
	} else { */
		selected_man_arr = selected_man_str.split(',');
		for (i = 0; i < selected_man_arr.length; i++) {
			selected_man_item = selected_man_arr[i];
			$('#Manufacturer' + selected_man_item).attr('checked', 'checked');
		}
	}
	
	selected_att_str = $('#CategoriesProductAttributeId').val();
	if (!selected_att_str) {
		selected_att_str = '';
	}
	if (selected_att_str != '') {
/*		$('.filter_attribute').attr('checked', 'checked');
	} else { */
		selected_att_arr = selected_att_str.split(',');
		for (i = 0; i < selected_att_arr.length; i++) {
			selected_att_item = selected_att_arr[i];
			$('#Attribute' + selected_att_item).attr('checked', 'checked');
		}
	}
	
	$('#body').unmask();
	
	// pokud nekdo klikne na polozku ve filtru podle vyrobcu
	$('.filter_manufacturer').change(function() {
		// zobrazi se loading info
		$('#body').mask('Načítám...');
		// inicializace pole pro zjisteni zaskrtnutych polozek
		selected_man = [];
		// vyberu vsechny zaskrtnute polozky z filtru vyrobcu
		$('.filter_manufacturer:checked').each(function() {
			selected_man.push($(this).attr('rel'));
		});
		// pokud nemam nic zaskrtnute
		if (selected_man.length == 0) {
			// chci vybrat vsechny vyrobce
			$('.filter_manufacturer').each(function() {
				selected_man.push($(this).attr('rel'));
			});
		}
		$('#CategoriesProductManufacturerId').val(selected_man.toString());
		$('#filter_form').submit();
	});
	
	$('.filter_attribute').change(function() {
		$('#body').mask('Načítám...');
		selected_att = [];
		$('.filter_attribute:checked').each(function() {
			selected_att.push($(this).attr('rel'));
		});
		// pokud nemam nic zaskrtnute
		if (selected_att.length == 0) {
			// chci vybrat vsechny vyrobce
			$('.filter_attribute').each(function() {
				selected_att.push($(this).attr('rel'));
			});
		}
		$('#CategoriesProductAttributeId').val(selected_att.toString());
		$('#filter_form').submit();
	});
});