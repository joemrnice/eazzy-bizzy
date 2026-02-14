<?php
/**
 * Middleware Functions
 */

require_once __DIR__ . '/../helpers/functions.php';

/**
 * Initialize session if not started
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Authentication middleware
 */
function authMiddleware() {
    initSession();
    
    if (!isset($_SESSION['user_id'])) {
        errorResponse('Unauthorized', [], 401);
    }
}

/**
 * Admin authentication middleware
 */
function adminMiddleware() {
    initSession();
    
    if (!isset($_SESSION['admin_id'])) {
        errorResponse('Unauthorized - Admin access required', [], 401);
    }
}

/**
 * CSRF middleware
 */
function csrfMiddleware() {
    initSession();
    
    $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    
    if (!$token || !verifyCsrfToken($token)) {
        errorResponse('Invalid CSRF token', [], 403);
    }
}

/**
 * Rate limiter middleware
 */
function rateLimitMiddleware($key, $maxAttempts = 5, $decayMinutes = 1) {
    initSession();
    
    $rateLimitKey = 'rate_limit_' . $key . '_' . ($_SESSION['user_id'] ?? $_SERVER['REMOTE_ADDR']);
    
    if (!isset($_SESSION[$rateLimitKey])) {
        $_SESSION[$rateLimitKey] = [
            'attempts' => 0,
            'reset_at' => time() + ($decayMinutes * 60)
        ];
    }
    
    $rateLimit = $_SESSION[$rateLimitKey];
    
    // Reset if time expired
    if (time() > $rateLimit['reset_at']) {
        $_SESSION[$rateLimitKey] = [
            'attempts' => 1,
            'reset_at' => time() + ($decayMinutes * 60)
        ];
        return;
    }
    
    // Check if exceeded
    if ($rateLimit['attempts'] >= $maxAttempts) {
        $retryAfter = $rateLimit['reset_at'] - time();
        errorResponse('Too many requests. Try again in ' . $retryAfter . ' seconds', [], 429);
    }
    
    // Increment attempts
    $_SESSION[$rateLimitKey]['attempts']++;
}

/**
 * CORS middleware (for API endpoints)
 */
function corsMiddleware() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

/**
 * JSON middleware - ensure request is JSON
 */
function jsonMiddleware() {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') === false && $_SERVER['REQUEST_METHOD'] !== 'GET') {
        errorResponse('Content-Type must be application/json', [], 400);
    }
}

/**
 * Sanitize input middleware
 */
function sanitizeInputMiddleware() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if ($data !== null) {
            $_POST = array_merge($_POST, $data);
        }
    }
}
