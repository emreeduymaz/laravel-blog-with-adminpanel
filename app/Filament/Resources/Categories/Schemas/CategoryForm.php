<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                $context === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                            
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                            
                        Textarea::make('description')
                            ->rows(3),
                            
                        ColorPicker::make('color')
                            ->default('#6366f1'),
                            
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
