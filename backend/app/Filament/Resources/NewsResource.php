<?php

namespace App\Filament\Resources;

use App\Models\News;
use App\Filament\Resources\NewsResource\Pages;

use Filament\Resources\Resource;
use App\Enums\NewsCategory;
use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Osnovno')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Naslov')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get) {
                                // slug popuni samo ako je prazan (da korisnik može ručno)
                                if (! $get('slug')) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            })
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('category')
                            ->label('Kategorija')
                            ->options(NewsCategory::options())
                            ->searchable()
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Aktivno')
                            ->default(true),

                        DateTimePicker::make('published_at')
                            ->label('Datum objave')
                            ->seconds(false)
                            ->helperText('Ako ostaviš prazno – neće se prikazivati na sajtu.')
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Kratak opis')
                            ->rows(3)
                            ->maxLength(300)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Sadržaj')
                            ->columnSpanFull(),
                    ]),

                Section::make('Slika')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Cover slika')
                            ->disk('public')
                            ->directory('news')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1600')
                            ->imageResizeTargetHeight('900')
                            ->maxSize(4096)
                            ->helperText('Preporuka: 1600x900 (16:9).')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Slika')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(fn () => url('/favicon.ico')),

                TextColumn::make('title')
                    ->label('Naslov')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('category')
                    ->label('Kategorija')
                    ->formatStateUsing(function ($state) {
                        // ako je enum cast
                        if ($state instanceof \App\Enums\NewsCategory) {
                            return $state->label();
                        }

                        // ako je string u bazi
                        $options = \App\Enums\NewsCategory::options();
                        return $options[$state] ?? (string) $state;
                    })
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktivno')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Objavljeno')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategorija')
                    ->options(NewsCategory::options()),

                TernaryFilter::make('is_active')
                    ->label('Aktivno'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
