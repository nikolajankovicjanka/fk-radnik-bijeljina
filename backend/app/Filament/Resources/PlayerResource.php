<?php

namespace App\Filament\Resources;

use App\Enums\TeamType;
use App\Enums\PlayerPosition;
use App\Filament\Resources\PlayerResource\Pages;
use App\Models\Player;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

use Illuminate\Support\Facades\Storage;

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
                                                                                  if (!$teamType) return;

                                                                                  $q = Player::query()->where('team_type', $teamType)->where('shirt_number', (int) $value);

                                                                                  if ($record) $q->whereKeyNot($record->getKey());

                                                                                  if ($q->exists()) {
                                                                                      $fail('Taj broj dresa već postoji u ovoj selekciji.');
                                                                                  }
                                                                              };
                                                                          }),

                                                                          FileUpload::make('photo')->label('Slika')->disk('public')->directory('players')->image()->multiple(false)                 // ✅ bitno: single file UI
                                                                              ->fetchFileInformation(false)     // ✅ makni "waiting for size"
                                                                              ->imageEditor()->imageCropAspectRatio('3:4')->imageResizeTargetWidth('900')->imageResizeTargetHeight('1200')->maxSize(4096)->columnSpanFull()->helperText('Preporuka: 900x1200 (3:4).')

                                                                              // ✅ Filament ponekad očekuje array interni state čak i za single.
                                                                              // Ovako ga napunimo na edit-u bez pucanja / loadera:
                                                                              ->afterStateHydrated(function (FileUpload $component, $state) {
                                                                                  // ako iz baze dođe string "players/xxx.png" pretvori u array
                                                                                  $component->state(blank($state) ? [] : (is_array($state) ? $state : [$state]));
                                                                              })

                                                                              // ✅ u bazu upiši string (prvi element)
                                                                              ->dehydrateStateUsing(fn($state) => is_array($state) ? ($state[0] ?? null) : $state)

                                                                              // ✅ uvijek novo ime fajla
                                                                              ->getUploadedFileNameForStorageUsing(fn($file) => (string) str()->uuid() . '.' . $file->getClientOriginalExtension())

                                                                              // ✅ briši stari fajl tek kad zaista snimaš novi
                                                                              ->saveUploadedFileUsing(function ($file, $get, $set, ?Player $record) {
                                                                                  // obriši staru sliku (osim default)
                                                                                  if ($record?->photo && $record->photo !== 'players/default.png') {
                                                                                      Storage::disk('public')->delete($record->photo);
                                                                                  }

                                                                                  // snimi novu
                                                                                  $path = $file->storePublicly('players', 'public');

                                                                                  // postavi state (kao array)
                                                                                  $set('photo', [$path]);

                                                                                  return $path;
                                                                              }),

                                                                          Toggle::make('is_active')->label('Aktivan')->default(true),]),]);
    }

    public static function table(Table $table) : Table
    {
        return $table->columns([ImageColumn::make('photo')->label('Slika')->disk('public')->square()->defaultImageUrl(fn() => url('/FK_Radnik_logo.png')),

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
}
