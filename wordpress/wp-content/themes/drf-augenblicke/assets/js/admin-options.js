/**
 * DRF Augenblicke - Theme Administration JavaScript
 */
jQuery(document).ready(function ($) {
    // 1. Tab Switching
    $('.drf-nav-item').on('click', function () {
        var targetTab = $(this).data('tab');

        $('.drf-nav-item').removeClass('active');
        $(this).addClass('active');

        $('.drf-tab-pane').removeClass('active');
        $('#' + targetTab).addClass('active');

        // Store active tab in localStorage
        if (window.localStorage) {
            localStorage.setItem('drf_active_admin_tab', targetTab);
        }
    });

    // Restore last active tab
    if (window.localStorage) {
        var savedTab = localStorage.getItem('drf_active_admin_tab');
        if (savedTab && $('#' + savedTab).length) {
            $('.drf-nav-item[data-tab="' + savedTab + '"]').trigger('click');
        }
    }

    // 2. Initialize WordPress Color Picker
    if ($.fn.wpColorPicker) {
        $('.drf-color-picker').wpColorPicker();
    }

    // 3. WordPress Media Uploader
    var mediaFrame;
    $('.drf-media-upload-btn').on('click', function (e) {
        e.preventDefault();
        var targetInput = $($(this).data('target'));
        var previewContainer = $(this).siblings('.drf-media-preview');

        // If frame already exists, open it
        if (mediaFrame) {
            mediaFrame.off('select');
            mediaFrame.on('select', function () {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
                if (previewContainer.length) {
                    previewContainer.html('<img src="' + attachment.url + '" alt="Vorschau" style="max-height:80px; margin-top:8px;">');
                }
            });
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title: 'Bild auswählen oder hochladen',
            button: {
                text: 'Dieses Bild verwenden'
            },
            multiple: false
        });

        mediaFrame.on('select', function () {
            var attachment = mediaFrame.state().get('selection').first().toJSON();
            targetInput.val(attachment.url);
            if (previewContainer.length) {
                previewContainer.html('<img src="' + attachment.url + '" alt="Vorschau" style="max-height:80px; margin-top:8px;">');
            }
        });

        mediaFrame.open();
    });
});
