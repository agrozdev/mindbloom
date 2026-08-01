<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EventForm
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
                    ->rows(2)
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->directory('events'),
                DateTimePicker::make('starts_at')
                    ->required(),
                TextInput::make('location')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
