<?php

namespace App\Application\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public function log(string $action, Model $entity, ?array $oldValues = null, ?array $newValues = null, array $context = []): AuditLog
    {
        return AuditLog::query()->create([
            'user_id' => Auth::id(),
            'entity_type' => $entity::class,
            'entity_id' => $entity->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'context' => $context,
        ]);
    }
}
