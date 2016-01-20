function categoryView(categoryId) {
	res = false;
	$.ajax({
		url: '/t_s_visit_categories/my_create/' + categoryId,
		async: false,
		dataType: 'json',
		success: function(data) {
			if (data.success) {
				res = data.data;
			}
		}
	});
	return res;
}