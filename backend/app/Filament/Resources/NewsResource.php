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
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form) : Form
    {
        return $form->schema([Section::make('Osnovno')->columns(2)->schema([Tabs::make('Translations')->columnSpanFull()->tabs([Tab::make('SR (Lat)')->schema([TextInput::make('title.sr-Latn')->label('Naslov')->required()->live(onBlur: true)->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get) {
            // slug popuni samo ako je prazan (da korisnik može ručno)
            if (!$get('slug')) {
                $set('slug', Str::slug($state ?? ''));
            }
        })->maxLength(255),

                                                                                                                                                               Textarea::make('excerpt.sr-Latn')->label('Kratak opis')->rows(3)->maxLength(300),

                                                                                                                                                               RichEditor::make('content.sr-Latn')->label('Sadržaj'),]),

                                                                                                                                Tab::make('SR (Ćir)')->schema([TextInput::make('title.sr-Cyrl')->label('Наслов')->maxLength(255),

                                                                                                                                                               Textarea::make('excerpt.sr-Cyrl')->label('Кратак опис')->rows(3)->maxLength(300),

                                                                                                                                                               RichEditor::make('content.sr-Cyrl')->label('Садржај'),]),

                                                                                                                                Tab::make('EN')->schema([TextInput::make('title.en')->label('Title')->maxLength(255),

                                                                                                                                                         Textarea::make('excerpt.en')->label('Excerpt')->rows(3)->maxLength(300),

                                                                                                                                                         RichEditor::make('content.en')->label('Content'),]),

                                                                                                                                Tab::make('FR')->schema([TextInput::make('title.fr')->label('Titre')->maxLength(255),

                                                                                                                                                         Textarea::make('excerpt.fr')->label('Extrait')->rows(3)->maxLength(300),

                                                                                                                                                         RichEditor::make('content.fr')->label('Contenu'),]),

                                                                                                                                Tab::make('ES')->schema([TextInput::make('title.es')->label('Título')->maxLength(255),

                                                                                                                                                         Textarea::make('excerpt.es')->label('Extracto')->rows(3)->maxLength(300),

                                                                                                                                                         RichEditor::make('content.es')->label('Contenido'),]),

                                                                                                                                Tab::make('DE')->schema([TextInput::make('title.de')->label('Titel')->maxLength(255),

                                                                                                                                                         Textarea::make('excerpt.de')->label('Auszug')->rows(3)->maxLength(300),

                                                                                                                                                         RichEditor::make('content.de')->label('Inhalt')->maxLength(0),]),]),

                                                                            TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(255),

                                                                            Select::make('category')->label('Kategorija')->options(NewsCategory::options())->searchable()->required(),

                                                                            Toggle::make('is_active')->label('Aktivno')->default(true),

                                                                            DateTimePicker::make('published_at')->label('Datum objave')->seconds(false)->helperText('Ako ostaviš prazno – neće se prikazivati na sajtu.')->columnSpanFull(),]),

                              Section::make('Slika')->schema([FileUpload::make('image')->label('Cover slika')->disk('public')->directory('news')->image()->imageEditor()->imageCropAspectRatio('16:9')->imageResizeTargetWidth('1600')->imageResizeTargetHeight('900')->maxSize(4096)->helperText('Preporuka: 1600x900 (16:9).')->columnSpanFull(),]),]);
    }

    public static function table(Table $table) : Table
    {
        return $table->columns([ImageColumn::make('image')->label('Slika')->disk('public')->square()->defaultImageUrl(fn() => url('/favicon.ico')),

                                TextColumn::make('title')->label('Naslov')->formatStateUsing(function ($state) {
                                    // title je JSON array; prikazujemo sr-Latn (ili prvi dostupni)
                                    if (is_array($state)) {
                                        return $state['sr-Latn'] ?? reset($state) ?? '';
                                    }
                                    return (string) $state;
                                })->sortable()->limit(40),

                                TextColumn::make('category')->label('Kategorija')->formatStateUsing(function ($state) {
                                    // ako je enum cast
                                    if ($state instanceof NewsCategory) {
                                        return $state->label();
                                    }

                                    // ako je string u bazi
                                    $options = NewsCategory::options();
                                    return $options[$state] ?? (string) $state;
                                })->badge()->sortable(),

                                IconColumn::make('is_active')->label('Aktivno')->boolean()->sortable(),

                                TextColumn::make('published_at')->label('Objavljeno')->dateTime('d.m.Y H:i')->sortable(),])->filters([SelectFilter::make('category')->label('Kategorija')->options(NewsCategory::options()),

                                                                                                                                      TernaryFilter::make('is_active')->label('Aktivno'),])->actions([Tables\Actions\EditAction::make(),])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(),]),])->defaultSort('published_at', 'desc');
    }

    public static function getRelations() : array
    {
        return [//
        ];
    }

    public static function getPages() : array
    {
        return ['index' => Pages\ListNews::route('/'), 'create' => Pages\CreateNews::route('/create'),
                'edit'  => Pages\EditNews::route('/{record}/edit'),];
    }
}
