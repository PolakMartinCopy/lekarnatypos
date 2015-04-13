<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/custom-scripts.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	/* Apply fancybox to multiple items */
	$("a.product-image-link").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	false
	});
	
});
</script>
<script type="text/javascript">
	window.openedCategoryId = <?php echo (isset($opened_category_id) ? $opened_category_id : ROOT_CATEGORY_ID) ?>;
	window.isCustomerLoggedIn = <?php echo intval($this->Session->check('Customer'))?>;
</script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/masonry/3.2.2/masonry.pkgd.min.js"></script>

	<script type="text/javascript">

	var containers = $('ul.categories > li');
	$.each(containers, function(index, container) {
		var id = $(container).attr('id');
		var c = $('#' + id + ' .subcategories-box');

		c.masonry({
			columnWidth: 200,
			itemSelector: '.subcategories-box-subcategory',
			containerStyle: 'none'
		});
	});

	$('.subcategories-box').hide();
	$('ul.categories > li').hover(function() {
		var id = $(this).attr('id');
		var c = $('#' + id + ' .subcategories-box');

		c.show();
	}, function() {
		var id = $(this).attr('id');
		var c = $('#' + id + ' .subcategories-box');

		c.hide();
	});

/*	$('ul.categories > li').mouseout(function() {
		var id = $(this).attr('id');
		var c = $('#' + id + ' .subcategories-box');

		c.hide();
	}); */
	</script>
<!-- <script src="/js/typos/categories.js?ver=2"></script> -->