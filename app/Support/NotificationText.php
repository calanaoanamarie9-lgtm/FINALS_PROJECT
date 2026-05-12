<?php

namespace App\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

/**
 * Ensures values passed to MailMessage::line() / subject() are plain strings.
 * Laravel's SimpleMessage::formatLine() maps trim() over array elements; nested
 * arrays then trigger "trim(): Argument #1 ($string) must be of type string, array given".
 */
final class NotificationText
{
    public static function asString(mixed $value): string
    {
        if ($value instanceof Htmlable) {
            return $value->toHtml();
        }

        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            $parts = [];
            foreach (Arr::flatten($value) as $chunk) {
                if (is_string($chunk) || is_numeric($chunk)) {
                    $parts[] = (string) $chunk;
                }
            }

            return implode(' ', $parts);
        }

        return '';
    }
}
