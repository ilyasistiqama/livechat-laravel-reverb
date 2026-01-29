<?php

namespace App\Services;

use App\Models\ChatPair;

class ChatPairService
{
    public function getOrCreate(
        int $meId,
        string $meType,
        int $toId,
        string $toType,
        string $type = 'customer-to-admin'
    ): ChatPair {

        // URUTKAN BIAR UNIK (ANTI DUPLIKAT & BOCOR)
        if (
            $meType > $toType ||
            ($meType === $toType && $meId > $toId)
        ) {
            [$meId, $toId] = [$toId, $meId];
            [$meType, $toType] = [$toType, $meType];
        }

        return ChatPair::firstOrCreate([
            'user_a_id'   => $meId,
            'user_a_type' => $meType,
            'user_b_id'   => $toId,
            'user_b_type' => $toType,
        ], [
            'type'     => $type,
            'active'   => true,
            'finished' => false,
        ]);
    }
}
