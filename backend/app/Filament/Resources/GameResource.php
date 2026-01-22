<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Models\Club;
use App\Models\Game;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Games';
    protected static ?string $modelLabel = 'Game';
    protected static ?string $pluralModelLabel = 'Games';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Osnovno')->columns(2)->schema([
                Select::make('team_type')
                    ->label('Tim')
                    ->options([
                        'first_team' => 'Prvi tim',
                        'youth' => 'Youth',
                        'women' => 'Žene',
                    ])
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'live' => 'Live',
                        'finished' => 'Finished',
                    ])
                    ->required()
                    ->default('scheduled'),

                Select::make('home_club_id')
                    ->label('Domaćin')
                    ->options(fn () => Club::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),

                Select::make('away_club_id')
                    ->label('Gost')
                    ->options(fn () => Club::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),

                DateTimePicker::make('kickoff_at')
                    ->label('Početak')
                    ->seconds(false)
                    ->required(),

                TextInput::make('stadium')->label('Stadion'),
                TextInput::make('competition')->label('Takmicenje'),

                TextInput::make('home_score')->label('Golovi domaćin')->numeric()->minValue(0),
                TextInput::make('away_score')->label('Golovi gost')->numeric()->minValue(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kickoff_at')->label('Početak')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('team_type')->label('Tim')->sortable(),
                TextColumn::make('homeClub.name')->label('Domaćin')->searchable(),
                TextColumn::make('awayClub.name')->label('Gost')->searchable(),
                TextColumn::make('status')->label('Status')->sortable(),
                TextColumn::make('competition')->label('Takmicenje')->toggleable(),
            ])
            ->defaultSort('kickoff_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
