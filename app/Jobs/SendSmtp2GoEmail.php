<?php

namespace App\Jobs;

use App\Services\Smtp2GoMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSmtp2GoEmail implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected Smtp2GoMail $mail;

    public function __construct(Smtp2GoMail $mail)
    {
        $this->mail = $mail;
    }

    public function handle(): void
    {
        $this->mail->send();
    }
}
