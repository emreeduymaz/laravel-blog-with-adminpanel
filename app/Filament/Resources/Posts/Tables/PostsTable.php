<?php

namespace App\Filament\Resources\Posts\Tables;

use Illuminate\Support\Str;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->size(60)
                    ->square(),
                    
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn ($record) => $record->excerpt ? 
                        Str::limit($record->excerpt, 60) : null
                    ),
                    
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                    
                TextColumn::make('category.name')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? '#6366f1')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('tags.name')
                    ->badge()
                    ->color(fn ($record, $state) => 
                        $record->tags->where('name', $state)->first()?->color ?? '#10b981'
                    )
                    ->separator(',')
                    ->limit(2),
                    
                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('Not published'),
                    
                TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                    
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
                    ]),
                    
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('is_featured')
                    ->label('Featured Posts')
                    ->options([
                        1 => 'Featured',
                        0 => 'Not Featured',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => route('blog.show', $record->slug))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
