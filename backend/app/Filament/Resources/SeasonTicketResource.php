<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonTicketResource\Pages;
use App\Models\SeasonTicket;
use App\Models\ShopSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonTicketResource extends Resource
{
    protected static ?string $model = SeasonTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Webshop';

    protected static ?string $navigationLabel = 'Sezonske karte';

    protected static ?string $modelLabel = 'sezonska karta';

    protected static ?string $pluralModelLabel = 'sezonske karte';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Podaci o sezonskoj karti')
                    ->description(
                        'Sezonska karta omogućava vlasniku popust prilikom kupovine u webshopu.'
                    )
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('Broj sezonske karte')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('npr. 000123')
                            ->helperText('Broj mora odgovarati broju koji se nalazi na sezonskoj karti.'),

                        Forms\Components\TextInput::make('season')
                            ->label('Sezona')
                            ->required()
                            ->maxLength(20)
                            ->default(
                                fn(): string => (string)ShopSetting::getValue('current_season', '2026/27')
                            )
                            ->placeholder('2026/27'),

                        Forms\Components\TextInput::make('holder_name')
                            ->label('Ime i prezime vlasnika')
                            ->maxLength(255)
                            ->placeholder('npr. Petar Petrović')
                            ->helperText('Opcionalno. Za provjeru popusta dovoljan je broj karte.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktivna')
                            ->default(true)
                            ->helperText('Neaktivna karta ne može ostvariti popust.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Broj karte')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('holder_name')
                    ->label('Vlasnik')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('season')
                    ->label('Sezona')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivna')
                    ->boolean(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Narudžbe')
                    ->counts('orders')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kreirana')
                    ->dateTime('d.m.Y. H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('season')
                    ->label('Sezona')
                    ->options(
                        fn(): array => SeasonTicket::query()
                            ->select('season')
                            ->distinct()
                            ->orderByDesc('season')
                            ->pluck('season', 'season')
                            ->all()
                    ),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktivne')
                    ->falseLabel('Neaktivne')
                    ->placeholder('Sve'),
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
            'index' => Pages\ListSeasonTickets::route('/'),
            'create' => Pages\CreateSeasonTicket::route('/create'),
            'edit' => Pages\EditSeasonTicket::route('/{record}/edit'),
        ];
    }
}
