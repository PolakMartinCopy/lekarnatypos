$(document).ready(function() {
	// natahnu ajaxem kategorie
	$.ajax({
		type: 'POST',
		url: '/categories/ajax_menu',
		data: {
			openedCategoryId: window.openedCategoryId,
			isCustomerLoggedIn: window.isCustomerLoggedIn
		},
		dataType: 'json',
		success: function(data) {
			if (!data.success) {
				alert(data.message);
			} else {
				categories = data.data.categories;
				pathIds = data.data.path_ids;
				menu = '<ul class="categories">';
				$.each(categories, function(key, value) {
					categoryId = value.Category.id;
					categoryName = value.Category.name;
					categoryUrl = value.Category.url;
					categoryChildren = value.children;
					listItemClass = '';
					if (pathIds.indexOf(categoryId) != -1) {
						listItemClass = ' class="active"';
					}
					menu += '<li' + listItemClass + '><a href="/' + categoryUrl + '">' + categoryName + '</a>';
					if (typeof categoryChildren != 'undefined' && categoryChildren.length > 0) {
						menu += '<ul>';
						$.each(categoryChildren, function(childKey, childValue) {
							childCategoryName = childValue.Category.name;
							childCategoryUrl = childValue.Category.url;
							childCategoryChildren = childValue.children;
							menu += '<li><a href="/' + childCategoryUrl + '"><i class="fa fa-caret-right"></i>' + childCategoryName + '</a>';
							if (typeof childCategoryChildren != 'undefined' && childCategoryChildren.length > 0) {
								menu += '<ul>';
								$.each(childCategoryChildren, function(gChildKey, gChildValue) {
									gChildCategoryName = gChildValue.Category.name;
									gChildCategoryUrl = gChildValue.Category.url;
/*									gChildCategoryChildren = gChildValue.Category.children; */
									menu += '<li><a href="/' + gChildCategoryUrl + '"><i class="fa fa-caret-right"></i>' + gChildCategoryName + '</a></li>';
								});
								menu += '</ul>';
							}
							menu += '</li>'; 
						});
						menu += '</ul>';
					}
					menu += '</li>';
				});
				menu += '</ul>';
				$('#categoriesMenu').html(menu);
				
				$('ul.categories a').bind('click', function(e) {
					e.preventDefault();
				});
				
				$('ul.categories li').bind('mouseenter', function() {
					console.log('zobrazit menu podkategorii');
				});
				
				$('ul.categories li').bind('mouseout', function() {
					console.log('skryt menu podkategorii');
				});
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(textStatus);
		}
	});

});