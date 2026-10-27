<?php

namespace App\Filament\Resources\EmailTemplates;

use App\Filament\Resources\EmailTemplates\Pages\CreateEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Models\EmailTemplate;
use App\Services\Readers\ReaderMailer;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Читатели';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Шаблон';

    protected static ?string $pluralModelLabel = 'Шаблони';

    protected static ?string $slug = 'email-templates';

    public static function form(Schema $schema): Schema
    {
        $placeholders = collect(ReaderMailer::PLACEHOLDERS)
            ->map(fn (string $description, string $tag) => '<li><code>'.e($tag).'</code> – '.e($description).'</li>')
            ->implode('');

        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')->label('Име')->required()->maxLength(255),
                TextInput::make('subject')->label('Тема')->required()->maxLength(200),
                RichEditor::make('body')
                    ->label('Съдържание')
                    ->required()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'link'],
                        ['h2', 'h3'],
                        ['bulletList', 'orderedList', 'blockquote'],
                        ['undo', 'redo'],
                    ]),
                Html::make(new HtmlString(
                    '<div style="font-size:.875rem; line-height:1.6; opacity:.85;"><strong>Можете да използвате:</strong><ul style="list-style:disc; padding-left:1.25rem; margin:.25rem 0;">'
                    .$placeholders.'</ul>'.e(ReaderMailer::BUTTON_HINT).'</div>'
                )),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Име')->searchable()->sortable(),
                TextColumn::make('subject')->label('Тема')->limit(60),
                TextColumn::make('updated_at')->label('Променен на')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
