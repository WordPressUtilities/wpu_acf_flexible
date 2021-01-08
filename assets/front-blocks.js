window.addEventListener("DOMContentLoaded", function(e) {

    (function() {
        var $videos = document.querySelectorAll('.wpuacf-video');
        for (var i = 0, len = $videos.length; i < len; i++) {
            set_play_event($videos[i]);
        }

        function set_play_event($video) {
            $video.addEventListener('click', function() {
                var $iframe = $video.querySelector('iframe');
                $iframe.setAttribute('src', $iframe.getAttribute('data-src'));
                $video.setAttribute('data-is-loading', 1);
                setTimeout(function() {
                    $video.setAttribute('data-is-playing', 1);
                }, 500);
            }, false);
        }
    }());

});
