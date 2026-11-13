<?php
/**
 * Mailer Class - Core Bulk Email Sender
 *
 * Uses PHPMailer for SMTP email sending.
 * Supports bulk sending with progress tracking.
 */

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class Mailer
{
    private $config;
    private $mail;
    private $totalCount = 0;
    private $successCount = 0;
    private $failCount = 0;
    private $failedRecipients = [];
    private $lastError = '';
    private $lastRecipient = '';

    public function __construct(array $config = [])
    {
        $this->config = array_merge(require __DIR__ . '/../config/email_config.php', $config);
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
        $this->setupMailer();
    }

    private function setupSMTP(): void
    {
        if (!empty($this->config['smtp_host'])) {
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp_host'];
            $this->mail->Port = (int) $this->config['smtp_port'];
            $this->mail->SMTPSecure = $this->config['smtp_secure'];
            $this->mail->SMTPAuth = (bool) $this->config['smtp_auth'];
            $this->mail->Timeout = (int) $this->config['smtp_timeout'];
            if (!empty($this->config['smtp_user'])) {
                $this->mail->Username = $this->config['smtp_user'];
                $this->mail->Password = $this->config['smtp_pass'];
            }
        }
    }

    private function setupMailer(): void
    {
        $this->mail->CharSet = 'UTF-8';
        $this->mail->LANG = 'en';
        $this->mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        if (!empty($this->config['from_email'])) {
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name'] ?? 'Mailer');
        }
        if (!empty($this->config['reply_to'])) {
            $this->mail->addReplyTo($this->config['reply_to']);
        }
    }

    public function setTemplate(string $template): self
    {
        $this->mail->msgHTML($template);
        return $this;
    }

    public function addRecipient(string $email, string $name = '', array $placeholders = []): self
    {
        $this->mail->addAddress($email, $name);
        $this->totalCount++;
        $this->lastRecipient = $email;
        return $this;
    }

    public function addRecipients(array $recipients): self
    {
        $this->totalCount = 0;
        $this->successCount = 0;
        $this->failCount = 0;
        $this->failedRecipients = [];
        foreach ($recipients as $email => $name) {
            $this->addRecipient($email, $name);
        }
        $this->lastRecipient = key($recipients);
        return $this;
    }

    public function addCC(string $email, string $name = ''): self
    {
        $this->mail->addCC($email, $name);
        return $this;
    }

    public function addBCC(string $email, string $name = ''): self
    {
        $this->mail->addBCC($email, $name);
        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->mail->Subject = $subject;
        return $this;
    }

    public function setTextBody(string $text): self
    {
        $this->mail->AltBody = $text;
        return $this;
    }

    public function addAttachment(string $filePath, string $attachmentName = ''): self
    {
        $this->mail->addAttachment($filePath, $attachmentName);
        return $this;
    }

    public function addAttachments(array $files): self
    {
        foreach ($files as $filePath) {
            $this->addAttachment($filePath);
        }
        return $this;
    }

    public function sendWithRetry(int $maxRetries = 2): bool
    {
        for ($i = 0; $i <= $maxRetries; $i++) {
            try {
                if ($this->mail->send()) {
                    $this->successCount++;
                    $this->lastError = '';
                    return true;
                }
            } catch (Exception $e) {
                $this->lastError = $this->mail->ErrorInfo;
            }
        }
        $this->failCount++;
        $this->failedRecipients[] = ['email' => $this->lastRecipient, 'error' => $this->lastError, 'attempts' => $maxRetries + 1];
        if ($this->config['debug_mode']) {
            error_log('Send Failure: ' . $this->lastError);
        }
        return false;
    }

    public function sendBulk(callable $progressCallback = null): array
    {
        $this->successCount = 0;
        $this->failCount = 0;
        $this->failedRecipients = [];
        if ($this->totalCount === 0) {
            return ['success' => false, 'message' => 'No recipients added.', 'successCount' => 0, 'failCount' => 0, 'totalCount' => 0];
        }
        for ($i = 0; $i < $this->totalCount; $i++) {
            $this->sendWithRetry();
            if ($progressCallback) {
                $percentage = $this->totalCount > 0 ? round(($this->successCount + $this->failCount) / $this->totalCount * 100) : 100;
                $progressCallback($percentage);
            }
        }
        return [
            'success' => $this->failCount === 0,
            'message' => $this->failCount === 0 ? 'All emails sent successfully.' : 'Some emails failed to send.',
            'successCount' => $this->successCount,
            'failCount' => $this->failCount,
            'totalCount' => $this->totalCount,
            'failedRecipients' => $this->failedRecipients,
            'lastError' => $this->lastError
        ];
    }

    public function getSummary(): array
    {
        return [
            'total' => $this->totalCount,
            'success' => $this->successCount,
            'failed' => $this->failCount,
            'percentage' => $this->totalCount > 0 ? round($this->successCount / $this->totalCount * 100) : 0,
            'lastError' => $this->lastError,
            'failedRecipients' => $this->failedRecipients
        ];
    }

    public function getPHPMailer(): PHPMailer
    {
        return $this->mail;
    }

    public function clearAddresses(): self
    {
        $this->mail->clearAddresses();
        $this->mail->clearCCs();
        $this->mail->clearBCCs();
        return $this;
    }

    public function reset(): self
    {
        $this->mail->clearAddresses();
        $this->mail->clearCCs();
        $this->mail->clearBCCs();
        $this->totalCount = 0;
        $this->successCount = 0;
        $this->failCount = 0;
        $this->failedRecipients = [];
        return $this;
    }

    public static function getDebugPath(): string
    {
        $config = require __DIR__ . '/../config/email_config.php';
        return $config['debug_file'] ?? '';
    }
}