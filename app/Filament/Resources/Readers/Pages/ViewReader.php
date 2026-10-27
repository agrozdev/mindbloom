<?php

namespace App\Filament\Resources\Readers\Pages;

use App\Filament\Resources\Readers\Actions\SendReaderEmailAction;
use App\Filament\Resources\Readers\ReaderResource;
use App\Filament\Resources\Readers\Tables\ReadersTable;
use Filament\Resources\Pages\ViewRecord;

class ViewReader extends ViewRecord
{
    protected static string $resource = ReaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SendReaderEmailAction::make(),
            ReadersTable::deleteAction(),
        ];
    }
}
