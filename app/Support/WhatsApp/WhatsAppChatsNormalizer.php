<?php

namespace App\Support\WhatsApp;

class WhatsAppChatsNormalizer
{
    /**
     * @param  array<int, mixed>  $chats
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeList(array $chats): array
    {
        $out = [];
        foreach ($chats as $c) {
            if (! is_array($c)) {
                continue;
            }
            $out[] = self::normalizeOne($c);
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $c
     * @return array<string, mixed>
     */
    public static function normalizeOne(array $c): array
    {
        return [
            'id' => (string) ($c['id'] ?? $c['chatId'] ?? $c['_serialized'] ?? ''),
            'name' => (string) ($c['name'] ?? $c['pushName'] ?? $c['formattedTitle'] ?? ''),
            'unreadCount' => (int) ($c['unreadCount'] ?? $c['unread'] ?? 0),
            'lastMessage' => $c['lastMessage'] ?? $c['last_message'] ?? null,
        ];
    }

    /**
     * يمرّ على استجابة الميكروسيرفس ويُطبّق التطبيع على أي مصفوفة chats موجودة.
     */
    public static function normalizePayload(mixed $body): mixed
    {
        if (! is_array($body)) {
            return $body;
        }

        if (isset($body['chats']) && is_array($body['chats'])) {
            $body['chats'] = self::normalizeList($body['chats']);

            return $body;
        }

        if (isset($body['data']['chats']) && is_array($body['data']['chats'])) {
            $body['data']['chats'] = self::normalizeList($body['data']['chats']);

            return $body;
        }

        if (isset($body['data']['data']['chats']) && is_array($body['data']['data']['chats'])) {
            $body['data']['data']['chats'] = self::normalizeList($body['data']['data']['chats']);

            return $body;
        }

        return $body;
    }
}
