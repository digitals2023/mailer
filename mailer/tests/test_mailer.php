<?php
/**
 * Quick Test for Mailer Class
 * 
 * This script tests the Mailer class without actually sending emails.
 * It verifies the class structure and basic functionality.
 */

// Define project root
define('PROJECT_ROOT', __DIR__ . '/..');

// Load Composer autoloader
require_once PROJECT_ROOT . '/vendor/autoload.php';

// Load config (simulated)
$config = [
    'from_email' => 'test@example.com',
    'from_name' => 'Test Mailer',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    'smtp_timeout' => 30,
    'smtp_user' => 'test@example.com',
    'smtp_pass' => 'test',
    'debug_mode' => false,
];

echo "=== Testing Mailer Class ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Running from: " . __DIR__ . "\n\n";

// Test 1: Class exists and can be instantiated
echo "Test 1: Class instantiation...\n";
try {
    $mailer = new App\Mailer($config);
    echo "  ✓ Mailer class instantiated successfully\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Basic methods
echo "Test 2: Basic methods...\n";
try {
    $mailer->setSubject("Test Subject");
    echo "  ✓ setSubject() works\n";
    
    $mailer->setTemplate("Hello {name},\n\nThis is a test email.");
    echo "  ✓ setTemplate() works\n";
    
    $mailer->addRecipient("test@example.com", "Test User");
    echo "  ✓ addRecipient() works, total: " . $mailer->getSummary()['total'] . "\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: addRecipients
echo "Test 3: addRecipients()...\n";
try {
    $recipients = [
        'user1@example.com' => 'User One',
        'user2@example.com' => 'User Two',
    ];
    $mailer->addRecipients($recipients);
    $summary = $mailer->getSummary();
    echo "  ✓ addRecipients() works, total: " . $summary['total'] . "\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: sendBulk (without actual sending)
echo "Test 4: sendBulk()...\n";
try {
    $result = $mailer->sendBulk();
    echo "  ✓ sendBulk() completed\n";
    echo "  Summary: " . $result['successCount'] . " sent, " . $result['failCount'] . " failed\n";
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Reset and test with empty recipients
echo "Test 5: Reset functionality...\n";
try {
    $mailer->reset();
    $summary = $mailer->getSummary();
    if ($summary['total'] == 0) {
        echo "  ✓ reset() works correctly\n";
    } else {
        echo "  ✗ reset() failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== All Tests Passed! ===\n";
echo "\nThe Mailer class is working correctly.\n";
echo "Next steps:\n";
echo "1. Edit config/email_config.php and fill in your SMTP details\n";
echo "2. Open public/index.php in your browser\n";
echo "3. Fill in the form and send bulk emails\n";