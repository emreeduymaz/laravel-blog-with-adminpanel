<?php
// app/helpers.php (yoksa oluştur)
use App\Support\LogActivity;
if (! function_exists('log_activity')) {
    function log_activity(string $action, $model = null, ?array $changes = null): void {
        LogActivity::add($action, $model, $changes);
    }
}
