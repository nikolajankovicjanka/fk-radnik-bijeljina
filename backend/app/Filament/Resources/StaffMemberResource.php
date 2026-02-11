<?php

namespace App\Filament\Resources;

use App\Enums\TeamType;

use App\Enums\StaffRole;
use App\Filament\Resources\StaffMemberResource\Pages;
use App\Models\StaffMember;
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
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Illuminate\Support\Facades\Storage;

class StaffMemberResource extends Resource
{
    protected static ?string $model = StaffMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Team';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form) : Form
    {
        return $form->schema([Section::make('Stručni štab')->columns(2)->schema([Select::make('team_type')->label('Selekcija')->options(TeamType::options())->required(),

                                                                                 TextInput::make('name')->label('Ime i prezime')->required()->maxLength(255)->columnSpanFull(),

                                                                                 Select::make('role')->label('Uloga')->options(StaffRole::options())->required()->searchable(),

                                                                                 // Opcija 2: plain string (brže)
                                                                                 TextInput::make('role')->label('Uloga')->required()->maxLength(255),

                                                                                 TextInput::make('sort_order')->label('Redoslijed')->numeric()->minValue(0)->default(0)->helperText('Manji broj = prikazuje se ranije.'),

                                                                                 FileUpload::make('photo')->label('Slika')->disk('public')->directory('staff')->image()->maxSize(4096)->columnSpanFull()->helperText('Sistem snima 900x1200 WEBP + 450x600 thumb WEBP.')->saveUploadedFileUsing(function ($file, Forms\Get $get, ?StaffMember $record) {
                                                                                     // obriši stare ako edit
                                                                                     if ($record?->photo) {
                                                                                         $disk = Storage::disk('public');
                                                                                         $disk->delete($record->photo);
                                                                                         $disk->delete(str_replace('.webp', '_thumb.webp', $record->photo));
                                                                                     }

                                                                                     $uuid = (string) Str::uuid();
                                                                                     $dir = 'staff';

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

                                                                                     ImageOptimizer::optimize($mainAbs);
                                                                                     ImageOptimizer::optimize($thumbAbs);

                                                                                     return $mainRel;
                                                                                 }),

                                                                                 Toggle::make('is_active')->label('Aktivan')->default(true),]),]);
    }

    public static function table(Table $table) : Table
    {
        return $table->columns([ImageColumn::make('photo')->label('Slika')->disk('public')->square()
                                    // u tabeli koristi thumb ako postoji
                                    ->getStateUsing(fn(StaffMember $record) => $record->photo ? str_replace('.webp', '_thumb.webp', $record->photo) : null)->defaultImageUrl(fn() => url('/FK_Radnik_logo.png')),

                                TextColumn::make('name')->label('Ime i prezime')->sortable()->searchable(),

                                TextColumn::make('role')->label('Uloga')->badge()->sortable()->searchable(),

                                TextColumn::make('team_type')->label('Selekcija')->badge()->sortable(),

                                TextColumn::make('sort_order')->label('Redoslijed')->sortable(),

                                IconColumn::make('is_active')->label('Aktivan')->boolean()->sortable(),])->filters([SelectFilter::make('team_type')->label('Selekcija')->options(TeamType::options()),

                                                                                                                    TernaryFilter::make('is_active')->label('Aktivan'),])->actions([Tables\Actions\EditAction::make(),
                                                                                                                                                                                    Tables\Actions\DeleteAction::make(),])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(),]),])->defaultSort('team_type')->defaultSort('sort_order');
    }

    public static function getPages() : array
    {
        return ['index' => Pages\ListStaffMembers::route('/'), 'create' => Pages\CreateStaffMember::route('/create'),
                'edit'  => Pages\EditStaffMember::route('/{record}/edit'),];
    }
}
