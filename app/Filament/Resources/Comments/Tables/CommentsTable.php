<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('post.title')->label('История')->searchable()->limit(40),
                TextColumn::make('name')->label('Име')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('body')->label('Коментар')->limit(80)->wrap()->tooltip(fn (Comment $record) => $record->body),
                TextColumn::make('approved_at')
                    ->label('Статус')
                    ->badge()
                    ->state(fn (Comment $record) => $record->isApproved() ? 'Одобрен' : 'Чака одобрение')
                    ->color(fn (Comment $record) => $record->isApproved() ? 'success' : 'warning'),
                TextColumn::make('created_at')->label('Получен на')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('approved_at')
                    ->label('Статус')
                    ->nullable()
                    ->trueLabel('Одобрени')
                    ->falseLabel('Чакащи одобрение'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Одобри')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Comment $record) => ! $record->isApproved())
                    ->requiresConfirmation()
                    ->modalDescription('Коментарът ще се появи на сайта, а читателят ще получи имейл с благодарност.')
                    ->action(fn (Comment $record) => $record->approve()),
                Action::make('unapprove')
                    ->label('Скрий')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('gray')
                    ->visible(fn (Comment $record) => $record->isApproved())
                    ->requiresConfirmation()
                    ->action(fn (Comment $record) => $record->unapprove()),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Одобри избраните')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Избраните коментари ще се появят на сайта, а читателите ще получат имейл с благодарност.')
                        ->action(fn (Collection $records) => $records->each->approve())
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
