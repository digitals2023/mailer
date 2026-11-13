<?php
/**
 * Send Processing - Handle bulk email sending
 * 
 * This script processes the form submission and sends bulk emails.
 * Use it via AJAX or directly in the browser.
 */

use App\Mailer;

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load config
$config = require_once __DIR__ . '/../config/email_config.php';

// Include DB if needed
// require_once __DIR__ . '/../includes/db.php';

// Helper function to convert CSV to array
function csvToArray(string $csv, string $delimiter = ','): array
{
    $rows = array_map('str_getcsv', explode("\n", $csv));
    $header = array_shift($rows);
    $result = [];
    foreach ($rows as $row) {
        if (count($row) === count($header)) {
            $result[] = array_combine($header, $row);
        }
    }
    return $result;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $subject = trim($_POST['subject'] ?? '');
    $emailType = $_POST['email_type'] ?? 'text';
    $template = trim($_POST['template'] ?? '');
    $recipients = $_POST['recipients'] ?? '';
    $fromEmail = $config['from_email'];
    $fromName = $config['from_name'];
    $replyTo = $config['reply_to'];

    $errors = [];

    // Validate subject
    if (empty($subject)) {
        $errors[] = 'Subject is required.';
    }

    // Validate recipients
    if (empty($recipients)) {
        $errors[] = 'Please provide at least one recipient.';
    }

    // Validate email type
    if (!in_array($emailType, ['text', 'html', 'template'])) {
        $errors[] = 'Invalid email type.';
    }

    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }

    // Create Mailer instance
    $mailer = new Mailer([
        'from_email' => $fromEmail,
        'from_name' => $fromName,
        'reply_to' => $replyTo,
        'debug_mode' => $config['debug_mode'],
    ]);

    // Set subject
    $mailer->setSubject($subject);

    // Set body based on type
    if ($emailType === 'text') {
        $mailer->setTextBody($template);
    } else {
        $mailer->setTemplate($template);
    }

    // Process recipients
    $recipientsArray = [];
    $recipientLines = array_filter(array_map('trim', explode("\n", $recipients)));

    foreach ($recipientLines as $line) {
        // Skip empty lines
        if (empty($line)) {
            continue;
        }

        // Handle CSV format: name,email
        if (strpos($line, ',') !== false) {
            $parts = str_getcsv($line);
            $email = trim($parts[1] ?? '');
            $name = trim($parts[0] ?? '');
        } else {
            // Handle email only
            $email = trim($line);
            $name = '';
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address: {$email}";
            continue;
        }

        $recipientsArray[$email] = $name;
    }

    if (empty($recipientsArray)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'errors' => ['No valid recipients found in the provided list.']
        ]);
        exit;
    }

    // Add recipients to mailer
    $mailer->addRecipients($recipientsArray);

    // Send emails
    $progress = function($percentage) use (&$lastProgress) {
        $lastProgress = $percentage;
    };

    $result = $mailer->sendBulk($progress);

    // Return response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result['failCount'] === 0,
        'message' => $result['message'],
        'successCount' => $result['successCount'],
        'failCount' => $result['failCount'],
        'totalCount' => $result['totalCount'],
        'progress' => $lastProgress ?? 0,
        'debug' => $config['debug_mode'] ? [
            'errors' => $result['failedRecipients'],
            'lastError' => $result['lastError']
        ] : null
    ]);

    exit;
}

// If not POST, show error
http_response_code(405);
echo 'Method not allowed. Use POST.';
