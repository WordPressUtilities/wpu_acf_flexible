window.addEventListener("DOMContentLoaded", function(e) {

    /* Video blocks */
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

    /* Force autoplay on mobile videos */
    (function() {
        document.body.addEventListener('touchstart', function() {
            var $videos = document.querySelectorAll('[data-wpu-acf-video="1"][autoplay]');
            for (var i = 0, len = $videos.length; i < len; i++) {
                if (!$videos[i].playing) {
                    $videos[i].play();
                }
            }
        }, {
            once: true
        });
    }());

});
