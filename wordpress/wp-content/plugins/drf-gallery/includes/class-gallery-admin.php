<?php
/**
 * DRF Gallery Admin Dashboard & Album Manager
 *
 * @package DRF_Gallery
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Gallery_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_init', [$this, 'handle_album_actions']);

        // AJAX handlers
        add_action('wp_ajax_drf_upload_gallery_image', [$this, 'ajax_upload_image']);
        add_action('wp_ajax_drf_add_existing_media', [$this, 'ajax_add_existing_media']);
        add_action('wp_ajax_drf_reorder_album_images', [$this, 'ajax_reorder_images']);
        add_action('wp_ajax_drf_remove_album_image', [$this, 'ajax_remove_image']);
        add_action('wp_ajax_drf_update_image_caption', [$this, 'ajax_update_caption']);
    }

    public function register_admin_menus() {
        add_menu_page(
            __('Foto-Alben', 'drf-gallery'),
            __('Foto-Alben', 'drf-gallery'),
            'upload_files',
            'drf-gallery',
            [$this, 'render_admin_page'],
            'dashicons-format-gallery',
            24
        );

        add_submenu_page(
            'drf-gallery',
            __('Alle Alben', 'drf-gallery'),
            __('Alle Alben', 'drf-gallery'),
            'upload_files',
            'drf-gallery',
            [$this, 'render_admin_page']
        );

        add_submenu_page(
            'drf-gallery',
            __('Neues Album', 'drf-gallery'),
            __('Neues Album', 'drf-gallery'),
            'upload_files',
            'drf-gallery-new',
            [$this, 'render_new_album_page']
        );
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'drf-gallery') === false) {
            return;
        }

        // Native WordPress media library & sortable
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');

        wp_enqueue_style(
            'drf-gallery-admin-css',
            DRF_GALLERY_URL . 'assets/css/admin-gallery.css',
            [],
            DRF_GALLERY_VERSION
        );

        wp_enqueue_script(
            'drf-gallery-admin-js',
            DRF_GALLERY_URL . 'assets/js/admin-gallery.js',
            ['jquery', 'jquery-ui-sortable'],
            DRF_GALLERY_VERSION,
            true
        );

        $album_id = isset($_GET['album_id']) ? absint($_GET['album_id']) : 0;

        wp_localize_script('drf-gallery-admin-js', 'drfGalleryAdmin', [
            'ajaxUrl'   => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('drf_gallery_admin_nonce'),
            'albumId'   => $album_id,
            'i18n'      => [
                'uploading'      => __('Wird hochgeladen...', 'drf-gallery'),
                'orderSaved'     => __('Reihenfolge gespeichert!', 'drf-gallery'),
                'confirmDelete'  => __('Bild wirklich aus dem Album entfernen?', 'drf-gallery'),
                'selectMedia'    => __('Bilder auswählen', 'drf-gallery'),
                'useSelected'    => __('Ausgewählte Bilder zum Album hinzufügen', 'drf-gallery'),
                'dropFilesHere'  => __('Dateien hier ablegen zum Hochladen', 'drf-gallery'),
            ],
        ]);
    }

    public function handle_album_actions() {
        if (!current_user_can('upload_files')) {
            return;
        }

        // 1. Save or Update Album Details
        if (isset($_POST['drf_save_album'])) {
            check_admin_referer('drf_save_album_nonce');
            $id = !empty($_POST['album_id']) ? absint($_POST['album_id']) : null;
            $title = sanitize_text_field($_POST['title'] ?? '');
            $desc = sanitize_textarea_field($_POST['description'] ?? '');
            $cover = absint($_POST['cover_image_id'] ?? 0);

            if (!empty($title)) {
                $saved_id = DRF_Gallery_DB::save_album($title, $desc, $cover, $id);
                wp_redirect(add_query_arg([
                    'page'     => 'drf-gallery',
                    'action'   => 'edit',
                    'album_id' => $saved_id,
                    'status'   => 'saved',
                ], admin_url('admin.php')));
                exit;
            }
        }

        // 2. Delete Album
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['album_id'])) {
            check_admin_referer('drf_delete_album_nonce');
            $id = absint($_GET['album_id']);
            DRF_Gallery_DB::delete_album($id);
            wp_redirect(add_query_arg([
                'page'   => 'drf-gallery',
                'status' => 'deleted',
            ], admin_url('admin.php')));
            exit;
        }
    }

    /**
     * AJAX: Drag & Drop File Upload into Album
     */
    public function ajax_upload_image() {
        check_ajax_referer('drf_gallery_admin_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'Keine Berechtigung']);
        }

        $album_id = absint($_POST['album_id'] ?? 0);
        if (!$album_id) {
            wp_send_json_error(['message' => 'Ungültiges Album']);
        }

        if (empty($_FILES['file'])) {
            wp_send_json_error(['message' => 'Keine Datei übermittelt']);
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $attachment_id = media_handle_upload('file', 0);
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => $attachment_id->get_error_message()]);
        }

        // Add to album
        $post = get_post($attachment_id);
        $caption = $post ? $post->post_title : '';
        $album_image_id = DRF_Gallery_DB::add_image_to_album($album_id, $attachment_id, $caption);

        $thumb_url = wp_get_attachment_image_url($attachment_id, 'medium');

        wp_send_json_success([
            'id'            => $album_image_id,
            'attachment_id' => $attachment_id,
            'thumb_url'     => $thumb_url,
            'caption'       => $caption,
            'title'         => $caption,
        ]);
    }

    /**
     * AJAX: Add Existing Media from WP Library
     */
    public function ajax_add_existing_media() {
        check_ajax_referer('drf_gallery_admin_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'Keine Berechtigung']);
        }

        $album_id = absint($_POST['album_id'] ?? 0);
        $attachment_ids = isset($_POST['attachment_ids']) ? array_map('absint', (array) $_POST['attachment_ids']) : [];

        if (!$album_id || empty($attachment_ids)) {
            wp_send_json_error(['message' => 'Ungültige Parameter']);
        }

        $added = [];
        foreach ($attachment_ids as $att_id) {
            $post = get_post($att_id);
            if (!$post) continue;
            $caption = $post->post_title;
            $album_img_id = DRF_Gallery_DB::add_image_to_album($album_id, $att_id, $caption);
            $added[] = [
                'id'            => $album_img_id,
                'attachment_id' => $att_id,
                'thumb_url'     => wp_get_attachment_image_url($att_id, 'medium'),
                'caption'       => $caption,
                'title'         => $caption,
            ];
        }

        wp_send_json_success(['added' => $added]);
    }

    /**
     * AJAX: Drag & Drop Reorder Album Images
     */
    public function ajax_reorder_images() {
        check_ajax_referer('drf_gallery_admin_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'Keine Berechtigung']);
        }

        $album_id = absint($_POST['album_id'] ?? 0);
        $order = isset($_POST['order']) ? array_map('absint', (array) $_POST['order']) : [];

        if (!$album_id || empty($order)) {
            wp_send_json_error(['message' => 'Ungültige Reihenfolge']);
        }

        DRF_Gallery_DB::update_images_order($album_id, $order);
        wp_send_json_success(['message' => 'Reihenfolge gespeichert']);
    }

    /**
     * AJAX: Remove Image from Album
     */
    public function ajax_remove_image() {
        check_ajax_referer('drf_gallery_admin_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'Keine Berechtigung']);
        }

        $image_id = absint($_POST['image_id'] ?? 0);
        if (!$image_id) {
            wp_send_json_error(['message' => 'Ungültiges Bild']);
        }

        DRF_Gallery_DB::remove_image_from_album($image_id);
        wp_send_json_success(['message' => 'Bild entfernt']);
    }

    /**
     * AJAX: Update Image Caption
     */
    public function ajax_update_caption() {
        check_ajax_referer('drf_gallery_admin_nonce', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'Keine Berechtigung']);
        }

        $image_id = absint($_POST['image_id'] ?? 0);
        $caption = sanitize_text_field($_POST['caption'] ?? '');

        DRF_Gallery_DB::update_caption($image_id, $caption);
        wp_send_json_success(['message' => 'Bildunterschrift gespeichert']);
    }

    /**
     * Render Admin Main Page (List or Edit)
     */
    public function render_admin_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $album_id = isset($_GET['album_id']) ? absint($_GET['album_id']) : 0;

        if ($action === 'edit' && $album_id) {
            $this->render_album_editor($album_id);
        } else {
            $this->render_albums_list();
        }
    }

    public function render_new_album_page() {
        $this->render_album_editor(0);
    }

    /**
     * View: Albums Overview List
     */
    private function render_albums_list() {
        $albums = DRF_Gallery_DB::get_all_albums();
        ?>
        <div class="wrap drf-gallery-admin-wrap">
            <header class="drf-gallery-header">
                <div class="drf-header-brand">
                    <span class="dashicons dashicons-format-gallery drf-logo-icon"></span>
                    <div>
                        <h1 class="drf-header-title">Moderne Foto-Alben &amp; Galerien</h1>
                        <p class="drf-header-subtitle">Verwalte Fotostrecken von den Jahrestreffen (z.B. Neuendettelsau 2026/2027), Workshops und Dunkelkammer-Sessions.</p>
                    </div>
                </div>
                <div class="drf-header-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=drf-gallery-new')); ?>" class="button button-primary drf-btn-gold">
                        <span class="dashicons dashicons-plus-alt2" style="margin-top:4px;"></span> Neues Album erstellen
                    </a>
                </div>
            </header>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted') : ?>
                <div class="notice notice-success is-dismissible"><p>Album erfolgreich gelöscht.</p></div>
            <?php endif; ?>

            <div class="drf-albums-grid">
                <?php if (empty($albums)) : ?>
                    <div class="drf-empty-box">
                        <span class="empty-icon">📷</span>
                        <h3>Noch keine Alben vorhanden</h3>
                        <p>Erstelle dein erstes Fotoalbum, um Bilder per Drag &amp; Drop hochzuladen und zu sortieren.</p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=drf-gallery-new')); ?>" class="button button-primary drf-btn-gold">
                            Jetzt erstes Album erstellen
                        </a>
                    </div>
                <?php else : ?>
                    <?php foreach ($albums as $album) :
                        $edit_url = add_query_arg(['page' => 'drf-gallery', 'action' => 'edit', 'album_id' => $album->id], admin_url('admin.php'));
                        $delete_url = wp_nonce_url(add_query_arg(['page' => 'drf-gallery', 'action' => 'delete', 'album_id' => $album->id], admin_url('admin.php')), 'drf_delete_album_nonce');
                        $cover = !empty($album->cover_url) ? $album->cover_url : DRF_GALLERY_URL . 'assets/images/placeholder.svg';
                        ?>
                        <div class="drf-album-card">
                            <div class="album-cover-wrap">
                                <?php if (!empty($album->cover_url)) : ?>
                                    <img src="<?php echo esc_url($album->cover_url); ?>" alt="<?php echo esc_attr($album->title); ?>" class="album-cover-img">
                                <?php else : ?>
                                    <div class="album-cover-placeholder"><span>📷 Kein Cover</span></div>
                                <?php endif; ?>
                                <span class="album-badge-count">
                                    <?php echo sprintf(_n('%d Foto', '%d Fotos', $album->image_count, 'drf-gallery'), $album->image_count); ?>
                                </span>
                            </div>

                            <div class="album-card-content">
                                <h3 class="album-title">
                                    <a href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html($album->title); ?></a>
                                </h3>
                                <?php if (!empty($album->description)) : ?>
                                    <p class="album-desc"><?php echo wp_trim_words(esc_html($album->description), 14); ?></p>
                                <?php endif; ?>

                                <div class="album-shortcode-box">
                                    <code>[drf_album id="<?php echo esc_attr($album->id); ?>"]</code>
                                    <button type="button" class="drf-btn-copy" data-copy='[drf_album id="<?php echo esc_attr($album->id); ?>"]' title="Shortcode kopieren">Kopieren</button>
                                </div>

                                <div class="album-card-footer">
                                    <a href="<?php echo esc_url($edit_url); ?>" class="button button-primary drf-btn-manage">
                                        <span class="dashicons dashicons-images-alt2" style="margin-top:3px;"></span> Bilder verwalten
                                    </a>
                                    <a href="<?php echo esc_url($delete_url); ?>" class="button button-link-delete" onclick="return confirm('Album und Bildverknüpfungen wirklich löschen? Die Bilder bleiben in der Mediathek erhalten.');">
                                        Löschen
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * View: Album Editor with Drag & Drop Upload & Sorting
     */
    private function render_album_editor($album_id) {
        $album = $album_id ? DRF_Gallery_DB::get_album($album_id) : null;
        $images = $album_id ? DRF_Gallery_DB::get_album_images($album_id) : [];
        $is_new = empty($album);
        ?>
        <div class="wrap drf-gallery-admin-wrap">
            <div class="drf-breadcrumb">
                <a href="<?php echo esc_url(admin_url('admin.php?page=drf-gallery')); ?>">&larr; Zurück zu allen Alben</a>
            </div>

            <header class="drf-gallery-header">
                <div class="drf-header-brand">
                    <span class="dashicons dashicons-images-alt2 drf-logo-icon"></span>
                    <div>
                        <h1 class="drf-header-title">
                            <?php echo $is_new ? 'Neues Foto-Album anlegen' : 'Album bearbeiten: ' . esc_html($album->title); ?>
                        </h1>
                        <p class="drf-header-subtitle">Lade neue Fotos per Drag &amp; Drop hoch und sortiere die Reihenfolge bequem per Maus.</p>
                    </div>
                </div>
            </header>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'saved') : ?>
                <div class="notice notice-success is-dismissible"><p>Album-Einstellungen erfolgreich gespeichert.</p></div>
            <?php endif; ?>

            <div class="drf-editor-layout">
                <!-- Left: Album Metadata Form -->
                <div class="drf-editor-sidebar">
                    <div class="drf-box">
                        <h3>Album-Details</h3>
                        <form method="post" action="">
                            <?php wp_nonce_field('drf_save_album_nonce'); ?>
                            <?php if (!$is_new) : ?>
                                <input type="hidden" name="album_id" value="<?php echo esc_attr($album->id); ?>">
                            <?php endif; ?>

                            <div class="drf-form-group">
                                <label for="album_title"><strong>Titel des Albums *</strong></label>
                                <input type="text" id="album_title" name="title" required value="<?php echo esc_attr($album->title ?? ''); ?>" class="widefat" placeholder="z.B. Augenblicke 2026 – Neuendettelsau">
                            </div>

                            <div class="drf-form-group">
                                <label for="album_description"><strong>Beschreibung</strong></label>
                                <textarea id="album_description" name="description" rows="3" class="widefat" placeholder="Optionale Notizen oder Thema der Fotostrecke..."><?php echo esc_textarea($album->description ?? ''); ?></textarea>
                            </div>

                            <div class="drf-form-group">
                                <label><strong>Cover-Bild</strong></label>
                                <input type="hidden" id="cover_image_id" name="cover_image_id" value="<?php echo esc_attr($album->cover_image_id ?? 0); ?>">
                                <div id="cover_preview" class="cover-preview-box">
                                    <?php if (!empty($album->cover_url)) : ?>
                                        <img src="<?php echo esc_url($album->cover_url); ?>" alt="Cover" style="max-width:100%; height:auto; border-radius:6px;">
                                    <?php else : ?>
                                        <span style="color:#64748b; font-size:0.85rem;">Standard: Erstes Bild des Albums</span>
                                    <?php endif; ?>
                                </div>
                                <button type="button" id="btn_choose_cover" class="button button-secondary" style="margin-top:6px;">
                                    Cover aus Mediathek wählen
                                </button>
                            </div>

                            <p class="submit" style="margin-bottom:0; padding-bottom:0;">
                                <button type="submit" name="drf_save_album" class="button button-primary drf-btn-gold">
                                    <?php echo $is_new ? 'Album anlegen & Fotos hochladen' : 'Details speichern'; ?>
                                </button>
                            </p>
                        </form>
                    </div>

                    <?php if (!$is_new) : ?>
                        <div class="drf-box" style="margin-top:20px;">
                            <h3>Shortcode für dieses Album</h3>
                            <p style="font-size:0.85rem; color:#64748b; margin-bottom:10px;">Füge diesen Shortcode in jeden Beitrag, jede Seite oder Elementor-Textfeld ein:</p>
                            <div class="album-shortcode-box">
                                <code>[drf_album id="<?php echo esc_attr($album->id); ?>"]</code>
                                <button type="button" class="drf-btn-copy" data-copy='[drf_album id="<?php echo esc_attr($album->id); ?>"]'>Kopieren</button>
                            </div>
                            <div style="margin-top:12px; font-size:0.82rem; color:#64748b;">
                                <strong>Tipp:</strong> Du kannst auch <code>layout="polaroid"</code> oder <code>layout="masonry"</code> ergänzen:
                                <code>[drf_album id="<?php echo esc_attr($album->id); ?>" layout="polaroid"]</code>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Drag & Drop Dropzone & Sortable Images Grid -->
                <div class="drf-editor-main">
                    <?php if ($is_new) : ?>
                        <div class="drf-box" style="text-align:center; padding:40px;">
                            <span style="font-size:3rem; display:block; margin-bottom:12px;">💡</span>
                            <h3>Speichere zuerst die Album-Details links ab</h3>
                            <p style="color:#64748b;">Sobald das Album angelegt ist, kannst du hier per Drag &amp; Drop beliebig viele Bilder hochladen und sortieren.</p>
                        </div>
                    <?php else : ?>
                        <!-- 1. DRAG & DROP UPLOAD DROPZONE -->
                        <div class="drf-dropzone-box" id="drfDropzone">
                            <div class="dropzone-inner">
                                <span class="dropzone-icon">📥</span>
                                <h3>Fotos hierher ziehen zum Hochladen</h3>
                                <p>Dateien einfach per Drag &amp; Drop hier ablegen (JPG, PNG, WebP) oder:</p>
                                <div class="dropzone-buttons">
                                    <label class="button button-primary drf-btn-gold dropzone-browse-label">
                                        <span class="dashicons dashicons-upload" style="margin-top:4px;"></span> Vom Computer wählen
                                        <input type="file" id="drfFileInput" multiple accept="image/*" style="display:none;">
                                    </label>
                                    <button type="button" id="drfBtnMediaLibrary" class="button button-secondary">
                                        <span class="dashicons dashicons-admin-media" style="margin-top:4px;"></span> Aus WordPress-Mediathek wählen
                                    </button>
                                </div>
                                <div id="drfUploadProgress" class="drf-progress-wrap" style="display:none;">
                                    <div class="drf-progress-bar"><div class="drf-progress-fill" id="drfProgressFill"></div></div>
                                    <span class="drf-progress-text" id="drfProgressText">Wird hochgeladen...</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. SORTABLE DRAG & DROP IMAGES GRID -->
                        <div class="drf-images-section">
                            <div class="section-header">
                                <div>
                                    <h3>Fotos im Album (<span id="drfImageCount"><?php echo count($images); ?></span>)</h3>
                                    <p class="subtitle">Bilder mit der Maus verschieben, um die Reihenfolge zu ändern. Änderungen werden sofort gespeichert.</p>
                                </div>
                                <div id="drfSortStatus" class="sort-status-pill" style="display:none;">
                                    ✓ Reihenfolge gespeichert
                                </div>
                            </div>

                            <div class="drf-sortable-grid" id="drfSortableGrid">
                                <?php if (!empty($images)) : ?>
                                    <?php foreach ($images as $img) : ?>
                                        <div class="drf-image-tile" data-id="<?php echo esc_attr($img->id); ?>" data-attachment-id="<?php echo esc_attr($img->attachment_id); ?>">
                                            <div class="tile-handle" title="Ziehen zum Sortieren">
                                                <span class="dashicons dashicons-move"></span>
                                            </div>
                                            <div class="tile-thumb-wrap">
                                                <img src="<?php echo esc_url($img->thumb_url); ?>" alt="<?php echo esc_attr($img->caption); ?>" loading="lazy">
                                            </div>
                                            <div class="tile-info">
                                                <input type="text" class="tile-caption-input" value="<?php echo esc_attr($img->caption); ?>" placeholder="Bildunterschrift..." title="Klicken zum Bearbeiten">
                                            </div>
                                            <button type="button" class="tile-btn-delete" title="Aus Album entfernen">
                                                &times;
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div id="drfNoImagesNotice" class="drf-no-images" style="<?php echo empty($images) ? 'display:block;' : 'display:none;'; ?>">
                                <span style="font-size:2rem; display:block; margin-bottom:8px;">🖼️</span>
                                <strong>Noch keine Fotos in diesem Album.</strong>
                                <p style="margin:4px 0 0 0; color:#64748b;">Ziehe Bilder in den oberen Bereich, um sie direkt hochzuladen.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
