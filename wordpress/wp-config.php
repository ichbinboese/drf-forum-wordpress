<?php
define('DB_NAME', 'wordpress');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

define('AUTH_KEY',         'drf-salt-key-83921890312890312890');
define('SECURE_AUTH_KEY',  'drf-salt-key-83921890312890312891');
define('LOGGED_IN_KEY',    'drf-salt-key-83921890312890312892');
define('NONCE_KEY',        'drf-salt-key-83921890312890312893');
define('AUTH_SALT',        'drf-salt-key-83921890312890312894');
define('SECURE_AUTH_SALT', 'drf-salt-key-83921890312890312895');
define('LOGGED_IN_SALT',   'drf-salt-key-83921890312890312896');
define('NONCE_SALT',       'drf-salt-key-83921890312890312897');

$table_prefix = 'wp_';

define('WP_DEBUG', false);
define('WP_HOME', 'http://localhost:8080');
define('WP_SITEURL', 'http://localhost:8080');

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
