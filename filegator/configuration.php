<?php

$drf_head = <<<'HTML'
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="alternate icon" type="image/x-icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<meta name="theme-color" content="#141518">
HTML;

$drf_body = <<<'HTML'
<link rel="stylesheet" href="/dateien/drf-theme.css?v=3">
<script>
(function() {
  function injectDrfFilebar() {
    if (document.getElementById('drfFilegatorBar')) return;
    var bar = document.createElement('header');
    bar.id = 'drfFilegatorBar';
    bar.className = 'drf-fg-topbar';
    bar.innerHTML =
      '<div class="drf-fg-topbar-inner">' +
        '<a href="/dateien/" class="drf-fg-brand">' +
          '<img src="/wp-content/themes/drf-augenblicke/assets/images/drf-logo.svg" alt="d.r.f Logo">' +
          '<div>' +
            '<span class="drf-fg-brand-title">AUGENBLICKE</span>' +
            '<span class="drf-fg-brand-badge">Dateiablage</span>' +
          '</div>' +
        '</a>' +
        '<div class="drf-fg-actions">' +
          '<a href="/forum/" class="drf-fg-btn drf-fg-btn-forum">← Zurück zum Forum</a>' +
          '<a href="/" class="drf-fg-btn drf-fg-btn-home">Homepage</a>' +
        '</div>' +
      '</div>';
    document.body.insertBefore(bar, document.body.firstChild);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectDrfFilebar);
  } else {
    injectDrfFilebar();
  }
})();
</script>
<script src="/dateien/drf-icons.js?v=3"></script>
HTML;

return [
    'public_path' => APP_PUBLIC_PATH,
    'public_dir' => APP_PUBLIC_DIR,
    'overwrite_on_upload' => false,
    'timezone' => 'Europe/Berlin',
    'download_inline' => ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'],
    'lockout_attempts' => 5,
    'lockout_timeout' => 15,

    'frontend_config' => [
        'app_name' => 'd.r.f Dateiablage',
        'app_version' => APP_VERSION,
        'language' => 'german',
        'logo' => '/wp-content/themes/drf-augenblicke/assets/images/drf-logo.svg',
        'upload_max_size' => 100 * 1024 * 1024, // 100MB
        'upload_chunk_size' => 1 * 1024 * 1024, // 1MB
        'upload_simultaneous' => 3,
        'default_archive_name' => 'drf-dateien.zip',
        'editable' => ['.txt', '.md', '.csv', '.json', '.html', '.css', '.js', '.xml', '.yml', '.yaml'],
        'date_format' => 'DD.MM.YYYY HH:mm',
        'guest_redirection' => '',
        'search_simultaneous' => 5,
        'filter_entries' => [],
        'pagination' => ['', 10, 25, 50],
    ],

    'services' => [
        'Filegator\Services\Logger\LoggerInterface' => [
            'handler' => '\Filegator\Services\Logger\Adapters\MonoLogger',
            'config' => [
                'monolog_handlers' => [
                    function () {
                        return new \Monolog\Handler\StreamHandler(
                            __DIR__ . '/private/logs/app.log',
                            \Monolog\Logger::DEBUG
                        );
                    },
                ],
            ],
        ],
        'Filegator\Services\Session\SessionStorageInterface' => [
            'handler' => '\Filegator\Services\Session\Adapters\SessionStorage',
            'config' => [
                'handler' => function () {
                    $save_path = __DIR__ . '/private/sessions';
                    $handler = new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler($save_path);

                    return new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage([
                        'cookie_samesite' => 'Lax',
                        'cookie_secure' => null,
                        'cookie_httponly' => true,
                    ], $handler);
                },
            ],
        ],
        'Filegator\Services\Cors\Cors' => [
            'handler' => '\Filegator\Services\Cors\Cors',
            'config' => [
                'enabled' => APP_ENV == 'production' ? false : true,
            ],
        ],
        'Filegator\Services\Tmpfs\TmpfsInterface' => [
            'handler' => '\Filegator\Services\Tmpfs\Adapters\Tmpfs',
            'config' => [
                'path' => __DIR__ . '/private/tmp/',
                'gc_probability_perc' => 10,
                'gc_older_than' => 60 * 60 * 24 * 2,
            ],
        ],
        'Filegator\Services\Security\Security' => [
            'handler' => '\Filegator\Services\Security\Security',
            'config' => [
                'csrf_protection' => true,
                'csrf_key' => 'drf_augenblicke_fg_2026_secure_key',
                'ip_allowlist' => [],
                'ip_denylist' => [],
                'allow_insecure_overlays' => false,
            ],
        ],
        'Filegator\Services\View\ViewInterface' => [
            'handler' => '\Filegator\Services\View\Adapters\Vuejs',
            'config' => [
                'add_to_head' => $drf_head,
                'add_to_body' => $drf_body,
            ],
        ],
        'Filegator\Services\Storage\Filesystem' => [
            'handler' => '\Filegator\Services\Storage\Filesystem',
            'config' => [
                'separator' => '/',
                'config' => [],
                'adapter' => function () {
                    return new \League\Flysystem\Adapter\Local(
                        __DIR__ . '/repository'
                    );
                },
            ],
        ],
        'Filegator\Services\Archiver\ArchiverInterface' => [
            'handler' => '\Filegator\Services\Archiver\Adapters\ZipArchiver',
            'config' => [],
        ],
        'Filegator\Services\Auth\AuthInterface' => [
            'handler' => '\Filegator\Services\Auth\Adapters\JsonFile',
            'config' => [
                'file' => __DIR__ . '/private/users.json',
            ],
        ],
        'Filegator\Services\Router\Router' => [
            'handler' => '\Filegator\Services\Router\Router',
            'config' => [
                'query_param' => 'r',
                'routes_file' => __DIR__ . '/backend/Controllers/routes.php',
            ],
        ],
    ],
];
