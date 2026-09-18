<?php

namespace App\Filament\Resources\ShopOrderResource\RelationManagers;

use App\Models\ShopOrderItem;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Artikli narudžbe';

    protected static ?string $modelLabel = 'artikal';

    protected static ?string $pluralModelLabel = 'artikli';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Proizvod')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('size')
                    ->label('Veličina')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->copyable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Cijena')
                    ->money('BAM'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Kol.')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('customization')
                    ->label('Personalizacija')
                    ->getStateUsing(function (ShopOrderItem $record): string {
                        $parts = [];

                        if ($record->customization_name) {
                            $parts[] = $record->customization_name;
                        }

                        if ($record->customization_number) {
                            $parts[] = '#' . $record->customization_number;
                        }

                        return $parts !== []
                            ? implode(' / ', $parts)
                            : '—';
                    }),

                Tables\Columns\TextColumn::make('customization_price')
                    ->label('Personalizacija')
                    ->money('BAM'),

                Tables\Columns\TextColumn::make('line_total')
                    ->label('Ukupno')
                    ->money('BAM')
                    ->weight('bold'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
