-- Create both databases if not exist
CREATE DATABASE IF NOT EXISTS wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS flarum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant permissions to drf_user
GRANT ALL PRIVILEGES ON wordpress.* TO 'drf_user'@'%';
GRANT ALL PRIVILEGES ON flarum.* TO 'drf_user'@'%';
FLUSH PRIVILEGES;
