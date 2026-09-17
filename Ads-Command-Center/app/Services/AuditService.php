<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log(array $data): AuditLog
    {
        $payload = array_merge($data, [
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'request_id' => Request::header('X-Request-ID') ?? null,
        ]);

        return AuditLog::create($payload);
    }
}
