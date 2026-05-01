<?php

namespace App\Filament\Resources\ClubDocumentResource\Pages;

use App\Filament\Resources\ClubDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClubDocuments extends ListRecords
{
    protected static string $resource = ClubDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
