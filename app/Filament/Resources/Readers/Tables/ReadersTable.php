<?php

namespace App\Filament\Resources\Readers\Tables;

use App\Filament\Resources\Readers\Actions\SendReaderEmailAction;
use App\Models\Post;
use App\Models\Reader;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ReadersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Име')->searchable()->sortable(),
                TextColumn::make('email')->label('Имейл')->searchable()->sortable()->copyable(),
                TextColumn::make('created_at')->label('Регистриран на')->date('d.m.Y')->sortable(),
                TextColumn::make('read_count')
                    ->label('Прочетени приказки')
                    ->state(fn (Reader $record) => $record->read_count.' / '.$record->opened_count)
                    ->tooltip('прочетени / отворени')
                    ->sortable(['read_count']),
                TextColumn::make('last_activity_at')
                    ->label('Последна активност')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->diffForHumans() : '—')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('last_email_at')
                    ->label('Последен имейл')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->diffForHumans() : '—')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('reviews_count')->label('Отзиви')->sortable(),
                TextColumn::make('unsubscribed_at')
                    ->label('Статус')
                    ->badge()
                    ->state(fn (Reader $record) => $record->isUnsubscribed() ? 'отписан' : 'активен')
                    ->color(fn (Reader $record) => $record->isUnsubscribed() ? 'gray' : 'success'),
            ])
            ->filters([
                SelectFilter::make('story')
                    ->label('Приказка')
                    ->options(fn () => Post::query()->orderBy('title')->pluck('title', 'id'))
                    ->searchable()
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereHas('storyReads', fn (Builder $reads) => $reads->where('post_id', $data['value']))
                        : $query),
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(['active' => 'активен', 'unsubscribed' => 'отписан'])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'active' => $query->whereNull('unsubscribed_at'),
                        'unsubscribed' => $query->whereNotNull('unsubscribed_at'),
                        default => $query,
                    }),
                TernaryFilter::make('has_feedback')
                    ->label('Има отзив')
                    ->trueLabel('Да')
                    ->falseLabel('Не')
                    ->queries(
                        true: fn (Builder $query) => $query->has('reviews'),
                        false: fn (Builder $query) => $query->doesntHave('reviews'),
                    ),
            ])
            ->recordUrl(fn (Reader $record) => route('filament.admin.resources.readers.view', $record))
            ->recordActions([
                ViewAction::make()->label('Детайли'),
                SendReaderEmailAction::make(),
                self::deleteAction(),
            ]);
    }

    public static function deleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->label('Изтрий')
            ->modalHeading(fn (Reader $record) => 'Изтриване на '.$record->name)
            ->modalDescription('Читателят и всички свързани данни (прочитания, изпратени имейли, отзиви) ще бъдат изтрити завинаги. Това не може да бъде отменено.')
            ->modalSubmitActionLabel('Да, изтрий всичко')
            ->successNotificationTitle('Читателят и всички негови данни са изтрити.');
    }
}
