<?php

namespace App\Support\WhatsApp;

class WhatsAppMessagesHelper
{
    /**
     * ترتيب الرسائل من الأقدم للأحدث (للعرض في الشات).
     *
     * @param  array<int, mixed>  $messages
     * @return array<int, mixed>
     */
    public static function sortOldestFirst(array $messages): array
    {
        usort($messages, function ($a, $b) {
            return self::extractTs($a) <=> self::extractTs($b);
        });

        return $messages;
    }

    /**
     * @param  array<string, mixed>|object  $m
     */
    public static function extractTs(mixed $m): int
    {
        if (is_object($m)) {
            $m = (array) $m;
        }
        if (! is_array($m)) {
            return 0;
        }
        $t = $m['timestamp'] ?? $m['t'] ?? $m['time'] ?? null;
        if ($t === null) {
            return 0;
        }
        if (is_numeric($t)) {
            $n = (int) $t;

            return $n < 1e12 ? $n * 1000 : $n;
        }

        return 0;
    }
}
