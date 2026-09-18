<?php

namespace App\Filament\Resources\SeasonTicketResource\Pages;

use App\Filament\Resources\SeasonTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeasonTickets extends ListRecords
{
    protected static string $resource = SeasonTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
