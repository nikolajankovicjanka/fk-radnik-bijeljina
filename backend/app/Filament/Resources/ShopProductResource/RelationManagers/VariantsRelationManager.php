<?php

namespace App\Filament\Resources\ShopProductResource\RelationManagers;

use App\Models\ShopProductVariant;
use App\Models\ShopStockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Veličine i stanje';

    protected static ?string $modelLabel = 'varijanta';

    protected static ?string $pluralModelLabel = 'varijante';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('size')
                    ->label('Veličina')
                    ->placeholder('npr. S, M, L, XL, XXL, 128...')
                    ->required()
                    ->maxLength(30),

                Forms\Components\TextInput::make('sku')
                    ->label('SKU varijante')
                    ->helperText('Jedinstvena šifra ove veličine.')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktivna')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('size')
            ->columns([
                Tables\Columns\TextColumn::make('size')
                    ->label('Veličina')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stanje')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->label('Rezervisano')
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Dostupno')
                    ->getStateUsing(
                        fn(ShopProductVariant $record): int => $record->available_quantity
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivna')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktivne')
                    ->falseLabel('Neaktivne')
                    ->placeholder('Sve'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Dodaj veličinu')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['stock_quantity'] = 0;
                        $data['reserved_quantity'] = 0;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Dopuni stanje')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Količina za dodavanje')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->required(),

                        Forms\Components\Textarea::make('note')
                            ->label('Napomena')
                            ->placeholder('npr. Nova isporuka robe')
                            ->rows(3),
                    ])
                    ->action(function (
                        ShopProductVariant $record,
                        array              $data
                    ): void {
                        DB::transaction(function () use ($record, $data): void {
                            $variant = ShopProductVariant::query()
                                ->lockForUpdate()
                                ->findOrFail($record->id);

                            $before = $variant->stock_quantity;
                            $quantity = (int)$data['quantity'];
                            $after = $before + $quantity;

                            $variant->update([
                                'stock_quantity' => $after,
                            ]);

                            ShopStockMovement::create([
                                'shop_product_variant_id' => $variant->id,
                                'type' => ShopStockMovement::TYPE_RESTOCK,
                                'quantity' => $quantity,
                                'quantity_before' => $before,
                                'quantity_after' => $after,
                                'reference' => null,
                                'note' => $data['note'] ?? null,
                                'created_by' => auth()->id(),
                            ]);
                        });

                        Notification::make()
                            ->title('Stanje robe je dopunjeno.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Uredi'),

                Tables\Actions\Action::make('deactivate')
                    ->label('Deaktiviraj')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(
                        fn(ShopProductVariant $record): bool => $record->is_active
                    )
                    ->action(function (ShopProductVariant $record): void {
                        $record->update([
                            'is_active' => false,
                        ]);

                        Notification::make()
                            ->title('Varijanta je deaktivirana.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }
}
