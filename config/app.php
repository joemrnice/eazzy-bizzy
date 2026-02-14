<?php
/**
 * Application Configuration
 * Global constants and settings
 */

// Load environment
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/../.env');

return [
    // Application
    'name' => $_ENV['APP_NAME'] ?? 'EazzyBizzy',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    
    // Security
    'key' => $_ENV['APP_KEY'] ?? 'change-this-secret-key',
    'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? '_token',
    'session_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
    
    // Email
    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? '',
        'port' => (int)($_ENV['MAIL_PORT'] ?? 587),
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@eazzybizzy.com',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'EazzyBizzy',
    ],
    
    // Uploads
    'upload' => [
        'max_size' => (int)($_ENV['UPLOAD_MAX_SIZE'] ?? 5242880), // 5MB
        'allowed_types' => explode(',', $_ENV['UPLOAD_ALLOWED_TYPES'] ?? 'jpg,jpeg,png,gif,webp'),
        'path' => __DIR__ . '/../assets/images/uploads/',
        'url' => '/assets/images/uploads/',
    ],
    
    // Pagination
    'pagination' => [
        'products_per_page' => (int)($_ENV['PRODUCTS_PER_PAGE'] ?? 12),
        'orders_per_page' => (int)($_ENV['ORDERS_PER_PAGE'] ?? 10),
        'reviews_per_page' => (int)($_ENV['REVIEWS_PER_PAGE'] ?? 10),
    ],
    
    // Currency
    'currency' => [
        'symbol' => $_ENV['CURRENCY_SYMBOL'] ?? '$',
        'code' => $_ENV['CURRENCY_CODE'] ?? 'USD',
    ],
    
    // Paths
    'paths' => [
        'root' => __DIR__ . '/../',
        'app' => __DIR__ . '/../app/',
        'public' => __DIR__ . '/../public/',
        'assets' => __DIR__ . '/../assets/',
        'config' => __DIR__ . '/../config/',
    ],
];
