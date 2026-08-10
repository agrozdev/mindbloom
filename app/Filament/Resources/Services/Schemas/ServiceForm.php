<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255)
                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create'
                        ? $set('slug', \Illuminate\Support\Str::slug($state))
                        : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('excerpt')
                    ->maxLength(500)
                    ->rows(5)
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                TextInput::make('icon')
                    ->helperText('CSS icon class from the theme, e.g. "lnr-heart"')
                    ->maxLength(255),
                FileUpload::make('image')
                    ->image()
                    ->directory('services'),
                Toggle::make('is_active')
                    ->default(true),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
