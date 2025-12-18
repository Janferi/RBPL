<?php
/**
 * =============================================================================
 * COMPREHENSIVE SECURITY CONFIGURATION
 * =============================================================================
 * 
 * This file implements comprehensive security measures to protect against:
 * - Cross-Site Scripting (XSS)
 * - Cross-Site Request Forgery (CSRF)
 * - Clickjacking attacks
 * - Session hijacking
 * - Information disclosure
 * - Server fingerprinting
 * 
 * Include this file at the VERY BEGINNING of every PHP file:
 * require_once '../security_headers.php';
 * 
 * =============================================================================
 */

// =============================================================================
// ERROR HANDLING - PREVENT APPLICATION ERROR DISCLOSURE
// =============================================================================

// In production: Hide all errors from users
// In development: You can set display_errors to 1 for debugging
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
    // Protect logs directory
    @file_put_contents($logDir . '/.htaccess', 'Deny from all');
}

// Custom error handler to prevent information disclosure
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log the error
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    // Don't execute PHP's internal error handler
    return true;
});

// =============================================================================
// HIDE SERVER INFORMATION
// =============================================================================

// Remove X-Powered-By header
if (function_exists('header_remove')) {
    header_remove('X-Powered-By');
}
// Alternative method
ini_set('expose_php', 'off');

// =============================================================================
// SECURE SESSION COOKIE CONFIGURATION
// =============================================================================

if (session_status() === PHP_SESSION_NONE) {
    
    // Cookie Security Flags
    ini_set('session.cookie_httponly', 1);
    
    // Secure flag - auto-detect HTTPS
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    ini_set('session.cookie_secure', $isSecure ? 1 : 0);
    
    // SameSite attribute - CSRF protection
    ini_set('session.cookie_samesite', 'Strict');
    
    // Session security settings
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    
    // Strong session ID
    ini_set('session.sid_length', 48);
    ini_set('session.sid_bits_per_character', 6);
    
    // Session lifetime
    ini_set('session.cookie_lifetime', 0);
    ini_set('session.gc_maxlifetime', 3600);
    
    // Cookie path
    ini_set('session.cookie_path', '/');
    
    // Apply cookie parameters
    $cookieParams = [
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict'
    ];
    session_set_cookie_params($cookieParams);
}

// =============================================================================
// HTTP SECURITY HEADERS
// =============================================================================

if (!headers_sent()) {
    
    // Remove server information headers
    header_remove('X-Powered-By');
    header_remove('Server');
    
    // Anti-Clickjacking
    header("X-Frame-Options: SAMEORIGIN");
    
    // Content Security Policy
    $csp_directives = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
        "style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://fonts.googleapis.com https://cdn.jsdelivr.net",
        "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
        "img-src 'self' data: blob: https:",
        "connect-src 'self'",
        "frame-ancestors 'self'",
        "form-action 'self'",
        "base-uri 'self'",
        "object-src 'none'",
        "upgrade-insecure-requests"
    ];
    header("Content-Security-Policy: " . implode("; ", $csp_directives));
    
    // XSS Protection
    header("X-XSS-Protection: 1; mode=block");
    
    // Prevent MIME sniffing
    header("X-Content-Type-Options: nosniff");
    
    // Referrer Policy
    header("Referrer-Policy: strict-origin-when-cross-origin");
    
    // Permissions Policy
    $permissions = [
        "geolocation=()",
        "microphone=()",
        "camera=()",
        "payment=()",
        "usb=()"
    ];
    header("Permissions-Policy: " . implode(", ", $permissions));
    
    // Cache Control
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    // Cross-Origin Policies
    header("Cross-Origin-Opener-Policy: same-origin");
    header("Cross-Origin-Resource-Policy: same-origin");
}

// =============================================================================
// CSRF TOKEN FUNCTIONS
// =============================================================================

/**
 * Generate a CSRF token
 * Call this once per session/page
 */
function csrf_generate_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    // Regenerate token if older than 1 hour
    if (time() - $_SESSION['csrf_token_time'] > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Get HTML hidden input field with CSRF token
 * Use this in forms: <?php echo csrf_token_field(); ?>
 */
function csrf_token_field() {
    $token = csrf_generate_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Get just the CSRF token value
 * Use for AJAX requests
 */
function csrf_token() {
    return csrf_generate_token();
}

/**
 * Validate CSRF token from form submission
 * Returns true if valid, false otherwise
 */
function csrf_validate_token($token = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate CSRF token and die if invalid
 * Use at the beginning of POST handlers
 */
function csrf_require_valid_token() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_validate_token()) {
            http_response_code(403);
            die('Invalid security token. Please refresh the page and try again.');
        }
    }
}

// =============================================================================
// INPUT SANITIZATION FUNCTIONS
// =============================================================================

/**
 * Sanitize string input - prevents XSS
 */
function sanitize_input($input) {
    if (is_array($input)) {
        return array_map('sanitize_input', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize integer input
 */
function sanitize_int($input) {
    return filter_var($input, FILTER_VALIDATE_INT) !== false ? (int)$input : 0;
}

/**
 * Sanitize email input
 */
function sanitize_email($input) {
    return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
}

/**
 * Validate and sanitize URL
 */
function sanitize_url($input) {
    return filter_var(trim($input), FILTER_SANITIZE_URL);
}

// =============================================================================
// SESSION SECURITY FUNCTIONS
// =============================================================================

/**
 * Regenerate session ID - call after login
 */
function secure_session_regenerate() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
        // Also regenerate CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
}

/**
 * Securely destroy session - call on logout
 */
function secure_session_destroy() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }
}

/**
 * Check session timeout
 */
function check_session_timeout($maxIdleTime = 1800) {
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $maxIdleTime) {
            secure_session_destroy();
            return false;
        }
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// =============================================================================
// SECURITY VALIDATION FUNCTIONS
// =============================================================================

/**
 * Validate that a value exists in allowed list (prevent parameter tampering)
 */
function validate_in_list($value, array $allowedValues, $default = null) {
    return in_array($value, $allowedValues, true) ? $value : $default;
}

/**
 * Validate numeric ID
 */
function validate_id($id) {
    $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id !== false ? $id : false;
}

/**
 * Safe redirect - prevents open redirect vulnerabilities
 */
function safe_redirect($url, $allowedHosts = []) {
    $parsed = parse_url($url);
    
    // Only allow relative URLs or URLs to allowed hosts
    if (isset($parsed['host'])) {
        if (!in_array($parsed['host'], $allowedHosts)) {
            $url = '/';
        }
    }
    
    header("Location: " . $url);
    exit();
}
?>