<?php

namespace App\Filament\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = null;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),

                        FileUpload::make('avatar')
                            ->image()
                            ->imageEditor()
                            ->directory('avatars')
                            ->visibility('public'),

                        Textarea::make('bio')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Security')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Toggle::make('google2fa_enabled')
                            ->label('2FA Enabled')
                            ->disabled()
                            ->helperText('Users can enable 2FA from their profile'),
                    ]),

                Section::make('Roles & Permissions')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columnSpanFull(),

                Section::make('Login Information')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('last_login_at')
                            ->disabled(),

                        TextInput::make('last_login_ip')
                            ->disabled(),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->circular()
                    ->size(40),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Super Admin' => 'danger',
                        'Admin' => 'warning',
                        'Editor' => 'info',
                        'Author' => 'success',
                        default => 'gray',
                    })
                    ->separator(','),

                IconColumn::make('google2fa_enabled')
                    ->label('2FA')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('google2fa_enabled')
                    ->label('2FA Enabled'),

                Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Email Verified'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}