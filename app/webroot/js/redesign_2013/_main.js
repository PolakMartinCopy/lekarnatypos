$(function() {
	$("#header ul.accordion li a").click(function(){
		if($(this).attr('href')[0] == "#"){
			$("#basket").hide();
			$("#login").hide();
			$("#info").hide();
			$($(this).attr('href')).show();
			$(this).parent().parent().find('li').removeClass('active');
			var tab = $(this).parent().attr('class');
			$(this).parent().addClass("active");
			
			$.ajax({
				url: '/tools/login_box_tab',
				dataType: 'json',
				type: 'post',
				data: {tab: tab},
			});
			return false;
		}
	});

	$("#slides").slidorion();

	$(".tabs").tabs();
	$(".fancybox").fancybox();
});