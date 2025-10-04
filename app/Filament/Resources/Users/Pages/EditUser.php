<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        if ($this->previousUrl && str_contains($this->previousUrl, '/admin/users')) {
            return $this->previousUrl;
        }

        return $this->getResource()::getUrl('index');
    }

    // Rol kontrolleri artık UserResource'da yapılıyor
}
