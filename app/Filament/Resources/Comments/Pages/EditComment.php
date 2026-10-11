<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Filament\Resources\Comments\CommentResource;
use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Одобри')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->visible(fn (Comment $record) => ! $record->isApproved())
                ->requiresConfirmation()
                ->modalDescription('Промените ще бъдат запазени, коментарът ще се появи на сайта, а читателят ще получи имейл с благодарност.')
                ->action(function (Comment $record) {
                    // Save any edits first, so the published text is what's on screen.
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);
                    $record->refresh()->approve();

                    Notification::make()->title('Коментарът е одобрен и публикуван')->success()->send();
                }),
            Action::make('unapprove')
                ->label('Скрий')
                ->icon(Heroicon::OutlinedEyeSlash)
                ->color('gray')
                ->visible(fn (Comment $record) => $record->isApproved())
                ->requiresConfirmation()
                ->action(function (Comment $record) {
                    $record->unapprove();

                    Notification::make()->title('Коментарът е скрит от сайта')->success()->send();
                }),
            DeleteAction::make(),
        ];
    }
}
