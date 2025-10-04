<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                $context === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                            
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                            
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Brief description of the post (optional - will be auto-generated from content if empty)'),
                            
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                                'undo',
                                'redo',
                            ]),
                    ]),
                    
                Section::make('Media')
                    ->schema([
                        FileUpload::make('featured_image')
                            ->image()
                            ->imageEditor()
                            ->directory('posts')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('Publishing')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'scheduled' => 'Scheduled',
                            ])
                            ->default('draft')
                            ->required()
                            ->live(),
                            
                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->visible(fn (callable $get) => in_array($get('status'), ['published', 'scheduled'])),
                            
                        Toggle::make('is_featured')
                            ->label('Featured Post'),
                            
                        TextInput::make('views_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically updated when users view the post'),
                    ]),
                    
                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id())
                            ->searchable()
                            ->preload(),
                            
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                        $context === 'create' ? $set('slug', Str::slug($state)) : null
                                    ),
                                TextInput::make('slug')
                                    ->required(),
                                Textarea::make('description'),
                                ColorPicker::make('color')
                                    ->default('#6366f1'),
                                Toggle::make('is_active')
                                    ->default(true),
                            ]),
                            
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                        $context === 'create' ? $set('slug', Str::slug($state)) : null
                                    ),
                                TextInput::make('slug')
                                    ->required(),
                                ColorPicker::make('color')
                                    ->default('#10b981'),
                            ])
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('SEO')
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta.title')
                            ->label('SEO Title')
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters'),
                            
                        Textarea::make('meta.description')
                            ->label('SEO Description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Recommended: 150-160 characters'),
                            
                        TextInput::make('meta.keywords')
                            ->label('SEO Keywords')
                            ->helperText('Comma-separated keywords'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
