<?php
// app/Support/LogActivity.php
namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogActivity
{
    public static function add(string $action, $model = null, array $changes = null): void
    {
        ActivityLog::create([
            'user_id'   => Auth::id(),
            'action'    => $action,
            'model'     => $model ? get_class($model) : null,
            'model_id'  => $model?->getKey(),
            'changes'   => $changes,
            'url'       => Request::fullUrl() ?: null,
            'ip'        => Request::ip(),
            'user_agent'=> substr((string) Request::userAgent(), 0, 512),
        ]);
    }
}
