<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string | \UnitEnum | null $navigationGroup = 'Activity';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view activity logs') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '-')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('changes')
                    ->label('Changes')
                    ->limit(80)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Action')
                    ->options(function (): array {
                        return ActivityLog::query()
                            ->select('action')
                            ->distinct()
                            ->orderBy('action')
                            ->pluck('action', 'action')
                            ->all();
                    }),
                Tables\Filters\SelectFilter::make('model')
                    ->label('Model')
                    ->options(function (): array {
                        return ActivityLog::query()
                            ->select('model')
                            ->distinct()
                            ->orderBy('model')
                            ->get()
                            ->mapWithKeys(fn ($r) => [$r->model => class_basename((string) $r->model)])
                            ->all();
                    }),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordUrl(null)
            ->recordAction(null)
            ->actions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }
}


