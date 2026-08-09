<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('uuid')->label('Поръчка')->limit(8)->fontFamily('mono'),
                TextColumn::make('orderable.title')->label('Артикул')->searchable(),
                TextColumn::make('name')->label('Име')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('amount')->money('EUR')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_PAID => 'success',
                        Order::STATUS_FAILED, Order::STATUS_CANCELLED => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('paid_at')->dateTime()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    Order::STATUS_PENDING => 'Изчаква',
                    Order::STATUS_PAID => 'Платена',
                    Order::STATUS_FAILED => 'Неуспешна',
                    Order::STATUS_CANCELLED => 'Отказана',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
