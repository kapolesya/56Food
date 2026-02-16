<?php
// helpers/validator.php

/**
 * Sanitize input string used for display (prevents XSS)
 * @param mixed $input
 * @return string
 */
function sanitize_text($input): string
{
    if (is_null($input)) return '';
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function validate_email(string $email): bool
{
    $email = trim($email);
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * @param string $password
 * @param int $minLength
 * @return bool
 */
function validate_password(string $password, int $minLength = 8): bool
{
    return strlen($password) >= $minLength;
}

/**
 * Sanitize redirect URL to prevent open redirection
 * @param string|null $url
 * @return string|null
 */
function sanitize_redirect(?string $url): ?string
{
    if (empty($url)) return null;
    
    // Parse the URL
    $parsed = parse_url($url);
    
    // If it has a host, it must match our domain (or be relative)
    // Simplified: stricter check, only allow relative paths starting with / or simple filenames
    // This prevents https://evil.com/
    
    if (isset($parsed['scheme']) || isset($parsed['host'])) {
        return 'index.php'; // Default to index if absolute URL provided
    }
    
    // Clean the path
    $path = $parsed['path'] ?? '';
    // Prevent directory traversal
    if (strpos($path, '..') !== false) {
        return 'index.php';
    }
    
    return $url;
}
?>
