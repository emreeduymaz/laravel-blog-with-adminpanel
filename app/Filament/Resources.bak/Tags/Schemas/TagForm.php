<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tag Details')
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
                            
                        ColorPicker::make('color')
                            ->default('#10b981'),
                    ]),
            ]);
    }
}
