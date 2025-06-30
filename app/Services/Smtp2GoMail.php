<?php

namespace App\Services;

use App\Http\Controllers\Admin\SDCOptionController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Smtp2GoMail
{
    protected string $to;
    protected string $subject;
    protected string $view;
    protected array $data = [];
    protected string $htmlBody = '';
    protected string $textBody = '';
    protected array $cc = [];
    protected array $bcc = [];
    protected array $attachments = [];

    protected string $replyTo;
    protected array $message = [];

    public static function to(string $email): static
    {
        $instance = new static();
        $instance->to = $email;
        return $instance;
    }

    public function replyTo(string $email): static
    {
        $this->replyTo = $email;
        return $this;
    }

    public function subject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function view(string $view, array $data = []): static
    {
        $this->view = $view;
        $this->data = $data;
        return $this;
    }

    public function cc(array|string $emails): static
    {
        $this->cc = is_array($emails) ? $emails : [$emails];
        return $this;
    }

    public function bcc(array|string $emails): static
    {
        $this->bcc = is_array($emails) ? $emails : [$emails];
        return $this;
    }

    public function attach(string $filePath, ?string $filename = null): static
    {
        $filename = $filename ?? basename($filePath);
        $this->attachments[] = [
            'file_name' => $filename,
            'file_body' => base64_encode(file_get_contents($filePath)),
        ];
        return $this;
    }

    protected function build(): void
    {
        $this->htmlBody = view($this->view, $this->data)->render();
        $this->textBody = strip_tags($this->htmlBody);
    }

    public function send(): bool|string
    {
        $this->build();

        $serviceProvider = Session::get('service_provider') ?? 1;
        $apiKey = SDCOptionController::getOption('sdcsm_smtp2Go_api_key', $serviceProvider);
        $senderEmail = SDCOptionController::getOption('sdcsm_smtp2Go_sender_email', $serviceProvider);

        $payload = [
            'api_key' => $apiKey,
            'to' => [$this->to],
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'sender' => $senderEmail,
            'subject' => $this->subject,
            'text_body' => $this->textBody,
            'html_body' => $this->htmlBody,
            'attachments' => $this->attachments,
        ];

        if (!empty($this->replyTo)) {

            $payload['custom_headers'] =  [
                [
                    "header" => "Reply-To",
                    "value" => $this->replyTo
                ]
            ];
        }

        $response = Http::post('https://api.smtp2go.com/v3/email/send', $payload);

        if (!$response->successful()) {
            Log::error('SMTP2GO email failed', [
                'error' => $response->body(),
                'to' => $this->to,
                'subject' => $this->subject,
            ]);
            return $response->body();
        }

        return true;
    }
}
