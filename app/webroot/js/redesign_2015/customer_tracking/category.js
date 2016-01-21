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

function sortingShow(visitCategoryId) {
	res = false;
	$.ajax({
		url: '/t_s_visit_category_sorting_shows/my_create/' + visitCategoryId,
		async: false,
		dataType: 'json',
		success: function(data) {
			if (data.success) {
				res = data.data;
			}
		}
	})
	return res;
}

function lastSortingShow(visitCategoryId) {
	$res = false;
	$.ajax({
		url: '/t_s_visit_category_sorting_shows/find_last/' + visitCategoryId,
		async: false,
		dataType: 'json',
		success: function(data) {
			if (data.success) {
				res = data.data;
			}
		}
	})
	return res;
}

function sortingChange(sortingShowId, sortingId) {
	res = false;
	$.ajax({
		url: '/t_s_visit_category_sorting_changes/my_create/' + sortingShowId + '/' + sortingId,
		async: false,
		dataType: 'json',
		success: function(data) {
			if (data.success) {
				res = data.data;
			}
		}
	})
	return res;
}



$(document).ready(function() {
	visitCategoryId = categoryView(categoryId);
	var sortingShowId = null;
	
	if ($('#filterForm #sorting .filter-selector').hasClass('expanded')) {
		sortingShowId = lastSortingShow(visitCategoryId);
	}
	
	$('#filterForm #sorting .filter-selector').click(function() {
		// zobrazeni ukladam, jen pokud byl element shovany a opravu jsem ho zobrazil (a ne ze byl zobrazeny a ja jsem ho schoval)
		if (!$(this).hasClass('expanded')) {
			sortingShowId = sortingShow(visitCategoryId);
		}
	});
	
	// zaklikavani razeni ve filtru na detailu kategorie
	$('#sorting .items ul li a').click(function(e) {
		if (sortingShowId) {
			e.preventDefault();
			var className = this.className;
			var sortingId = $(this).attr('data-sorting-id');
			// razeni nebylo zatrzeno
			if (className != 'checked') {
				// ulozim si, ze jsem zvolil razeni dle daneho parametru
				sortingChangeId = sortingChange(sortingShowId, sortingId);
			}
		}
	});
});