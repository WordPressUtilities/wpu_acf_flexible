window.addEventListener("DOMContentLoaded", function() {

    /* Video blocks */
    (function() {

        /* Check click event on every possible video (loaded ) */
        document.body.addEventListener('click', function(e) {
            for (var target = e.target; target && target != this; target = target.parentNode) {
                if (target.matches('.wpuacf-video')) {
                    set_play_event(target);
                    break;
                }
            }
        });

        function set_play_event($video) {
            if ($video.getAttribute('data-is-loading') == '1') {
                return;
            }
            var $iframe = $video.querySelector('iframe[data-src], video[data-src]');
            $iframe.setAttribute('src', $iframe.getAttribute('data-src'));
            $video.setAttribute('data-is-loading', 1);
            setTimeout(function() {
                $video.setAttribute('data-is-playing', 1);
            }, 500);
        }
    }());

    /* FAQ */
    (function() {
        document.body.addEventListener('click', function(e) {
            var $wrapper, wasExpanded;
            for (var target = e.target; target && target != this; target = target.parentNode) {
                if (target.matches('.wpuacfflexfaq-list__item .field-question[itemprop="name"] button')) {
                    $wrapper = target.closest('.wpuacfflexfaq-list__item');
                    wasExpanded = (target.getAttribute('aria-expanded') == 'true');
                    target.setAttribute('aria-expanded', !wasExpanded ? 'false' : 'true');
                    $wrapper.setAttribute('data-is-open', !wasExpanded ? 'false' : 'true');
                    break;
                }
            }
        });
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

        /* Check resize */
        window.addEventListener('resize', function() {
            windowWidth = window.innerWidth;
            check_videos_sources();
        });

        /* Allow ajax reload */
        window.addEventListener('wpu-acf-video-check-sources', function() {
            $videos = document.querySelectorAll('[data-wpu-acf-video="1"][autoplay]');
            check_videos_sources();
        });

        /* Intersect only */
        if ("IntersectionObserver" in window) {
            var lazyVideos = [].slice.call(document.querySelectorAll('[data-wpu-acf-video="1"][data-intersect-only]'));
            var lazyVideoObserver = new IntersectionObserver(function(entries) {
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
