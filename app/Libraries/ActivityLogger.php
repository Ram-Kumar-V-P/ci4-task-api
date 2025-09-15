<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;

class ActivityLogger
{
    public static function log(int $userId, string $action, ?int $taskId = null, array $meta = []): void
    {
        (new ActivityLogModel())->insert([
            'user_id' => $userId,
            'task_id' => $taskId,
            'action'  => $action,
            'meta'    => json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
