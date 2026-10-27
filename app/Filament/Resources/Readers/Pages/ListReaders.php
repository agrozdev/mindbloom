<?php

namespace App\Filament\Resources\Readers\Pages;

use App\Filament\Resources\Readers\ReaderResource;
use App\Models\Reader;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListReaders extends ListRecords
{
    protected static string $resource = ReaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Експорт CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn () => $this->exportCsv()),
        ];
    }

    /**
     * Exports what the table currently shows (search + filters + sort).
     */
    private function exportCsv(): StreamedResponse
    {
        $query = $this->getFilteredSortedTableQuery();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads Cyrillic correctly

            fputcsv($out, ['Име', 'Имейл', 'Регистриран на', 'Прочетени', 'Отворени', 'Последна активност', 'Последен имейл', 'Отзиви', 'Статус', 'Съгласие на']);

            $query->chunk(500, function ($readers) use ($out) {
                foreach ($readers as $reader) {
                    /** @var Reader $reader */
                    fputcsv($out, [
                        $reader->name,
                        $reader->email,
                        $reader->created_at?->format('Y-m-d H:i'),
                        $reader->read_count,
                        $reader->opened_count,
                        $reader->last_activity_at,
                        $reader->last_email_at,
                        $reader->reviews_count,
                        $reader->isUnsubscribed() ? 'отписан' : 'активен',
                        $reader->consent_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, 'chitateli-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
