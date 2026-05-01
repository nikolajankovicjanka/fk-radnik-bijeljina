<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClubDocumentResource\Pages;
use App\Models\ClubDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ClubDocumentResource extends Resource
{
    protected static ?string $model = ClubDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $navigationGroup = 'Klub';

    protected static ?string $navigationLabel = 'Dokumenti';

    protected static ?string $modelLabel = 'Dokument';

    protected static ?string $pluralModelLabel = 'Dokumenti';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Podaci o dokumentu')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Naziv dokumenta')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('Dokument')
                            ->disk('public')
                            ->directory('club-documents')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/csv',
                            ])
                            ->maxSize(10240)
                            ->required()
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (!$state) {
                                    return;
                                }

                                $set('uploaded_at', now()->toDateString());
                            }),

                        Forms\Components\DatePicker::make('uploaded_at')
                            ->label('Datum uploada')
                            ->default(now())
                            ->required(),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Objavljeno')
                            ->helperText('Samo objavljeni dokumenti će biti prikazani na sajtu.')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('uploaded_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Naziv')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_extension')
                    ->label('Tip')
                    ->badge(),

                Tables\Columns\TextColumn::make('formatted_file_size')
                    ->label('Veličina'),

                Tables\Columns\TextColumn::make('uploaded_at')
                    ->label('Datum uploada')
                    ->date('d.m.Y.')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Objavljeno')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kreirano')
                    ->dateTime('d.m.Y. H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Objavljeno')
                    ->placeholder('Svi dokumenti')
                    ->trueLabel('Samo objavljeni')
                    ->falseLabel('Samo neobjavljeni'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Skini')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (ClubDocument $record): ?string => $record->file_url)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('Uredi'),

                Tables\Actions\DeleteAction::make()
                    ->label('Obriši')
                    ->before(function (ClubDocument $record) {
                        if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                            Storage::disk('public')->delete($record->file_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Obriši označene')
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                                    Storage::disk('public')->delete($record->file_path);
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClubDocuments::route('/'),
            'create' => Pages\CreateClubDocument::route('/create'),
            'edit' => Pages\EditClubDocument::route('/{record}/edit'),
        ];
    }
}
