function categoryView(categoryId) {
	$.ajax({
		url: '/t_s_visit_categories/my_create/' + categoryId,
		async: false
	});
}