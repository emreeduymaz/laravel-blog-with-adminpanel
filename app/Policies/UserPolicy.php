<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Kullanıcı listesini görüntüleme yetkisi
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view users');
    }

    /**
     * Kullanıcı detayını görüntüleme yetkisi
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('view users');
    }

    /**
     * Yeni kullanıcı oluşturma yetkisi
     */
    public function create(User $user): bool
    {
        return $user->can('create users');
    }

    /**
     * Kullanıcı düzenleme yetkisi
     */
    public function update(User $user, User $model): bool
    {
        // Kullanıcı düzenleme yetkisi olmalı
        if (!$user->can('edit users')) {
            return false;
        }

        // Kendi profilini düzenleyebilir
        if ($user->id === $model->id) {
            return true;
        }

        // Rol değiştirme işlemi için özel kontrol
        return $this->canManageUserRoles($user, $model);
    }

    /**
     * Kullanıcı silme yetkisi
     */
    public function delete(User $user, User $model): bool
    {
        // Kendini silemez
        if ($user->id === $model->id) {
            return false;
        }

        return $user->can('delete users');
    }

    /**
     * Kullanıcı rollerini yönetme yetkisi
     */
    public function manageUserRoles(User $user, User $model): bool
    {
        return $this->canManageUserRoles($user, $model);
    }

    /**
     * Rol yönetim yetkisi kontrolü
     */
    private function canManageUserRoles(User $user, User $model): bool
    {
        // Rol yönetim yetkisi olmalı
        if (!$user->can('manage user roles')) {
            return false;
        }

        // Super Admin herkesi yönetebilir
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin sadece kendi seviyesinden düşük rolleri yönetebilir
        if ($user->hasRole('Admin')) {
            // Super Admin'i yönetemez
            if ($model->hasRole('Super Admin')) {
                return false;
            }
            return true;
        }

        // Editor ve Author rol değiştiremez
        return false;
    }

    /**
     * Belirli bir rolü atayabilir mi?
     */
    public function assignRole(User $user, string $roleName): bool
    {
        // Rol hiyerarşisi: Her rol sadece kendinden alt seviyedeki rolleri verebilir
        if ($user->hasRole('Super Admin')) {
            // Super Admin: Admin, Editor, Author verebilir (Super Admin veremez)
            return in_array($roleName, ['Admin', 'Editor', 'Author']);
        }

        if ($user->hasRole('Admin')) {
            // Admin: Editor, Author verebilir (Admin ve Super Admin veremez)
            return in_array($roleName, ['Editor', 'Author']);
        }

        if ($user->hasRole('Editor')) {
            // Editor: Sadece Author verebilir
            return $roleName === 'Author';
        }

        // Author hiçbir rol veremez
        return false;
    }
}
