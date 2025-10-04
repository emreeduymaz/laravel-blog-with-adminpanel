<?php

namespace App\Observers;

use App\Models\User;
use function log_activity;

class UserObserver
{
    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        // Rol değişikliklerini logla
        if ($user->isDirty()) {
            $changes = $user->getChanges();
            $original = $user->getOriginal();
            
            log_activity('user_updating', $user, [
                'changes' => $changes,
                'original' => $original,
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        log_activity('user_updated', $user, [
            'current_roles' => $user->roles->pluck('name')->toArray(),
        ]);
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        log_activity('user_created', $user, [
            'user_data' => $user->only(['name', 'email']),
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        log_activity('user_deleted', $user, [
            'user_data' => $user->only(['name', 'email']),
        ]);
    }
}
