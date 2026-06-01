<?php
// config/mail_config.php

require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return [
    'host'         => $_ENV['SMTP_HOST'] ?? 'smtp-relay.brevo.com',
    'port'         => (int)($_ENV['SMTP_PORT'] ?? 587),
    'encryption'   => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
    'username'     => $_ENV['SMTP_USER'] ?? '',      // used for SMTP auth
    'password'     => $_ENV['SMTP_PASS'] ?? '',
    'sender_email' => $_ENV['SENDER_EMAIL'] ?? $_ENV['SMTP_USER'] ?? '', // fallback to SMTP_USER
    'sender_name'  => $_ENV['SENDER_NAME'] ?? 'Restaurant Management System'
];
?>