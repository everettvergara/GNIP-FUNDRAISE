<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class EmailTemplateMailer
{
    /**
     * Send an email using a database email template and placeholder replacements.
     */
    public function send(string $templateKey, string $to, array $placeholders = []): void
    {
        $template = EmailTemplate::query()->where('key', $templateKey)->first();

        if (! $template) {
            return;
        }

        $subject = $this->replacePlaceholders($template->subject, $placeholders);
        $body = $this->replacePlaceholders($template->body, $placeholders);

        Mail::raw($body, function ($message) use ($to, $subject): void {
            $message->to($to)->subject($subject);
        });
    }

    private function replacePlaceholders(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{ '.$key.' }}', (string) $value, $text);
        }

        return $text;
    }
}
