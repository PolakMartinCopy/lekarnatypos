$(document).ready(function() {
	// inicializace
	var visitId = null;
	$.ajax({
		url: '/t_s_customer_devices/init',
		async: false,
		dataType: 'json',
		success: function(data) {
			if (data.success) {
				visitId = data.visitId;
			}
		}
	});

	if (visitId) {
		// mereni delky navstevy
		var firstClick = true;
		$(document).click(function() {
			if (firstClick) {
				firstClick = false;
				// ajaxem prepocitam delku navstevy
				$.ajax({
					url: '/t_s_visits/recount_duration/' + visitId,
//					async: false
				});
			}
		});
	}
});