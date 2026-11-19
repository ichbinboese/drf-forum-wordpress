<?php
/**
 * DRF Gallery Database Handler
 *
 * @package DRF_Gallery
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Gallery_DB {

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // 1. Albums Table
        $table_albums = $wpdb->prefix . 'drf_albums';
        $sql_albums = "CREATE TABLE $table_albums (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            slug varchar(100) NOT NULL,
            description text NULL,
            cover_image_id bigint(20) DEFAULT 0 NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql_albums);

        // 2. Album Images Table
        $table_images = $wpdb->prefix . 'drf_album_images';
        $sql_images = "CREATE TABLE $table_images (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            album_id mediumint(9) NOT NULL,
            attachment_id bigint(20) NOT NULL,
            sort_order int(11) DEFAULT 0 NOT NULL,
            caption text NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY album_id (album_id),
            KEY attachment_id (attachment_id)
        ) $charset_collate;";
        dbDelta($sql_images);

        // Seed initial albums if none exist
        self::seed_initial_albums();
    }

    public static function get_all_albums() {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_albums';
        $albums = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

        // Attach image counts and cover URLs
        $table_images = $wpdb->prefix . 'drf_album_images';
        foreach ($albums as $a) {
            $a->image_count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_images WHERE album_id = %d", $a->id));
            if ($a->cover_image_id > 0) {
                $a->cover_url = wp_get_attachment_image_url($a->cover_image_id, 'medium');
            } else {
                // Try first image in album as fallback cover
                $first_img = $wpdb->get_var($wpdb->prepare("SELECT attachment_id FROM $table_images WHERE album_id = %d ORDER BY sort_order ASC, id ASC LIMIT 1", $a->id));
                $a->cover_url = $first_img ? wp_get_attachment_image_url($first_img, 'medium') : '';
            }
        }

        return $albums;
    }

    public static function get_album($id_or_slug) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_albums';
        if (is_numeric($id_or_slug)) {
            $album = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", absint($id_or_slug)));
        } else {
            $album = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE slug = %s", sanitize_title($id_or_slug)));
        }

        if ($album) {
            if ($album->cover_image_id > 0) {
                $album->cover_url = wp_get_attachment_image_url($album->cover_image_id, 'medium');
            } else {
                $table_images = $wpdb->prefix . 'drf_album_images';
                $first_img = $wpdb->get_var($wpdb->prepare("SELECT attachment_id FROM $table_images WHERE album_id = %d ORDER BY sort_order ASC, id ASC LIMIT 1", $album->id));
                $album->cover_url = $first_img ? wp_get_attachment_image_url($first_img, 'medium') : '';
            }
        }

        return $album;
    }

    public static function save_album($title, $description = '', $cover_image_id = 0, $id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_albums';

        $title = sanitize_text_field($title);
        $slug = sanitize_title($title);
        $description = sanitize_textarea_field($description);
        $cover_image_id = absint($cover_image_id);

        if ($id) {
            $wpdb->update(
                $table,
                [
                    'title'          => $title,
                    'slug'           => $slug,
                    'description'    => $description,
                    'cover_image_id' => $cover_image_id,
                    'updated_at'     => current_time('mysql'),
                ],
                ['id' => absint($id)]
            );
            return $id;
        } else {
            // Check unique slug
            $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE slug = %s", $slug));
            if ($existing) {
                $slug .= '-' . time();
            }

            $wpdb->insert(
                $table,
                [
                    'title'          => $title,
                    'slug'           => $slug,
                    'description'    => $description,
                    'cover_image_id' => $cover_image_id,
                    'created_at'     => current_time('mysql'),
                    'updated_at'     => current_time('mysql'),
                ]
            );
            return $wpdb->insert_id;
        }
    }

    public static function delete_album($id) {
        global $wpdb;
        $id = absint($id);
        $wpdb->delete($wpdb->prefix . 'drf_albums', ['id' => $id]);
        $wpdb->delete($wpdb->prefix . 'drf_album_images', ['album_id' => $id]);
    }

    public static function get_album_images($album_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_album_images';
        $images = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE album_id = %d ORDER BY sort_order ASC, id ASC",
            absint($album_id)
        ));

        $output = [];
        foreach ($images as $img) {
            $attachment = get_post($img->attachment_id);
            if (!$attachment) {
                continue;
            }

            $thumb = wp_get_attachment_image_url($img->attachment_id, 'thumbnail');
            $medium = wp_get_attachment_image_url($img->attachment_id, 'medium');
            $large = wp_get_attachment_image_url($img->attachment_id, 'large');
            $full = wp_get_attachment_image_url($img->attachment_id, 'full');
            $meta = wp_get_attachment_metadata($img->attachment_id);

            $caption = !empty($img->caption) ? $img->caption : wp_get_attachment_caption($img->attachment_id);
            if (empty($caption)) {
                $caption = $attachment->post_title;
            }

            $output[] = (object) [
                'id'            => (int) $img->id,
                'album_id'      => (int) $img->album_id,
                'attachment_id' => (int) $img->attachment_id,
                'sort_order'    => (int) $img->sort_order,
                'title'         => $attachment->post_title,
                'caption'       => $caption,
                'thumb_url'     => $thumb ?: $full,
                'medium_url'    => $medium ?: $full,
                'large_url'     => $large ?: $full,
                'full_url'      => $full,
                'width'         => $meta['width'] ?? 1200,
                'height'        => $meta['height'] ?? 800,
            ];
        }

        return $output;
    }

    public static function add_image_to_album($album_id, $attachment_id, $caption = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_album_images';

        // Check if already in album
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE album_id = %d AND attachment_id = %d",
            absint($album_id),
            absint($attachment_id)
        ));
        if ($existing) {
            return $existing;
        }

        // Get max sort order
        $max_order = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(sort_order) FROM $table WHERE album_id = %d",
            absint($album_id)
        ));

        $wpdb->insert(
            $table,
            [
                'album_id'      => absint($album_id),
                'attachment_id' => absint($attachment_id),
                'sort_order'    => $max_order + 1,
                'caption'       => sanitize_text_field($caption),
                'created_at'    => current_time('mysql'),
            ]
        );

        return $wpdb->insert_id;
    }

    public static function remove_image_from_album($image_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_album_images';
        return $wpdb->delete($table, ['id' => absint($image_id)]);
    }

    public static function update_images_order($album_id, array $ordered_ids) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_album_images';

        foreach ($ordered_ids as $order => $image_id) {
            $wpdb->update(
                $table,
                ['sort_order' => (int) $order],
                [
                    'id'       => absint($image_id),
                    'album_id' => absint($album_id),
                ]
            );
        }
        return true;
    }

    public static function update_caption($image_id, $caption) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_album_images';
        return $wpdb->update(
            $table,
            ['caption' => sanitize_text_field($caption)],
            ['id' => absint($image_id)]
        );
    }

    /**
     * Seed initial albums and attach theme photos to demonstrate galleries
     */
    private static function seed_initial_albums() {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_albums';
        $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        // 1. Create Starter Albums
        $a1_id = self::save_album(
            'Augenblicke XXV (2024) – Jubiläum',
            'Das große 25. Jubiläumstreffen im Haus Lutherrose Neuendettelsau mit Bildprojektionen und Available Light.'
        );

        $a2_id = self::save_album(
            'Workshops & Dunkelkammer',
            'Impressionen aus den Praxis-Sessions: Studioblitz-Setups, Altglas an Spiegellos und SW-Laborchemie.'
        );

        $a3_id = self::save_album(
            'Tagungsort & Umgebung',
            'Haus Lutherrose, Parkanlagen und Impressionen rund um Neuendettelsau.'
        );

        // 2. Import existing theme photos into media library if not present and attach
        $theme_dir = get_template_directory() . '/assets/images/';
        $sample_files = [
            'hero-bg.jpg'           => ['title' => 'Fotowalk in der Abendsonne', 'album' => $a1_id],
            'polaroid-dock.jpg'     => ['title' => 'Available Light am See', 'album' => $a1_id],
            'polaroid-camera.jpg'   => ['title' => 'Klassische Kameratechnik', 'album' => $a2_id],
            'card-workshops.jpg'    => ['title' => 'Studioblitz-Session', 'album' => $a2_id],
            'card-verein.jpg'       => ['title' => 'Gemeinschaft & Austausch', 'album' => $a1_id],
            'card-tagungsbuero.jpg' => ['title' => 'Haus Lutherrose Rezeption', 'album' => $a3_id],
            'card-shuttle.jpg'      => ['title' => 'Bahnhofs-Transfer', 'album' => $a3_id],
        ];

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $upload_dir = wp_upload_dir();

        foreach ($sample_files as $filename => $info) {
            $src_path = $theme_dir . $filename;
            if (!file_exists($src_path)) {
                continue;
            }

            // Check if already in media library
            $existing_att = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_title = %s LIMIT 1",
                $info['title']
            ));

            if (!$existing_att) {
                // Copy to upload directory
                $target_path = $upload_dir['path'] . '/' . $filename;
                @copy($src_path, $target_path);

                $filetype = wp_check_filetype(basename($target_path), null);
                $attachment = [
                    'guid'           => $upload_dir['url'] . '/' . basename($target_path),
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => $info['title'],
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                ];
                $att_id = wp_insert_attachment($attachment, $target_path);
                if (!is_wp_error($att_id)) {
                    $attach_data = wp_generate_attachment_metadata($att_id, $target_path);
                    wp_update_attachment_metadata($att_id, $attach_data);
                    self::add_image_to_album($info['album'], $att_id, $info['title']);
                }
            } else {
                self::add_image_to_album($info['album'], $existing_att, $info['title']);
            }
        }
    }
}
