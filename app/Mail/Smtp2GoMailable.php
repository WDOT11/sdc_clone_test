<?php

namespace App\Mail;

class Smtp2GoMailable
{
    public string $to;
    public string $replyto;
    public string $subject;
    public string $htmlBody = '';
    public string $textBody = '';
    public string $view;
    public array $data = [];

    public function to(string $email): static
    {
        $this->to = $email;
        return $this;
    }
    public function replyto(string $email): static
    {
        $this->replyto = $email;
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

    public function build(): void
    {
        $this->htmlBody = view($this->view, $this->data)->render();
        $this->textBody = strip_tags($this->htmlBody);
    }
}
