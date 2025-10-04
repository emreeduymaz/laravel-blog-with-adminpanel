<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema; // v4 form imzası
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

// v4 aksiyonlar
use Filament\Actions\EditAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view users');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create users');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit users');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // DÜZ LİSTE – hiçbir layout bileşeni yok
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(255),

            Forms\Components\FileUpload::make('avatar')
                ->image()
                ->imageEditor()
                ->directory('avatars')
                ->visibility('public'),

            Forms\Components\Textarea::make('bio')
                ->rows(3),

            Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(255),

            Forms\Components\Toggle::make('google2fa_enabled')
                ->label('2FA Enabled')
                ->disabled()
                ->helperText('Users can enable 2FA from their profile'),

            Forms\Components\Select::make('roles')
                ->multiple()
                ->preload()
                ->searchable()
                ->visible(fn () => auth()->user()->can('manage user roles'))
                ->disabled(function ($record) {
                    // Rol yönetim yetkisi yoksa disabled
                    if (!auth()->user()->can('manage user roles')) {
                        return true;
                    }

                    // Super Admin kendi rolünü değiştiremez
                    if ($record && $record->hasRole('Super Admin') && auth()->user()->id === $record->id) {
                        return true;
                    }

                    return false;
                })
                ->options(function ($record) {
                    if (!auth()->user()->can('manage user roles')) {
                        return [];
                    }

                    $user = auth()->user();
                    $availableRoles = collect();

                    // MUTLAK KURAL: Super Admin rolü HİÇBİR DURUMDA gösterilmez

                    // Eğer kullanıcı kendi profilini düzenliyorsa, sadece mevcut rollerini göster
                    // ANCAK Super Admin rolünü asla gösterme
                    if ($record && $user->id === $record->id) {
                        $ownRoles = $record->roles->filter(function($role) {
                            return $role->name !== 'Super Admin';
                        })->pluck('name', 'id');

                        // Eğer Super Admin kendi profilini düzenliyorsa hiçbir seçenek gösterme
                        if ($user->hasRole('Super Admin')) {
                            return collect(); // Boş collection
                        }

                        return $ownRoles;
                    }

                    // Rol hiyerarşisi: Her rol sadece kendinden alt seviyedeki rolleri verebilir
                    if ($user->hasRole('Super Admin')) {
                        // Super Admin: Admin, Editor, Author verebilir (Super Admin ASLA veremez)
                        $availableRoles = \Spatie\Permission\Models\Role::whereNotIn('name', ['Super Admin'])->pluck('name', 'id');
                    }
                    elseif ($user->hasRole('Admin')) {
                        // Admin: Editor, Author verebilir (Admin ve Super Admin ASLA veremez)
                        $availableRoles = \Spatie\Permission\Models\Role::whereIn('name', ['Editor', 'Author'])->pluck('name', 'id');
                    }
                    elseif ($user->hasRole('Editor')) {
                        // Editor: Sadece Author verebilir
                        $availableRoles = \Spatie\Permission\Models\Role::where('name', 'Author')->pluck('name', 'id');
                    }
                    // Author hiçbir rol veremez

                    // Eğer başkasının profilini düzenliyorsa, mevcut rollerini de ekle (kaldırmasın diye)
                    // ANCAK Super Admin rolünü ASLA ekleme
                    if ($record && $user->id !== $record->id) {
                        $currentRoles = $record->roles->where('name', '!=', 'Super Admin')->pluck('name', 'id');

                        // Mevcut rolleri, izin verilen rollerle birleştir (duplicate olmadan)
                        foreach ($currentRoles as $roleId => $roleName) {
                            if (!$availableRoles->has($roleId)) {
                                $availableRoles->put($roleId, $roleName);
                            }
                        }
                    }

                    // Son güvenlik: Super Admin'i kesin olarak filtrele
                    $finalOptions = $availableRoles->reject(function($name, $id) {
                        return $name === 'Super Admin' || $id === 1; // ID 1 Super Admin
                    })->filter(function($name, $id) {
                        return $id > 0;
                    });

                    return $finalOptions;
                })
                ->default(function ($record) {
                    // Mevcut rolleri default olarak seç
                    if (!$record) {
                        return [];
                    }

                    return $record->roles->pluck('id')->toArray();
                })
                ->afterStateHydrated(function ($component, $state) {
                    $record = $component->getRecord();
                    if ($record) {
                        $currentRoles = $record->roles->pluck('id')->toArray();
                        $component->state($currentRoles);
                    }
                })
                ->saveRelationshipsUsing(function ($component, $state) {
                    $user = auth()->user();
                    $record = $component->getRecord();

                    // Super Admin kendi rolünü değiştirmeye çalışırsa engelle
                    if ($record->hasRole('Super Admin') && $user->id === $record->id) {
                        // Mevcut rolleri koru
                        return;
                    }

                    // 0 değerlerini filtrele (geçersiz rol ID'leri)
                    $validState = array_filter($state ?? [], function($roleId) {
                        return $roleId > 0 && \Spatie\Permission\Models\Role::where('id', $roleId)->exists();
                    });

                    // Rol hiyerarşisi kontrolü: Hangi rolleri verebilir?
                    $allowedRoleIds = collect();

                    if ($user->hasRole('Super Admin')) {
                        // Super Admin: Admin, Editor, Author verebilir (Super Admin veremez)
                        $allowedRoleIds = \Spatie\Permission\Models\Role::whereNotIn('name', ['Super Admin'])->pluck('id');
                    }
                    elseif ($user->hasRole('Admin')) {
                        // Admin: Editor, Author verebilir (Admin ve Super Admin veremez)
                        $allowedRoleIds = \Spatie\Permission\Models\Role::whereIn('name', ['Editor', 'Author'])->pluck('id');
                    }
                    elseif ($user->hasRole('Editor')) {
                        // Editor: Sadece Author verebilir
                        $allowedRoleIds = \Spatie\Permission\Models\Role::where('name', 'Author')->pluck('id');
                    }

                    // Sadece izin verilen rolleri filtrele
                    $authorizedRoles = collect($validState)->intersect($allowedRoleIds);

                    // Super Admin rolünü hiçbir zaman verme
                    $authorizedRoles = $authorizedRoles->reject(function($roleId) {
                        $role = \Spatie\Permission\Models\Role::find($roleId);
                        return $role && $role->name === 'Super Admin';
                    });

                    $record->roles()->sync($authorizedRoles->values()->toArray());
                })
                ->dehydrated(false),

            Forms\Components\DateTimePicker::make('last_login_at')
                ->disabled(),

            Forms\Components\TextInput::make('last_login_ip')
                ->disabled(),

            Forms\Components\DateTimePicker::make('email_verified_at')
                ->label('Email Verified At'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Super Admin' => 'danger',
                        'Admin'       => 'warning',
                        'Editor'      => 'info',
                        'Author'      => 'success',
                        default       => 'gray',
                    })
                    ->separator(',')
                    ->visible(fn () => auth()->user()->can('view roles')),

                Tables\Columns\IconColumn::make('google2fa_enabled')
                    ->label('2FA')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('google2fa_enabled')
                    ->label('2FA Enabled'),

                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Email Verified'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
}
