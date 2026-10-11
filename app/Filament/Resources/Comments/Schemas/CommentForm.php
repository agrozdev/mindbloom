<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Име')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->maxLength(255),
                Textarea::make('body')
                    ->label('Коментар')
                    ->required()
                    ->maxLength(3000)
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }
}
