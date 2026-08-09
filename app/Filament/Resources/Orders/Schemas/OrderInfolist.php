<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('uuid')->label('Номер на поръчка'),
                TextEntry::make('orderable.title')->label('Артикул'),
                TextEntry::make('name')->label('Име'),
                TextEntry::make('email'),
                TextEntry::make('phone')->label('Телефон'),
                TextEntry::make('amount')->label('Сума')->money('EUR'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_PAID => 'success',
                        Order::STATUS_FAILED, Order::STATUS_CANCELLED => 'danger',
                        default => 'warning',
                    }),
                TextEntry::make('transaction_id')->label('Транзакция'),
                TextEntry::make('paid_at')->label('Дата на плащане')->dateTime(),
                TextEntry::make('created_at')->label('Създадена на')->dateTime(),
            ]);
    }
}
