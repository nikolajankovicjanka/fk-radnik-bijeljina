<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopSettingResource\Pages;
use App\Models\ShopSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShopSettingResource extends Resource
{
    protected static ?string $model = ShopSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Webshop';

    protected static ?string $navigationLabel = 'Postavke';

    protected static ?string $modelLabel = 'postavka';

    protected static ?string $pluralModelLabel = 'postavke';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Webshop postavka')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Ključ')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('value')
                            ->label('Vrijednost')
                            ->required(
                                fn(?ShopSetting $record): bool => $record?->key !== 'shop_email'
                            )
                            ->helperText(
                                fn(?ShopSetting $record): ?string => static::getSettingDescription($record?->key)
                            ),

                        Forms\Components\TextInput::make('type')
                            ->label('Tip')
                            ->disabled()
                            ->dehydrated(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Postavka')
                    ->formatStateUsing(
                        fn(string $state): string => static::getSettingLabel($state)
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Vrijednost')
                    ->formatStateUsing(
                        fn($state, ShopSetting $record): string => static::formatSettingValue($record)
                    ),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Izmijenjeno')
                    ->dateTime('d.m.Y. H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Uredi'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShopSettings::route('/'),
            'edit' => Pages\EditShopSetting::route('/{record}/edit'),
        ];
    }

    private static function getSettingLabel(string $key): string
    {
        return match ($key) {
            'current_season' => 'Trenutna sezona',
            'season_ticket_discount_enabled' => 'Popust za sezonske karte',
            'season_ticket_discount_percent' => 'Popust za sezonsku kartu (%)',
            'shipping_price' => 'Cijena dostave',
            'free_shipping_threshold' => 'Besplatna dostava od',
            'shop_email' => 'Email za narudžbe',
            'orders_enabled' => 'Primanje narudžbi',
            default => $key,
        };
    }

    private static function getSettingDescription(?string $key): ?string
    {
        return match ($key) {
            'current_season' =>
            'Aktuelna sezona koja se koristi za provjeru sezonskih karata, npr. 2026/27.',

            'season_ticket_discount_enabled' =>
            '1 = popust je uključen, 0 = popust je isključen.',

            'season_ticket_discount_percent' =>
            'Procenat popusta koji ostvaruje validna sezonska karta.',

            'shipping_price' =>
            'Cijena kurirske dostave u KM.',

            'free_shipping_threshold' =>
            'Vrijednost robe od koje je dostava besplatna. Vrijednost 0 ćemo tretirati kao isključenu opciju.',

            'shop_email' =>
            'Email FK Radnika na koji će stizati obavještenja o novim narudžbama.',

            'orders_enabled' =>
            '1 = webshop prima narudžbe, 0 = checkout je privremeno isključen.',

            default => null,
        };
    }

    private static function formatSettingValue(ShopSetting $record): string
    {
        if ($record->type === 'boolean') {
            return filter_var($record->value, FILTER_VALIDATE_BOOLEAN)
                ? 'Uključeno'
                : 'Isključeno';
        }

        if (
            in_array(
                $record->key,
                ['shipping_price', 'free_shipping_threshold'],
                true
            )
        ) {
            return number_format((float)$record->value, 2, ',', '.') . ' KM';
        }

        if ($record->key === 'season_ticket_discount_percent') {
            return rtrim(
                    rtrim(
                        number_format((float)$record->value, 2, '.', ''),
                        '0'
                    ),
                    '.'
                ) . '%';
        }

        return $record->value !== null && $record->value !== ''
            ? (string)$record->value
            : '—';
    }
}
