<?php

namespace App\Filament\Resources\Readers\Schemas;

use App\Models\Reader;
use App\Models\StoryRead;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class ReaderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Читател')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')->label('Име'),
                        TextEntry::make('email')->label('Имейл')->copyable(),
                        TextEntry::make('status')
                            ->label('Статус')
                            ->badge()
                            ->state(fn (Reader $record) => $record->isUnsubscribed() ? 'отписан на '.$record->unsubscribed_at->format('d.m.Y') : 'активен')
                            ->color(fn (Reader $record) => $record->isUnsubscribed() ? 'gray' : 'success'),
                        TextEntry::make('created_at')->label('Регистриран на')->dateTime('d.m.Y H:i'),
                        TextEntry::make('signupStory.title')->label('Регистриран от приказка')->placeholder('—'),
                        TextEntry::make('consent_at')
                            ->label('Съгласие')
                            ->state(fn (Reader $record) => $record->consent_at
                                ? $record->consent_at->format('d.m.Y H:i').' (версия '.$record->consent_version.', IP '.$record->consent_ip.')'
                                : 'няма'),
                    ]),
                Section::make('Интереси')
                    ->description('Категории на приказките според времето, прекарано в четене.')
                    ->visible(fn (Reader $record) => self::interests($record) !== [])
                    ->schema([
                        TextEntry::make('interests')
                            ->hiddenLabel()
                            ->badge()
                            ->state(fn (Reader $record) => self::interests($record)),
                    ]),
                View::make('filament.readers.timeline'),
            ]);
    }

    /**
     * Top categories by active reading time, e.g. ["Магията на темперамента · 12 мин"].
     *
     * @return list<string>
     */
    public static function interests(Reader $record): array
    {
        return $record->storyReads()
            ->with('post.category')
            ->get()
            ->filter(fn (StoryRead $read) => $read->post?->category && $read->max_seconds > 0)
            ->groupBy(fn (StoryRead $read) => $read->post->category->name)
            ->map(fn ($reads) => $reads->sum('max_seconds'))
            ->sortDesc()
            ->take(5)
            ->map(fn (int $seconds, string $name) => $name.' · '.max(1, (int) round($seconds / 60)).' мин')
            ->values()
            ->all();
    }
}
