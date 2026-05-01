<?php

namespace App\Filament\Resources\ClubDocumentResource\Pages;

use App\Filament\Resources\ClubDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClubDocument extends EditRecord
{
    protected static string $resource = ClubDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
