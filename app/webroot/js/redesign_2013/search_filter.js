$(function() {
	$('.paging').change(function() {
		$('#SearchPaging').val($(this).val());
		$('#search_form').submit();
	});
	
	$('.sorting').change(function() {
		$('#SearchSorting').val($(this).val());
		$('#search_form').submit();
	});
});