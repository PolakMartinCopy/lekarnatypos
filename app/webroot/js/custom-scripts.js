$(document).ready(function () {

    var src = $(".intro-video").find('iframe').attr('src');
    $(".intro-video").find('iframe').hide();

    $(".intro-video").find('iframe').attr('src', '');

    $('.video-play-btn').on('click', function () {
        $(".intro-video").find('iframe').show();
        $('.intro-video').find('iframe').attr('src', src);
    });

});