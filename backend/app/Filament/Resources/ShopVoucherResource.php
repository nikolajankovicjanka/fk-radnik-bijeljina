<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopVoucherResource\Pages;
use App\Models\ShopVoucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ShopVoucherResource extends Resource
{
    protected static ?string $model = ShopVoucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Webshop';

    protected static ?string $navigationLabel = 'Promo vaučeri';

    protected static ?string $modelLabel = 'promo vaučer';

    protected static ?string $pluralModelLabel = 'promo vaučeri';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Osnovni podaci')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Naziv')
                            ->placeholder('npr. Ljetna akcija')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('Kod vaučera')
                            ->placeholder('npr. RADNIK20')
                            ->helperText('Kod koji kupac unosi prilikom narudžbe.')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(
                                fn(?string $state): string => Str::upper(trim($state ?? ''))
                            ),

                        Forms\Components\Textarea::make('description')
                            ->label('Opis')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktivan')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Popust')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Tip popusta')
                            ->options([
                                ShopVoucher::TYPE_PERCENTAGE => 'Procentualni popust',
                                ShopVoucher::TYPE_FIXED => 'Fiksni iznos',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\TextInput::make('discount_value')
                            ->label(
                                fn(Forms\Get $get): string => $get('discount_type') === ShopVoucher::TYPE_FIXED
                                    ? 'Iznos popusta'
                                    : 'Procenat popusta'
                            )
                            ->numeric()
                            ->minValue(0.01)
                            ->suffix(
                                fn(Forms\Get $get): string => $get('discount_type') === ShopVoucher::TYPE_FIXED
                                    ? 'KM'
                                    : '%'
                            )
                            ->required(),

                        Forms\Components\TextInput::make('minimum_order_amount')
                            ->label('Minimalna vrijednost narudžbe')
                            ->helperText('Ostaviti prazno ako nema minimalnog iznosa.')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('KM'),

                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Maksimalan broj korištenja')
                            ->helperText('Ostaviti prazno za neograničeno korištenje.')
                            ->numeric()
                            ->integer()
                            ->minValue(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Period važenja')
                    ->description('Polja mogu ostati prazna ako vaučer nema vremensko ograničenje.')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Važi od')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Važi do')
                            ->seconds(false)
                            ->after('starts_at'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naziv')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Kod')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Popust')
                    ->formatStateUsing(function ($state, ShopVoucher $record): string {
                        if ($record->discount_type === ShopVoucher::TYPE_PERCENTAGE) {
                            return rtrim(rtrim(number_format((float)$state, 2, '.', ''), '0'), '.')
                                . '%';
                        }

                        return number_format((float)$state, 2, ',', '.')
                            . ' KM';
                    }),

                Tables\Columns\TextColumn::make('minimum_order_amount')
                    ->label('Min. narudžba')
                    ->formatStateUsing(
                        fn($state): string => $state !== null
                            ? number_format((float)$state, 2, ',', '.') . ' KM'
                            : '—'
                    ),

                Tables\Columns\TextColumn::make('usages_count')
                    ->label('Iskorišteno')
                    ->counts('usages')
                    ->formatStateUsing(function ($state, ShopVoucher $record): string {
                        $used = (int)$state;

                        return $record->usage_limit !== null
                            ? $used . ' / ' . $record->usage_limit
                            : (string)$used;
                    }),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Važi od')
                    ->dateTime('d.m.Y. H:i')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Važi do')
                    ->dateTime('d.m.Y. H:i')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivan')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('Tip popusta')
                    ->options([
                        ShopVoucher::TYPE_PERCENTAGE => 'Procentualni',
                        ShopVoucher::TYPE_FIXED => 'Fiksni',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktivni')
                    ->falseLabel('Neaktivni')
                    ->placeholder('Svi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Uredi'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShopVouchers::route('/'),
            'create' => Pages\CreateShopVoucher::route('/create'),
            'edit' => Pages\EditShopVoucher::route('/{record}/edit'),
        ];
    }
}
