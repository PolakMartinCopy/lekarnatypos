$(document).ready(function(){
	// get the clicked rate !
	$(".rating").jRating({
		bigStarsPath: '/jRating-master/jquery/icons/stars.png',
		smallStarsPath: '/jRating-master/jquery/icons/small.png',
		type: ratingStarType,
		step: true,
		rateMax: 5,
    	showRateInfo: false,
    	// nechci posilat data na server jejich metodou, definuju si vlastni ajax (nize)
    	sendRequest: false,
    	onClick: function(element, rate, id) {
			$.ajax({
				'url': 'products/rate',
				'type': 'POST',
				'dataType': 'json',
				'data' : {
					id: id,
					rate: rate
				},
				'success': function(data) {
					alert(data.message);
				},
				'error': function(jqXHR, textStatus) {
					alert(textStatus);
				}
			});
    	}
      });
	
	// get the clicked rate !
	$(".g_rating").jRating({
		bigStarsPath: '/jRating-master/jquery/icons/stars.png',
		smallStarsPath: '/jRating-master/jquery/icons/small_grey.png',
		type: 'small',
		step: true,
		rateMax: 5,
    	showRateInfo: false,
    	// nechci posilat data na server jejich metodou, definuju si vlastni ajax (nize)
    	sendRequest: false,
    	onClick: function(element, rate, id) {
			$.ajax({
				'url': 'products/rate',
				'type': 'POST',
				'dataType': 'json',
				'data' : {
					id: id,
					rate: rate
				},
				'success': function(data) {
					alert(data.message);
				},
				'error': function(jqXHR, textStatus) {
					alert(textStatus);
				}
			});
    	}
      });
});