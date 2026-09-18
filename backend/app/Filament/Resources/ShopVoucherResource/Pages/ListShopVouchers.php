<?php

namespace App\Filament\Resources\ShopVoucherResource\Pages;

use App\Filament\Resources\ShopVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopVouchers extends ListRecords
{
    protected static string $resource = ShopVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
