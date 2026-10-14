<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function fieldLength(string $value): int {
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function respond(int $status, bool $success, string $message): never {
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_SLASHES);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, false, 'Method not allowed.');
}
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 16000) {
    respond(413, false, 'Message is too large.');
}
// Browsers provide Origin on fetch; reject unexpected origins when present.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
if ($origin !== '' && $host !== '') {
    $originHost = parse_url($origin, PHP_URL_HOST);
    $requestHost = explode(':', $host)[0];
    if (!is_string($originHost) || strcasecmp($originHost, $requestHost) !== 0) {
        respond(403, false, 'Invalid request origin.');
    }
}
if (!empty($_POST['website'])) {
    respond(200, true, 'Thank you. Your message has been received.');
}
$started = filter_var($_POST['form_started'] ?? null, FILTER_VALIDATE_INT);
$now = time();
if (!$started || $started > $now + 60 || $now - $started < 2 || $now - $started > 86400) {
    respond(422, false, 'Please reload the page and try again.');
}
// Basic per-session throttle. For high-traffic production, add IP-based server/WAF limits.
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'use_strict_mode' => true]);
if (isset($_SESSION['last_contact']) && $now - (int)$_SESSION['last_contact'] < 60) {
    respond(429, false, 'Please wait one minute before sending another enquiry.');
}
$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$interest = trim((string)($_POST['interest'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$consent = (string)($_POST['consent'] ?? '');
if (fieldLength($name) < 2 || fieldLength($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190 || fieldLength($phone) > 35 || fieldLength($message) < 10 || fieldLength($message) > 4000 || !in_array($interest, ['owner', 'driver', 'general'], true) || $consent !== 'yes') {
    respond(422, false, 'Please check the required fields and try again.');
}
if (preg_match('/[\r\n]/', $name . $email . $phone)) {
    respond(422, false, 'Invalid contact details.');
}
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
$configFile = dirname(__DIR__) . '/config/mail.php';
if (!is_file($autoload) || !is_file($configFile)) {
    error_log('Drive4U contact form: PHPMailer or SMTP configuration missing.');
    respond(503, false, 'The contact form is being configured. Please email info@drive4u.co.za.');
}
require $autoload;
$config = require $configFile;
if (!is_array($config)) {
    respond(503, false, 'Contact form is temporarily unavailable.');
}
$labels = ['owner' => 'Vehicle owner partnership', 'driver' => 'Driver application', 'general' => 'General enquiry'];
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = (string)$config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = (string)$config['smtp_username'];
    $mail->Password = (string)$config['smtp_password'];
    $mail->Port = (int)$config['smtp_port'];
    $mail->SMTPSecure = $config['smtp_encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->Timeout = 15;
    $mail->setFrom((string)$config['from_email'], (string)$config['from_name']);
    $mail->addAddress((string)$config['to_email'], (string)$config['to_name']);
    $mail->addReplyTo($email, $name);
    $mail->isHTML(false);
    $mail->Subject = '[Drive4U Website] ' . $labels[$interest];
    $mail->Body = "New website enquiry\n\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nInterest: {$labels[$interest]}\n\nMessage:\n{$message}\n\nConsent: Yes";
    $mail->send();
    $_SESSION['last_contact'] = $now;
    respond(200, true, 'Thank you. Your message has been sent successfully.');
} catch (Exception $e) {
    error_log('Drive4U contact form SMTP error: ' . $mail->ErrorInfo);
    respond(502, false, 'We could not send your message right now. Please try again or email info@drive4u.co.za.');
}
