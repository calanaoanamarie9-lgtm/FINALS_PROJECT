<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(?string $phone, string $message): void
    {
        if (! $phone) {
            return;
        }

        Log::channel('single')->info('[SMS stub] '.$phone.' — '.$message);
    }
}
