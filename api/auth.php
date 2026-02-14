<?php
/**
 * Authentication API
 * Handles registration, login, logout, password reset
 */

session_start();

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/middleware/auth.php';
require_once __DIR__ . '/../app/models/User.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$userModel = new User();

try {
    switch ($action) {
        case 'register':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['name', 'email', 'password'];
            $errors = validateRequired($data, $required);
            
            if (!empty($errors)) {
                errorResponse('Validation failed', $errors, 422);
            }
            
            // Validate email
            if (!validateEmail($data['email'])) {
                errorResponse('Invalid email address', [], 422);
            }
            
            // Check if email already exists
            if ($userModel->findByEmail($data['email'])) {
                errorResponse('Email already registered', [], 422);
            }
            
            // Create user
            $userId = $userModel->create([
                'name' => sanitize($data['name']),
                'email' => sanitize($data['email']),
                'password' => $data['password'],
                'phone' => sanitize($data['phone'] ?? null),
            ]);
            
            $user = $userModel->findById($userId);
            
            // Send verification email
            $verificationLink = $_ENV['APP_URL'] . '/verify?token=' . $user['verification_token'];
            $emailBody = getEmailTemplate("
                <h2>Welcome to EazzyBizzy!</h2>
                <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
                <p><a href='{$verificationLink}' class='button'>Verify Email</a></p>
            ");
            sendEmail($user['email'], 'Verify Your Email - EazzyBizzy', $emailBody);
            
            successResponse([
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                ]
            ], 'Registration successful. Please check your email for verification.', 201);
            break;
            
        case 'login':
            rateLimitMiddleware('login', 5, 5);
            
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['email', 'password'];
            $errors = validateRequired($data, $required);
            
            if (!empty($errors)) {
                errorResponse('Validation failed', $errors, 422);
            }
            
            $user = $userModel->findByEmail($data['email']);
            
            if (!$user || !verifyPassword($data['password'], $user['password'])) {
                errorResponse('Invalid credentials', [], 401);
            }
            
            if ($user['status'] !== 'active') {
                errorResponse('Account is inactive', [], 403);
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Merge guest cart if exists
            if (isset($_SESSION['guest_cart_id'])) {
                require_once __DIR__ . '/../app/models/Cart.php';
                $cartModel = new Cart();
                $cartModel->mergeGuestCart($_SESSION['guest_cart_id'], $user['id']);
                unset($_SESSION['guest_cart_id']);
            }
            
            successResponse([
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'avatar' => $user['avatar'],
                    'email_verified' => $user['email_verified'],
                ]
            ], 'Login successful');
            break;
            
        case 'logout':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            session_destroy();
            successResponse([], 'Logout successful');
            break;
            
        case 'current':
            if (!isset($_SESSION['user_id'])) {
                errorResponse('Not authenticated', [], 401);
            }
            
            $user = $userModel->findById($_SESSION['user_id']);
            
            if (!$user) {
                session_destroy();
                errorResponse('User not found', [], 404);
            }
            
            successResponse([
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'avatar' => $user['avatar'],
                    'phone' => $user['phone'],
                    'email_verified' => $user['email_verified'],
                ]
            ]);
            break;
            
        case 'forgot-password':
            rateLimitMiddleware('forgot_password', 3, 15);
            
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['email'])) {
                errorResponse('Email is required', [], 422);
            }
            
            $user = $userModel->findByEmail($data['email']);
            
            if ($user) {
                $token = $userModel->setResetToken($data['email']);
                
                $resetLink = $_ENV['APP_URL'] . '/reset-password?token=' . $token;
                $emailBody = getEmailTemplate("
                    <h2>Password Reset Request</h2>
                    <p>We received a request to reset your password. Click the button below to proceed:</p>
                    <p><a href='{$resetLink}' class='button'>Reset Password</a></p>
                    <p>This link will expire in 1 hour. If you didn't request this, please ignore this email.</p>
                ");
                sendEmail($user['email'], 'Password Reset - EazzyBizzy', $emailBody);
            }
            
            // Always return success to prevent email enumeration
            successResponse([], 'If the email exists, a password reset link has been sent');
            break;
            
        case 'reset-password':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['token', 'password'];
            $errors = validateRequired($data, $required);
            
            if (!empty($errors)) {
                errorResponse('Validation failed', $errors, 422);
            }
            
            if (strlen($data['password']) < 8) {
                errorResponse('Password must be at least 8 characters', [], 422);
            }
            
            $success = $userModel->resetPassword($data['token'], $data['password']);
            
            if (!$success) {
                errorResponse('Invalid or expired reset token', [], 400);
            }
            
            successResponse([], 'Password reset successful');
            break;
            
        case 'verify-email':
            if ($method !== 'POST') {
                errorResponse('Method not allowed', [], 405);
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['token'])) {
                errorResponse('Token is required', [], 422);
            }
            
            $success = $userModel->verifyEmail($data['token']);
            
            if (!$success) {
                errorResponse('Invalid verification token', [], 400);
            }
            
            successResponse([], 'Email verified successfully');
            break;
            
        default:
            errorResponse('Invalid action', [], 404);
    }
    
} catch (Exception $e) {
    errorResponse('Server error: ' . $e->getMessage(), [], 500);
}
