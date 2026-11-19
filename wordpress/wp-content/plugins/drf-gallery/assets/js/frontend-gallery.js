/**
 * DRF Foto-Alben & Galerien - Frontend Lightbox
 * Ultra-lightweight, zero-dependency, keyboard & touch swipe enabled
 */

(function($) {
    'use strict';

    var currentGallery = [];
    var currentIndex = 0;
    var $modal, $img, $caption, $counter;

    $(document).ready(function() {
        initLightbox();
    });

    function initLightbox() {
        // Build modal markup once if needed
        if (!$('#drfLightboxModal').length) {
            var markup = [
                '<div id="drfLightboxModal" class="drf-lightbox-modal" role="dialog" aria-modal="true">',
                    '<div class="drf-lightbox-counter" id="drfLightboxCounter">1 / 1</div>',
                    '<button type="button" class="drf-lightbox-close" id="drfLightboxClose" aria-label="Schließen">&times;</button>',
                    '<button type="button" class="drf-lightbox-btn drf-lightbox-prev" id="drfLightboxPrev" aria-label="Zurück">&#10094;</button>',
                    '<div class="drf-lightbox-stage">',
                        '<div class="drf-lightbox-img-wrap">',
                            '<img src="" alt="" class="drf-lightbox-img" id="drfLightboxImg">',
                        '</div>',
                        '<div class="drf-lightbox-caption" id="drfLightboxCaption"></div>',
                    '</div>',
                    '<button type="button" class="drf-lightbox-btn drf-lightbox-next" id="drfLightboxNext" aria-label="Weiter">&#10095;</button>',
                '</div>'
            ].join('');

            $('body').append(markup);
        }

        $modal = $('#drfLightboxModal');
        $img = $('#drfLightboxImg');
        $caption = $('#drfLightboxCaption');
        $counter = $('#drfLightboxCounter');

        // Click trigger on any lightbox-enabled item
        $(document).on('click', '.drf-gallery-container[data-lightbox="1"] .drf-lightbox-trigger', function(e) {
            e.preventDefault();

            var $container = $(this).closest('.drf-gallery-container');
            var $items = $container.find('.drf-gallery-item');
            var $clickedItem = $(this).closest('.drf-gallery-item');

            currentGallery = [];
            $items.each(function(i) {
                var $it = $(this);
                currentGallery.push({
                    full: $it.data('full') || $it.find('a').attr('href'),
                    caption: $it.data('caption') || $it.find('img').attr('alt') || ''
                });
                if ($it[0] === $clickedItem[0]) {
                    currentIndex = i;
                }
            });

            if (currentGallery.length > 0) {
                openLightbox(currentIndex);
            }
        });

        // Close handlers
        $('#drfLightboxClose').on('click', closeLightbox);
        $modal.on('click', function(e) {
            if ($(e.target).is('#drfLightboxModal') || $(e.target).is('.drf-lightbox-stage')) {
                closeLightbox();
            }
        });

        // Nav handlers
        $('#drfLightboxPrev').on('click', function(e) {
            e.stopPropagation();
            prevSlide();
        });

        $('#drfLightboxNext').on('click', function(e) {
            e.stopPropagation();
            nextSlide();
        });

        // Keyboard handler
        $(document).on('keydown', function(e) {
            if (!$modal.hasClass('is-active')) return;

            if (e.key === 'Escape' || e.keyCode === 27) {
                closeLightbox();
            } else if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                prevSlide();
            } else if (e.key === 'ArrowRight' || e.keyCode === 39) {
                nextSlide();
            }
        });

        // Touch Swipe Support for mobile
        var touchStartX = 0;
        var touchEndX = 0;

        $modal.on('touchstart', function(e) {
            touchStartX = e.originalEvent.changedTouches[0].screenX;
        });

        $modal.on('touchend', function(e) {
            touchEndX = e.originalEvent.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            var diff = touchEndX - touchStartX;
            if (Math.abs(diff) > 40) {
                if (diff < 0) {
                    nextSlide(); // Swiped left -> Next
                } else {
                    prevSlide(); // Swiped right -> Prev
                }
            }
        }
    }

    function openLightbox(index) {
        currentIndex = index;
        updateSlide();
        $modal.addClass('is-active');
        $('body').css('overflow', 'hidden'); // Prevent background scroll
    }

    function closeLightbox() {
        $modal.removeClass('is-active');
        $('body').css('overflow', '');
        $img.attr('src', '');
    }

    function prevSlide() {
        if (currentGallery.length <= 1) return;
        currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
        updateSlide();
    }

    function nextSlide() {
        if (currentGallery.length <= 1) return;
        currentIndex = (currentIndex + 1) % currentGallery.length;
        updateSlide();
    }

    function updateSlide() {
        if (!currentGallery[currentIndex]) return;

        var item = currentGallery[currentIndex];

        // Fade image briefly
        $img.css('opacity', '0.3');

        var preload = new Image();
        preload.onload = function() {
            $img.attr('src', item.full);
            $img.attr('alt', item.caption);
            $img.css('opacity', '1');
        };
        preload.src = item.full;

        $caption.text(item.caption);
        $counter.text((currentIndex + 1) + ' / ' + currentGallery.length);

        // Preload next image in background
        if (currentGallery.length > 1) {
            var nextIndex = (currentIndex + 1) % currentGallery.length;
            var nextImg = new Image();
            nextImg.src = currentGallery[nextIndex].full;
        }
    }

})(jQuery);
