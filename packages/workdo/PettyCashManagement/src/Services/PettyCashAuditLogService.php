<?php

namespace Workdo\PettyCashManagement\Services;

use Workdo\PettyCashManagement\Models\PettyCashAuditLog;

class PettyCashAuditLogService
{
    public function write(
        int $tenantId,
        ?int $actorId,
        string $action,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $meta = []
    ): PettyCashAuditLog {
        return PettyCashAuditLog::create([
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'actor_id' => $actorId,
            'meta' => empty($meta) ? null : $meta,
            'created_by' => $tenantId,
            'created_at' => now(),
        ]);
    }
}

