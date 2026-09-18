<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopOrderResource\Pages;
use App\Filament\Resources\ShopOrderResource\RelationManagers;
use App\Models\ShopOrder;
use App\Services\Shop\ShopOrderStatusService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Throwable;

class ShopOrderResource extends Resource
{
    protected static ?string $model = ShopOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Webshop';

    protected static ?string $navigationLabel = 'Narudžbe';

    protected static ?string $modelLabel = 'narudžba';

    protected static ?string $pluralModelLabel = 'narudžbe';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Narudžba')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Broj narudžbe')
                            ->disabled(),

                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->formatStateUsing(
                                fn($state) => self::getStatusLabel($state)
                            )
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('Način plaćanja')
                            ->formatStateUsing(
                                fn($state) => $state === ShopOrder::PAYMENT_COD
                                    ? 'Plaćanje pouzećem'
                                    : $state
                            )
                            ->disabled(),

                        Forms\Components\TextInput::make('delivery_method')
                            ->label('Dostava')
                            ->formatStateUsing(
                                fn($state) => match ($state) {
                                    ShopOrder::DELIVERY_COURIER => 'Kurirska dostava',
                                    ShopOrder::DELIVERY_PICKUP => 'Lično preuzimanje',
                                    default => $state,
                                }
                            )
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kupac')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Ime')
                            ->disabled(),

                        Forms\Components\TextInput::make('last_name')
                            ->label('Prezime')
                            ->disabled(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->disabled(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->disabled(),

                        Forms\Components\TextInput::make('address')
                            ->label('Adresa')
                            ->disabled(),

                        Forms\Components\TextInput::make('city')
                            ->label('Grad')
                            ->disabled(),

                        Forms\Components\TextInput::make('postal_code')
                            ->label('Poštanski broj')
                            ->disabled(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Napomena kupca')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Popust')
                    ->schema([
                        Forms\Components\TextInput::make('discount_type')
                            ->label('Tip popusta')
                            ->formatStateUsing(
                                fn($state) => match ($state) {
                                    ShopOrder::DISCOUNT_SEASON_TICKET => 'Sezonska karta',
                                    ShopOrder::DISCOUNT_VOUCHER => 'Promo vaučer',
                                    default => 'Bez popusta',
                                }
                            )
                            ->disabled(),

                        Forms\Components\TextInput::make('season_ticket_number')
                            ->label('Sezonska karta')
                            ->disabled(),

                        Forms\Components\TextInput::make('voucher_code')
                            ->label('Promo kod')
                            ->disabled(),

                        Forms\Components\TextInput::make('discount_percent')
                            ->label('Popust (%)')
                            ->suffix('%')
                            ->disabled(),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Iznos popusta')
                            ->suffix('KM')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Finansije')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Međuzbir')
                            ->suffix('KM')
                            ->disabled(),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Popust')
                            ->suffix('KM')
                            ->disabled(),

                        Forms\Components\TextInput::make('shipping_amount')
                            ->label('Dostava')
                            ->suffix('KM')
                            ->disabled(),

                        Forms\Components\TextInput::make('total')
                            ->label('UKUPNO')
                            ->suffix('KM')
                            ->disabled(),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Status narudžbe')
                    ->schema([
                        Forms\Components\Placeholder::make('created_status')
                            ->label('Kreirana')
                            ->content(
                                fn(?ShopOrder $record) => $record?->created_at
                                    ? $record->created_at->format('d.m.Y. H:i')
                                    : '-'
                            ),

                        Forms\Components\Placeholder::make('confirmed_status')
                            ->label('Potvrđena')
                            ->content(
                                fn(?ShopOrder $record) => $record?->confirmed_at
                                    ? $record->confirmed_at->format('d.m.Y. H:i')
                                    : '-'
                            ),

                        Forms\Components\Placeholder::make('shipped_status')
                            ->label('Poslata')
                            ->content(
                                fn(?ShopOrder $record) => $record?->shipped_at
                                    ? $record->shipped_at->format('d.m.Y. H:i')
                                    : '-'
                            ),

                        Forms\Components\Placeholder::make('delivered_status')
                            ->label('Isporučena')
                            ->content(
                                fn(?ShopOrder $record) => $record?->delivered_at
                                    ? $record->delivered_at->format('d.m.Y. H:i')
                                    : '-'
                            ),

                        Forms\Components\Placeholder::make('cancelled_status')
                            ->label('Otkazana')
                            ->content(
                                fn(?ShopOrder $record) => $record?->cancelled_at
                                    ? $record->cancelled_at->format('d.m.Y. H:i')
                                    : '-'
                            ),
                    ])
                    ->columns(5)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Narudžba')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer')
                    ->label('Kupac')
                    ->getStateUsing(
                        fn(ShopOrder $record): string => trim(
                            $record->first_name . ' ' . $record->last_name
                        )
                    )
                    ->searchable([
                        'first_name',
                        'last_name',
                    ]),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn(string $state): string => self::getStatusLabel($state)
                    )
                    ->color(
                        fn(string $state): string => self::getStatusColor($state)
                    ),

                Tables\Columns\TextColumn::make('total')
                    ->label('Ukupno')
                    ->money('BAM')
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Artikala')
                    ->counts('items'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Datum')
                    ->dateTime('d.m.Y. H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        ShopOrder::STATUS_PENDING => 'Na čekanju',
                        ShopOrder::STATUS_CONFIRMED => 'Potvrđene',
                        ShopOrder::STATUS_PROCESSING => 'U obradi',
                        ShopOrder::STATUS_SHIPPED => 'Poslate',
                        ShopOrder::STATUS_DELIVERED => 'Isporučene',
                        ShopOrder::STATUS_CANCELLED => 'Otkazane',
                    ]),

                Tables\Filters\SelectFilter::make('delivery_method')
                    ->label('Dostava')
                    ->options([
                        ShopOrder::DELIVERY_COURIER => 'Kurirska dostava',
                        ShopOrder::DELIVERY_PICKUP => 'Lično preuzimanje',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Otvori'),

                Tables\Actions\Action::make('confirm')
                    ->label('Potvrdi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Potvrdi narudžbu')
                    ->modalDescription(
                        'Rezervisana roba će biti skinuta sa fizičkog stanja.'
                    )
                    ->visible(
                        fn(ShopOrder $record): bool => $record->status === ShopOrder::STATUS_PENDING
                    )
                    ->action(
                        fn(ShopOrder $record) => self::changeOrderStatus(
                            $record,
                            ShopOrder::STATUS_CONFIRMED,
                            'Narudžba je potvrđena.'
                        )
                    ),

                Tables\Actions\Action::make('processing')
                    ->label('U obradu')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(
                        fn(ShopOrder $record): bool => $record->status === ShopOrder::STATUS_CONFIRMED
                    )
                    ->action(
                        fn(ShopOrder $record) => self::changeOrderStatus(
                            $record,
                            ShopOrder::STATUS_PROCESSING,
                            'Narudžba je stavljena u obradu.'
                        )
                    ),

                Tables\Actions\Action::make('ship')
                    ->label('Pošalji')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(
                        fn(ShopOrder $record): bool => $record->status === ShopOrder::STATUS_PROCESSING
                    )
                    ->action(
                        fn(ShopOrder $record) => self::changeOrderStatus(
                            $record,
                            ShopOrder::STATUS_SHIPPED,
                            'Narudžba je označena kao poslata.'
                        )
                    ),

                Tables\Actions\Action::make('deliver')
                    ->label('Isporučeno')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn(ShopOrder $record): bool => $record->status === ShopOrder::STATUS_SHIPPED
                    )
                    ->action(
                        fn(ShopOrder $record) => self::changeOrderStatus(
                            $record,
                            ShopOrder::STATUS_DELIVERED,
                            'Narudžba je označena kao isporučena.'
                        )
                    ),

                Tables\Actions\Action::make('cancel')
                    ->label('Otkaži')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Otkaži narudžbu')
                    ->modalDescription(
                        'Ova akcija će osloboditi rezervisanu robu ili vratiti prodatu robu na stanje.'
                    )
                    ->visible(
                        fn(ShopOrder $record): bool => in_array(
                            $record->status,
                            [
                                ShopOrder::STATUS_PENDING,
                                ShopOrder::STATUS_CONFIRMED,
                                ShopOrder::STATUS_PROCESSING,
                            ],
                            true
                        )
                    )
                    ->action(
                        fn(ShopOrder $record) => self::changeOrderStatus(
                            $record,
                            ShopOrder::STATUS_CANCELLED,
                            'Narudžba je otkazana.'
                        )
                    ),
            ])
            ->bulkActions([]);
    }

    private static function changeOrderStatus(
        ShopOrder $record,
        string    $newStatus,
        string    $successMessage
    ): void
    {
        try {
            app(ShopOrderStatusService::class)
                ->changeStatus(
                    $record,
                    $newStatus
                );

            Notification::make()
                ->title($successMessage)
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Promjena statusa nije izvršena')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    private static function getStatusLabel(?string $status): string
    {
        return match ($status) {
            ShopOrder::STATUS_PENDING => 'Na čekanju',
            ShopOrder::STATUS_CONFIRMED => 'Potvrđena',
            ShopOrder::STATUS_PROCESSING => 'U obradi',
            ShopOrder::STATUS_SHIPPED => 'Poslata',
            ShopOrder::STATUS_DELIVERED => 'Isporučena',
            ShopOrder::STATUS_CANCELLED => 'Otkazana',
            default => $status ?: '-',
        };
    }

    private static function getStatusColor(string $status): string
    {
        return match ($status) {
            ShopOrder::STATUS_PENDING => 'warning',
            ShopOrder::STATUS_CONFIRMED => 'info',
            ShopOrder::STATUS_PROCESSING => 'primary',
            ShopOrder::STATUS_SHIPPED => 'gray',
            ShopOrder::STATUS_DELIVERED => 'success',
            ShopOrder::STATUS_CANCELLED => 'danger',
            default => 'gray',
        };
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShopOrders::route('/'),
            'edit' => Pages\EditShopOrder::route('/{record}/edit'),
        ];
    }
}
