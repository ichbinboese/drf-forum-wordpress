/**
 * DRF Foto-Alben & Galerien - Admin JavaScript
 * Drag & Drop Reordering, HTML5 Dropzone Uploads, WP Media Integration
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initShortcodeCopy();
        initCoverPicker();

        // Only on album editor page with valid albumId
        if (typeof drfGalleryAdmin !== 'undefined' && drfGalleryAdmin.albumId > 0) {
            initSortableGrid();
            initDropzoneUpload();
            initMediaLibraryPicker();
            initTileActions();
        }
    });

    /**
     * 1. 1-Click Shortcode Copy
     */
    function initShortcodeCopy() {
        $(document).on('click', '.drf-btn-copy', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var text = $btn.data('copy') || $btn.prev('code').text();

            if (!text) return;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    showCopiedFeedback($btn);
                }).catch(function() {
                    fallbackCopy(text, $btn);
                });
            } else {
                fallbackCopy(text, $btn);
            }
        });
    }

    function fallbackCopy(text, $btn) {
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(text).select();
        document.execCommand('copy');
        $temp.remove();
        showCopiedFeedback($btn);
    }

    function showCopiedFeedback($btn) {
        var original = $btn.text();
        $btn.text('✓ Kopiert!').addClass('copied');
        setTimeout(function() {
            $btn.text(original).removeClass('copied');
        }, 1800);
    }

    /**
     * 2. Cover Image Picker (WP Media)
     */
    function initCoverPicker() {
        var coverFrame;
        $('#btn_choose_cover').on('click', function(e) {
            e.preventDefault();

            if (coverFrame) {
                coverFrame.open();
                return;
            }

            coverFrame = wp.media({
                title: 'Cover-Bild für Album auswählen',
                button: { text: 'Als Cover festlegen' },
                multiple: false,
                library: { type: 'image' }
            });

            coverFrame.on('select', function() {
                var attachment = coverFrame.state().get('selection').first().toJSON();
                $('#cover_image_id').val(attachment.id);

                var previewUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                $('#cover_preview').html('<img src="' + previewUrl + '" alt="Cover" style="max-width:100%; height:auto; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">');
            });

            coverFrame.open();
        });
    }

    /**
     * 3. Drag & Drop Sortable Images Grid
     */
    function initSortableGrid() {
        var $grid = $('#drfSortableGrid');
        if (!$grid.length) return;

        $grid.sortable({
            items: '.drf-image-tile',
            handle: '.tile-handle',
            cursor: 'grabbing',
            placeholder: 'drf-sortable-placeholder',
            opacity: 0.85,
            tolerance: 'pointer',
            update: function() {
                saveOrder();
            }
        });
    }

    function saveOrder() {
        var order = [];
        $('#drfSortableGrid .drf-image-tile').each(function() {
            var id = $(this).data('id');
            if (id) {
                order.push(id);
            }
        });

        if (!order.length) return;

        var $status = $('#drfSortStatus');
        $status.text('⏳ Speichere Reihenfolge...').fadeIn(150);

        $.ajax({
            url: drfGalleryAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'drf_reorder_album_images',
                nonce: drfGalleryAdmin.nonce,
                album_id: drfGalleryAdmin.albumId,
                order: order
            },
            success: function(res) {
                if (res.success) {
                    $status.text('✓ Reihenfolge gespeichert!').css('background', 'rgba(16, 185, 129, 0.15)').css('color', '#059669');
                    setTimeout(function() {
                        $status.fadeOut(300);
                    }, 2000);
                } else {
                    $status.text('Fehler beim Speichern').css('color', '#ef4444');
                }
            },
            error: function() {
                $status.text('Serverfehler').css('color', '#ef4444');
            }
        });
    }

    /**
     * 4. HTML5 Drag & Drop File Upload
     */
    function initDropzoneUpload() {
        var $dropzone = $('#drfDropzone');
        var $fileInput = $('#drfFileInput');

        if (!$dropzone.length) return;

        // Prevent browser default file open behavior
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
            window.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
            $dropzone[0].addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        // Highlight visual feedback
        $dropzone.on('dragenter dragover', function(e) {
            $dropzone.addClass('dragover');
        });

        $dropzone.on('dragleave dragend drop', function(e) {
            $dropzone.removeClass('dragover');
        });

        // Drop event
        $dropzone.on('drop', function(e) {
            var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
            if (files && files.length) {
                processFilesQueue(files);
            }
        });

        // File input change
        $fileInput.on('change', function() {
            if (this.files && this.files.length) {
                processFilesQueue(this.files);
                this.value = ''; // reset so same files can be chosen again
            }
        });
    }

    /**
     * Upload queue with progress bar
     */
    function processFilesQueue(files) {
        var validFiles = [];
        for (var i = 0; i < files.length; i++) {
            if (files[i].type.indexOf('image/') === 0) {
                validFiles.push(files[i]);
            }
        }

        if (!validFiles.length) {
            alert('Bitte nur Bilddateien (JPG, PNG, WebP) ablegen.');
            return;
        }

        var $progressWrap = $('#drfUploadProgress');
        var $progressFill = $('#drfProgressFill');
        var $progressText = $('#drfProgressText');

        $progressWrap.show();
        $progressFill.css('width', '5%');
        $progressText.text('Lade ' + validFiles.length + ' Foto(s) hoch...');

        var total = validFiles.length;
        var current = 0;

        function uploadNext() {
            if (current >= total) {
                $progressFill.css('width', '100%');
                $progressText.text('✓ Alle ' + total + ' Fotos erfolgreich hochgeladen!');
                setTimeout(function() {
                    $progressWrap.fadeOut(400);
                }, 2200);
                updateImageCount();
                return;
            }

            var file = validFiles[current];
            var percent = Math.round(((current) / total) * 100);
            $progressFill.css('width', percent + '%');
            $progressText.text('Lade hoch: ' + file.name + ' (' + (current + 1) + ' von ' + total + ')...');

            var formData = new FormData();
            formData.append('action', 'drf_upload_gallery_image');
            formData.append('nonce', drfGalleryAdmin.nonce);
            formData.append('album_id', drfGalleryAdmin.albumId);
            formData.append('file', file);

            $.ajax({
                url: drfGalleryAdmin.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success && res.data) {
                        appendTile(res.data);
                    } else {
                        console.error('Upload-Fehler bei ' + file.name, res);
                    }
                    current++;
                    uploadNext();
                },
                error: function(xhr, status, error) {
                    console.error('Serverfehler bei ' + file.name, error);
                    current++;
                    uploadNext();
                }
            });
        }

        uploadNext();
    }

    /**
     * 5. Add Existing Media from WordPress Media Library
     */
    function initMediaLibraryPicker() {
        var mediaFrame;
        $('#drfBtnMediaLibrary').on('click', function(e) {
            e.preventDefault();

            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: 'Bilder für das Album auswählen',
                button: { text: 'Auswahl zum Album hinzufügen' },
                multiple: true,
                library: { type: 'image' }
            });

            mediaFrame.on('select', function() {
                var selection = mediaFrame.state().get('selection');
                var ids = [];
                selection.each(function(attachment) {
                    ids.push(attachment.get('id'));
                });

                if (!ids.length) return;

                var $status = $('#drfSortStatus');
                $status.text('Füge Bilder hinzu...').fadeIn(150);

                $.ajax({
                    url: drfGalleryAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'drf_add_existing_media',
                        nonce: drfGalleryAdmin.nonce,
                        album_id: drfGalleryAdmin.albumId,
                        attachment_ids: ids
                    },
                    success: function(res) {
                        if (res.success && res.data && res.data.added) {
                            res.data.added.forEach(function(item) {
                                appendTile(item);
                            });
                            updateImageCount();
                            $status.text('✓ ' + res.data.added.length + ' Bilder hinzugefügt').fadeOut(2000);
                        } else {
                            $status.text('Fehler beim Hinzufügen').css('color', '#ef4444');
                        }
                    },
                    error: function() {
                        $status.text('Serverfehler').css('color', '#ef4444');
                    }
                });
            });

            mediaFrame.open();
        });
    }

    /**
     * 6. Helper: Append newly added image tile to sortable grid
     */
    function appendTile(item) {
        $('#drfNoImagesNotice').hide();

        var caption = item.caption || item.title || '';
        var thumb = item.thumb_url || '';

        var html = [
            '<div class="drf-image-tile" data-id="' + item.id + '" data-attachment-id="' + item.attachment_id + '" style="display:none;">',
                '<div class="tile-handle" title="Ziehen zum Sortieren">',
                    '<span class="dashicons dashicons-move"></span>',
                '</div>',
                '<div class="tile-thumb-wrap">',
                    '<img src="' + thumb + '" alt="' + caption + '" loading="lazy">',
                '</div>',
                '<div class="tile-info">',
                    '<input type="text" class="tile-caption-input" value="' + caption + '" placeholder="Bildunterschrift..." title="Klicken zum Bearbeiten">',
                '</div>',
                '<button type="button" class="tile-btn-delete" title="Aus Album entfernen">&times;</button>',
            '</div>'
        ].join('');

        var $tile = $(html);
        $('#drfSortableGrid').append($tile);
        $tile.fadeIn(300);

        // Refresh sortable
        if ($('#drfSortableGrid').data('ui-sortable')) {
            $('#drfSortableGrid').sortable('refresh');
        }
    }

    /**
     * 7. Tile Actions: Delete and Caption Edit
     */
    function initTileActions() {
        var $grid = $('#drfSortableGrid');

        // Delete Image
        $grid.on('click', '.tile-btn-delete', function(e) {
            e.preventDefault();
            var $tile = $(this).closest('.drf-image-tile');
            var imageId = $tile.data('id');

            if (!confirm('Bild wirklich aus diesem Album entfernen? (Die Datei in der Mediathek bleibt erhalten)')) {
                return;
            }

            $tile.css('opacity', '0.4');

            $.ajax({
                url: drfGalleryAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'drf_remove_album_image',
                    nonce: drfGalleryAdmin.nonce,
                    image_id: imageId
                },
                success: function(res) {
                    if (res.success) {
                        $tile.fadeOut(250, function() {
                            $(this).remove();
                            updateImageCount();
                        });
                    } else {
                        alert(res.data && res.data.message ? res.data.message : 'Fehler beim Löschen');
                        $tile.css('opacity', '1');
                    }
                },
                error: function() {
                    alert('Serverfehler beim Löschen');
                    $tile.css('opacity', '1');
                }
            });
        });

        // Inline Caption Editing (blur & enter)
        $grid.on('blur', '.tile-caption-input', function() {
            saveCaption($(this));
        });

        $grid.on('keypress', '.tile-caption-input', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $(this).blur();
            }
        });
    }

    function saveCaption($input) {
        var $tile = $input.closest('.drf-image-tile');
        var imageId = $tile.data('id');
        var newCaption = $input.val();
        var origCaption = $input.data('original-caption');

        if (origCaption !== undefined && origCaption === newCaption) {
            return;
        }

        $input.css('background-color', '#fffbeb');

        $.ajax({
            url: drfGalleryAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'drf_update_image_caption',
                nonce: drfGalleryAdmin.nonce,
                image_id: imageId,
                caption: newCaption
            },
            success: function(res) {
                $input.data('original-caption', newCaption);
                $input.css('background-color', '#f0fdf4');
                setTimeout(function() {
                    $input.css('background-color', '');
                }, 1000);
            },
            error: function() {
                $input.css('background-color', '#fef2f2');
            }
        });
    }

    function updateImageCount() {
        var count = $('#drfSortableGrid .drf-image-tile').length;
        $('#drfImageCount').text(count);
        if (count === 0) {
            $('#drfNoImagesNotice').fadeIn(200);
        } else {
            $('#drfNoImagesNotice').hide();
        }
    }

})(jQuery);
