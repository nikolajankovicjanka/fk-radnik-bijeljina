<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopProductResource\Pages;
use App\Filament\Resources\ShopProductResource\RelationManagers;
use App\Models\ShopProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ShopProductResource extends Resource
{
    protected static ?string $model = ShopProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Webshop';

    protected static ?string $navigationLabel = 'Proizvodi';

    protected static ?string $modelLabel = 'proizvod';

    protected static ?string $pluralModelLabel = 'proizvodi';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Osnovni podaci')
                    ->schema([
                        Forms\Components\Select::make('shop_category_id')
                            ->label('Kategorija')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Naziv proizvoda')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (
                                string    $operation,
                                ?string   $state,
                                Forms\Set $set
                            ): void {
                                if ($operation === 'create' && $state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('sku')
                            ->label('Šifra proizvoda (SKU)')
                            ->helperText('Jedinstvena interna šifra proizvoda, npr. FKR-DRES-DOM-26.')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Kratki opis')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Detaljni opis')
                            ->rows(7)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Cijena')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Redovna cijena')
                            ->numeric()
                            ->prefix('KM')
                            ->minValue(0)
                            ->required(),

                        Forms\Components\TextInput::make('sale_price')
                            ->label('Akcijska cijena')
                            ->numeric()
                            ->prefix('KM')
                            ->minValue(0)
                            ->nullable()
                            ->helperText('Ostaviti prazno ako proizvod nije na akciji.')
                            ->lt('price'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Slika proizvoda')
                    ->schema([
                        Forms\Components\FileUpload::make('main_image')
                            ->label('Glavna slika')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('shop/products')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Prikaz i opcije')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktivan')
                            ->default(true),

                        Forms\Components\Toggle::make('is_new')
                            ->label('Novo')
                            ->default(false),

                        Forms\Components\Toggle::make('is_bestseller')
                            ->label('Bestseller')
                            ->default(false),

                        Forms\Components\Toggle::make('is_customizable')
                            ->label('Moguća personalizacija')
                            ->helperText(
                                'Omogućava unos imena i broja na proizvodu.'
                            )
                            ->default(false),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Redoslijed')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Slika')
                    ->disk('public')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Proizvod')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategorija')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Cijena')
                    ->money('BAM')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Akcija')
                    ->money('BAM')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Varijante')
                    ->counts('variants'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktivan')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_new')
                    ->label('Novo')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_bestseller')
                    ->label('Bestseller')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Redoslijed')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shop_category_id')
                    ->label('Kategorija')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktivni')
                    ->falseLabel('Neaktivni')
                    ->placeholder('Svi'),

                Tables\Filters\TernaryFilter::make('is_new')
                    ->label('Novo'),

                Tables\Filters\TernaryFilter::make('is_bestseller')
                    ->label('Bestseller'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Uredi'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShopProducts::route('/'),
            'create' => Pages\CreateShopProduct::route('/create'),
            'edit' => Pages\EditShopProduct::route('/{record}/edit'),
        ];
    }
}
