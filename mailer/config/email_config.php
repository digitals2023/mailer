<?php
/**
 * Email Configuration - SMTP Settings
 * 
 * Fill in your SMTP details below.
 * Edit this file according to your email server.
 */

return [
    // === Mailer Settings ===
    'from_email'    => 'your-email@gmail.com',
    'from_name'     => 'Your Name or Company',
    'reply_to'      => null, // Leave null to use from_email

    // === SMTP Settings ===
    'smtp_host'     => 'smtp.gmail.com',      // e.g., smtp.gmail.com, smtp.office365.com, smtp.mail.yahoo.com
    'smtp_port'     => 587,                      // 587 for TLS, 465 for SSL
    'smtp_secure'   => 'tls',                    // 'tls' or 'ssl'
    'smtp_auth'     => true,
    'smtp_timeout'  => 30,

    // === Email Account Credentials ===
    'smtp_user'     => 'your-email@gmail.com',
    'smtp_pass'     => 'your-app-password',

    // === Debug Settings ===
    'debug_mode'    => false,                   // Set true for debugging
    'debug_file'    => __DIR__ . '/../logs/smtp_debug.log',
];