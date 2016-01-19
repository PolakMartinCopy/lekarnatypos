function productVisit(productId) {
	$.ajax({
		url: '/t_s_visit_products/my_create/' + productId,
		async: false
	});
}
	
function productDescClick(productId) {
	$('a[data-role="showDescription"]').click(function() {
		$.ajax({
			url: '/t_s_visit_products/product_description_shown/' + productId
		});
	});
}
	
function productCommentsClick(productId) {
	$('a[data-role="showComments"]').click(function() {
		$.ajax({
			url: '/t_s_visit_products/product_comments_shown/' + productId
		});
	});
}