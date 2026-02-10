<?php

namespace App\Filament\Resources;

use App\Enums\TeamType;
use App\Enums\PlayerPosition;
use App\Filament\Resources\PlayerResource\Pages;
use App\Models\Player;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class PlayerResource extends Resource
{
    protected static ?string $model = Player::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Team';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form) : Form
    {
        return $form->schema([Section::make('Igrač')->columns(2)->schema([Select::make('team_type')->label('Selekcija')->options(TeamType::options())->required(),

                                                                          TextInput::make('name')->label('Ime i prezime')->required()->maxLength(255)->columnSpanFull(),

                                                                          Select::make('position')->label('Pozicija')->options(PlayerPosition::options())->searchable()->required(),

                                                                          TextInput::make('birth_year')->label('Godište')->numeric()->minValue(1950)->maxValue((int) now()->format('Y'))->required(),

                                                                          TextInput::make('shirt_number')->label('Broj dresa')->numeric()->minValue(0)->maxValue(99)->required()->rule(function (Forms\Get $get, ?Player $record) {
                                                                                  return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                                                                      $teamType = $get('team_type');
                                                                                      if (!$teamType) {
                                                                                          return;
                                                                                      }

                                                                                      $q = Player::query()->where('team_type', $teamType)->where('shirt_number', (int) $value);

                                                                                      if ($record) {
                                                                                          $q->whereKeyNot($record->getKey());
                                                                                      }

                                                                                      if ($q->exists()) {
                                                                                          $fail('Taj broj dresa već postoji u ovoj selekciji.');
                                                                                      }
                                                                                  };
                                                                              }),

                                                                          FileUpload::make('photo')->label('Slika')->disk('public')->directory('players')->image()->maxSize(4096)->columnSpanFull()->helperText('Upload: bilo koji format. Sistem snima 900x1200 WEBP + 450x600 thumb WEBP.')->saveUploadedFileUsing(function ($file, ?Player $record) {
                                                                                  // ako mijenjaš sliku na edit-u, obriši staru (main + thumb)
                                                                                  if ($record?->photo) {
                                                                                      self::deletePhotoPair($record->photo);
                                                                                  }

                                                                                  $uuid = (string) Str::uuid();
                                                                                  $dir = 'players';

                                                                                  $mainRel = "{$dir}/{$uuid}.webp";
                                                                                  $thumbRel = "{$dir}/{$uuid}_thumb.webp";

                                                                                  $mainAbs = storage_path("app/public/{$mainRel}");
                                                                                  $thumbAbs = storage_path("app/public/{$thumbRel}");

                                                                                  if (!is_dir(dirname($mainAbs))) {
                                                                                      mkdir(dirname($mainAbs), 0775, true);
                                                                                  }

                                                                                  $manager = new ImageManager(new Driver());

                                                                                  // MAIN 900x1200 (3:4)
                                                                                  $manager->read($file->getRealPath())->cover(900, 1200)->toWebp(75)->save($mainAbs);

                                                                                  // THUMB 450x600 (3:4)
                                                                                  $manager->read($file->getRealPath())->cover(450, 600)->toWebp(72)->save($thumbAbs);

                                                                                  // Optimizer (radi jer imaš binarke)
                                                                                  ImageOptimizer::optimize($mainAbs);
                                                                                  ImageOptimizer::optimize($thumbAbs);

                                                                                  return $mainRel;
                                                                              }),

                                                                          Toggle::make('is_active')->label('Aktivan')->default(true),]),]);
    }

    public static function table(Table $table) : Table
    {
        return $table->columns([ImageColumn::make('photo')->label('Slika')->disk('public')->square()->getStateUsing(function (Player $record) {
                    // u tabeli koristi thumb da ne vuče velike fajlove
                    if (!$record->photo) {
                        return null;
                    }

                    return str_replace('.webp', '_thumb.webp', $record->photo);
                })->defaultImageUrl(fn() => url('/FK_Radnik_logo.png')),

                                TextColumn::make('shirt_number')->label('#')->sortable(),

                                TextColumn::make('name')->label('Ime i prezime')->sortable(),

                                TextColumn::make('position')->label('Pozicija')->badge()->sortable(),

                                TextColumn::make('team_type')->label('Selekcija')->formatStateUsing(fn($state) => match ($state) {
                                        'first_team' => 'Prvi tim',
                                        'youth' => 'Omladinski',
                                        'women' => 'Žene',
                                        default => (string) $state,
                                    })->badge()->sortable(),

                                TextColumn::make('birth_year')->label('Godište')->sortable(),

                                IconColumn::make('is_active')->label('Aktivan')->boolean()->sortable(),])->filters([SelectFilter::make('team_type')->label('Selekcija')->options(TeamType::options()),
                                                                                                                    SelectFilter::make('position')->label('Pozicija')->options(PlayerPosition::options()),
                                                                                                                    TernaryFilter::make('is_active')->label('Aktivan'),])->actions([Tables\Actions\EditAction::make(),])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(),]),])->defaultSort('team_type')->defaultSort('shirt_number');
    }

    public static function getPages() : array
    {
        return ['index' => Pages\ListPlayers::route('/'), 'create' => Pages\CreatePlayer::route('/create'),
                'edit'  => Pages\EditPlayer::route('/{record}/edit'),];
    }

    private static function deletePhotoPair(string $mainPath) : void
    {
        $disk = Storage::disk('public');

        $disk->delete($mainPath);

        if (str_ends_with($mainPath, '.webp')) {
            $thumb = str_replace('.webp', '_thumb.webp', $mainPath);
            $disk->delete($thumb);
        }
    }
}
