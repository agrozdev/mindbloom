<?php

namespace App\Filament\Resources\Readers;

use App\Filament\Resources\Readers\Pages\ListReaders;
use App\Filament\Resources\Readers\Pages\ViewReader;
use App\Filament\Resources\Readers\Schemas\ReaderInfolist;
use App\Filament\Resources\Readers\Tables\ReadersTable;
use App\Models\Reader;
use App\Models\StoryRead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ReaderResource extends Resource
{
    protected static ?string $model = Reader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Читатели';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Читател';

    protected static ?string $pluralModelLabel = 'Читатели';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount([
                'storyReads as read_count' => fn (Builder $query) => $query->where('status', StoryRead::STATUS_READ),
                'storyReads as opened_count' => fn (Builder $query) => $query->whereIn('status', [StoryRead::STATUS_OPENED, StoryRead::STATUS_READ]),
                'reviews',
            ])
            ->withMax('storyReads as last_activity_at', 'last_seen')
            ->withMax(['emailLogs as last_email_at' => fn (Builder $query) => $query->where('status', 'sent')], 'sent_at');
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReaderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReadersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReaders::route('/'),
            'view' => ViewReader::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
