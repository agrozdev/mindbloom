<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
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
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Textarea::make('excerpt')
                    ->maxLength(500)
                    ->rows(5)
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->columnSpanFull(),
                FileUpload::make('featured_image')
                    ->image()
                    ->directory('blog'),
                DateTimePicker::make('published_at'),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('€')
                    ->minValue(0)
                    ->helperText('Оставете празно, ако историята е безплатна.'),
                RichEditor::make('story_info')
                    ->label('Story info (locked teaser)')
                    ->helperText('Shown instead of the full story when a price is set, together with a lock icon and payment button. Ignored for free stories.')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->default(false),
            ]);
    }
}
