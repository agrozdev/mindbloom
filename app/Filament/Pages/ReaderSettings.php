<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ReaderSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Читатели';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Настройки';

    protected static ?string $title = 'Настройки на читателите';

    protected static ?string $slug = 'reader-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(collect(Setting::DEFAULTS)->mapWithKeys(fn ($default, $key) => [$key => Setting::get($key)])->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Изпращач')
                    ->description('Празно поле = стойността от .env (MAIL_FROM_NAME / MAIL_FROM_ADDRESS).')
                    ->columns(3)
                    ->schema([
                        TextInput::make('mail_from_name')->label('Име на изпращача')->placeholder(config('mail.from.name')),
                        TextInput::make('mail_from_address')->label('Имейл на изпращача')->email()->placeholder(config('mail.from.address')),
                        TextInput::make('mail_reply_to')->label('Отговор до (Reply-To)')->email(),
                    ]),
                Section::make('Четене')
                    ->columns(3)
                    ->schema([
                        TextInput::make('read_opened_seconds')->label('„Отворена“ след (сек)')->integer()->minValue(1)->required(),
                        TextInput::make('read_min_seconds')->label('„Прочетена“ след (сек)')->integer()->minValue(1)->required(),
                        TextInput::make('read_min_scroll')->label('„Прочетена“ при скрол (%)')->integer()->minValue(1)->maxValue(100)->required(),
                        TextInput::make('gate_visible_percent')->label('Видима част преди формата (%)')->integer()->minValue(5)->maxValue(95)->required(),
                        TextInput::make('privacy_policy_url')->label('Политика за поверителност (URL)')->url()->placeholder(route('legal.privacy'))->columnSpan(2),
                    ]),
                Section::make('Cloudflare Turnstile (по желание)')
                    ->description('Попълнете и двата ключа, за да се появи проверката във формата. Празни = изключено.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('turnstile_site_key')->label('Site key'),
                        TextInput::make('turnstile_secret_key')->label('Secret key')->password()->revealable(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Запази')->submit('save'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        Setting::putMany($this->form->getState());

        Notification::make()->title('Настройките са запазени.')->success()->send();
    }
}
