<?php

namespace App\Filament\Resources\SeasonTicketResource\Pages;

use App\Filament\Resources\SeasonTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeasonTicket extends EditRecord
{
    protected static string $resource = SeasonTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
