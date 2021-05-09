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

    /* Responsive videos & lazy loading */
    (function() {
        var windowWidth = window.innerWidth,
            $videos = document.querySelectorAll('[data-wpu-acf-video="1"][autoplay]');

        check_videos_sources();
        window.addEventListener('resize', function() {
            windowWidth = window.innerWidth;
            check_videos_sources();
        });

        /* Intersect only */
        if ("IntersectionObserver" in window) {
            var lazyVideos = [].slice.call(document.querySelectorAll('[data-wpu-acf-video="1"][data-intersect-only]'));
            var lazyVideoObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(video) {
                    if (video.isIntersecting) {
                        set_query_source(video.target, true);
                    }
                });
            }, {
                rootMargin: "300px",
            });

            lazyVideos.forEach(function(lazyVideo) {
                lazyVideoObserver.observe(lazyVideo);
            });
        }

        function check_videos_sources() {
            for (var i = 0, len = $videos.length; i < len; i++) {
                set_query_source($videos[i], false);
            }
        }

        function set_query_source($video, hasIntersect) {
            var $source = $video.querySelector('source'),
                currentSrc = $source.getAttribute('src'),
                dataSrc = $source.getAttribute('data-src');

            /* Not needed */
            if (!dataSrc) {
                return;
            }

            /* Source is already ok */
            if (currentSrc == dataSrc) {
                return;
            }

            /* Intersect only video */
            if ($video.getAttribute('data-intersect-only') == '1' && !hasIntersect) {
                return;
            }

            /* Mobile only video */
            if ($video.getAttribute('data-mobile-only') == '1' && windowWidth > 768) {
                return;
            }

            /* Desktop only video */
            if ($video.getAttribute('data-desktop-only') == '1' && windowWidth <= 768) {
                return;
            }

            /* Set source */
            $source.setAttribute('src', dataSrc);
            $video.load();
            $video.play();
        }

    }());

});
