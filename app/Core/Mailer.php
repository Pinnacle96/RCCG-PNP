<?php
/**
 * Email Mailer
 * Wrapper for PHPMailer
 */

class Mailer {
    private \PHPMailer\PHPMailer\PHPMailer $mailer;
    private array $config;

    public function __construct() {
        $this->config = require APP . '/Config/mailer.php';
        $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
        $this->setupMailer();
    }

    /**
     * Setup PHPMailer with config
     */
    private function setupMailer(): void {
        $config = $this->config;

        if ($config['driver'] === 'smtp') {
            $this->mailer->isSMTP();
            $this->mailer->Host = $config['host'];
            $this->mailer->Port = $config['port'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $config['username'];
            $this->mailer->Password = $config['password'];
            $this->mailer->SMTPSecure = $config['encryption'];
            $this->mailer->SMTPAutoTLS = false;
        }

        $this->mailer->setFrom($config['from_email'], $config['from_name']);
        $this->mailer->addReplyTo($config['reply_to'], $config['from_name']);
    }

    /**
     * Send email
     */
    public function send(string $to, string $subject, string $body, bool $html = true): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);

            $this->mailer->Subject = $subject;
            if ($html) {
                $this->mailer->isHTML(true);
                $this->mailer->Body = $body;
                $this->mailer->AltBody = strip_tags($body);
            } else {
                $this->mailer->isHTML(false);
                $this->mailer->Body = $body;
            }

            return $this->mailer->send();
        } catch (\Exception $e) {
            error_log("Mailer error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email with attachment
     */
    public function sendWithAttachment(string $to, string $subject, string $body, string $filePath): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->addAttachment($filePath);

            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;

            return $this->mailer->send();
        } catch (\Exception $e) {
            error_log("Mailer error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset mailer for next send
     */
    public function reset(): void {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
    }
}
