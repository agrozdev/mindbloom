<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255)
                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create'
                        ? $set('slug', Str::slug($state, '-', 'bg'))
                        : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('meta_description')
                    ->label('Meta description')
                    ->helperText('Shown in Google search results for this category page. Keep it specific to what\'s actually in this category, not a generic sentence. ~150-160 characters is the sweet spot.')
                    ->maxLength(255)
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
