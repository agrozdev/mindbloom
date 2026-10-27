<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Models\Review;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Читатели';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Отзив';

    protected static ?string $pluralModelLabel = 'Отзиви';

    public static function getNavigationBadge(): ?string
    {
        $pending = Review::pending()->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['reader', 'post']))
            ->columns([
                TextColumn::make('reader.name')->label('Читател')->searchable()
                    ->url(fn (Review $record) => $record->reader ? route('filament.admin.resources.readers.view', $record->reader) : null),
                TextColumn::make('post.title')->label('Приказка')->limit(35)->searchable(),
                TextColumn::make('rating')->label('Оценка')->sortable()
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->color('warning'),
                TextColumn::make('answer_feeling')->label('Какво почувствахте')->limit(80)->wrap()
                    ->tooltip(fn (Review $record) => $record->answer_feeling)->placeholder('—'),
                TextColumn::make('answer_lesson')->label('Послание / урок')->limit(80)->wrap()
                    ->tooltip(fn (Review $record) => $record->answer_lesson)->placeholder('—'),
                IconColumn::make('can_publish')->label('Може да се публикува')->boolean(),
                TextColumn::make('status')->label('Статус')->badge()
                    ->formatStateUsing(fn (string $state) => Review::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        Review::STATUS_APPROVED => 'success',
                        Review::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->label('Дата')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Статус')->options(Review::STATUS_LABELS),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Одобри')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Review $record) => $record->status !== Review::STATUS_APPROVED)
                    ->modalDescription(fn (Review $record) => $record->can_publish
                        ? 'Отзивът ще се появи под приказката.'
                        : 'Читателят не е разрешил публикуване, затова отзивът няма да се покаже на сайта.')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update(['status' => Review::STATUS_APPROVED]);
                        Notification::make()->title('Отзивът е одобрен.')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Отхвърли')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('gray')
                    ->visible(fn (Review $record) => $record->status !== Review::STATUS_REJECTED)
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update(['status' => Review::STATUS_REJECTED]);
                        Notification::make()->title('Отзивът е отхвърлен.')->success()->send();
                    }),
                DeleteAction::make()->label('Изтрий'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
