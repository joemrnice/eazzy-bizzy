<?php
/**
 * Helper Functions
 * Utility functions used throughout the application
 */

/**
 * Hash a password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify a password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate a random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Generate a slug from string
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $errors = [];
    
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[$field] = ucfirst($field) . ' is required';
        }
    }
    
    return $errors;
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    $config = require __DIR__ . '/../../config/app.php';
    return $config['currency']['symbol'] . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get pagination data
 */
function paginate($total, $perPage, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_next' => $currentPage < $totalPages,
        'has_prev' => $currentPage > 1,
    ];
}

/**
 * Upload and resize image
 */
function uploadImage($file, $targetDir, $maxWidth = 800, $maxHeight = 800) {
    $config = require __DIR__ . '/../../config/app.php';
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload failed'];
    }
    
    if ($file['size'] > $config['upload']['max_size']) {
        return ['error' => 'File too large'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $config['upload']['allowed_types'])) {
        return ['error' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $targetPath = $targetDir . $filename;
    
    // Create directory if not exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['error' => 'Failed to save file'];
    }
    
    // Resize image
    resizeImage($targetPath, $maxWidth, $maxHeight);
    
    return ['success' => true, 'filename' => $filename, 'path' => $targetPath];
}

/**
 * Resize image
 */
function resizeImage($path, $maxWidth, $maxHeight) {
    list($width, $height, $type) = getimagesize($path);
    
    if ($width <= $maxWidth && $height <= $maxHeight) {
        return;
    }
    
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = (int)($width * $ratio);
    $newHeight = (int)($height * $ratio);
    
    // Create image from file
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($path);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($path);
            break;
        default:
            return;
    }
    
    // Create resized image
    $dest = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagecolortransparent($dest, imagecolorallocatealpha($dest, 0, 0, 0, 127));
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
    }
    
    imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save resized image
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dest, $path, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($dest, $path, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($dest, $path);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($dest, $path, 90);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($dest);
}

/**
 * Send email
 */
function sendEmail($to, $subject, $body, $isHtml = true) {
    $config = require __DIR__ . '/../../config/app.php';
    $mail = $config['mail'];
    
    $headers = [
        'From: ' . $mail['from_name'] . ' <' . $mail['from_address'] . '>',
        'Reply-To: ' . $mail['from_address'],
        'X-Mailer: PHP/' . phpversion(),
    ];
    
    if ($isHtml) {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
    }
    
    return mail($to, $subject, $body, implode("\r\n", $headers));
}

/**
 * Get email template
 */
function getEmailTemplate($content) {
    $config = require __DIR__ . '/../../config/app.php';
    
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0ea5e9; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; background: #f8fafc; }
        .footer { text-align: center; padding: 20px; color: #64748b; font-size: 12px; }
        .button { display: inline-block; padding: 12px 30px; background: #f97316; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$config['name']}</h1>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p>&copy; 2024 {$config['name']}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken(32);
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * JSON response helper
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Success response
 */
function successResponse($data = [], $message = 'Success', $statusCode = 200) {
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data,
    ], $statusCode);
}

/**
 * Error response
 */
function errorResponse($message = 'Error', $errors = [], $statusCode = 400) {
    jsonResponse([
        'success' => false,
        'message' => $message,
        'errors' => $errors,
    ], $statusCode);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

/**
 * Get current user
 */
function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    require_once __DIR__ . '/../models/User.php';
    $userModel = new User();
    return $userModel->findById($_SESSION['user_id']);
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /auth');
        exit;
    }
}

/**
 * Require admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /admin/login');
        exit;
    }
}
