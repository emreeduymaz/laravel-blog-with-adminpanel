<?php

namespace App\Filament\Resources\Comments;

use App\Filament\Resources\Comments\Pages\CreateComment;
use App\Filament\Resources\Comments\Pages\EditComment;
use App\Filament\Resources\Comments\Pages\ListComments;
use App\Models\Comment;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('post_id')
                ->relationship('post', 'title')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->nullable(),
            Forms\Components\TextInput::make('name')->maxLength(255)->nullable(),
            Forms\Components\TextInput::make('email')->email()->maxLength(255)->nullable(),
            Forms\Components\Textarea::make('content')->rows(5)->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'spam' => 'Spam',
                ])->required(),
            Forms\Components\DateTimePicker::make('approved_at')->nullable(),
            Forms\Components\TextInput::make('ip_address')->disabled(),
            Forms\Components\TextInput::make('user_agent')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')->label('Post')->searchable(),
                Tables\Columns\TextColumn::make('name')->toggleable(),
                Tables\Columns\TextColumn::make('email')->toggleable(),
                Tables\Columns\TextColumn::make('content')->limit(80)->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'spam' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'spam' => 'Spam',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComments::route('/'),
            'create' => CreateComment::route('/create'),
            'edit' => EditComment::route('/{record}/edit'),
        ];
    }
}


