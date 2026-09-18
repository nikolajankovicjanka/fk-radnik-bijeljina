<?php

namespace App\Services\Shop;

use App\Models\SeasonTicket;
use App\Models\ShopSetting;

class SeasonTicketService
{
    /**
     * Provjerava sezonsku kartu za trenutno aktivnu sezonu.
     */
    public function validate(?string $ticketNumber): array
    {
        $ticketNumber = trim((string)$ticketNumber);

        if ($ticketNumber === '') {
            return [
                'valid' => false,
                'message' => 'Broj sezonske karte nije unesen.',
                'ticket' => null,
                'discount_percent' => 0.0,
            ];
        }

        $discountEnabled = (bool)ShopSetting::getValue(
            'season_ticket_discount_enabled',
            false
        );

        if (!$discountEnabled) {
            return [
                'valid' => false,
                'message' => 'Popust za sezonske karte trenutno nije aktivan.',
                'ticket' => null,
                'discount_percent' => 0.0,
            ];
        }

        $currentSeason = (string)ShopSetting::getValue(
            'current_season',
            ''
        );

        if ($currentSeason === '') {
            return [
                'valid' => false,
                'message' => 'Trenutna sezona nije podešena.',
                'ticket' => null,
                'discount_percent' => 0.0,
            ];
        }

        $ticket = SeasonTicket::query()
            ->where('ticket_number', $ticketNumber)
            ->where('season', $currentSeason)
            ->where('is_active', true)
            ->first();

        if (!$ticket) {
            return [
                'valid' => false,
                'message' => 'Sezonska karta nije pronađena ili nije aktivna za trenutnu sezonu.',
                'ticket' => null,
                'discount_percent' => 0.0,
            ];
        }

        $discountPercent = (float)ShopSetting::getValue(
            'season_ticket_discount_percent',
            0
        );

        if ($discountPercent <= 0) {
            return [
                'valid' => false,
                'message' => 'Popust za sezonske karte nije pravilno podešen.',
                'ticket' => null,
                'discount_percent' => 0.0,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Sezonska karta je potvrđena.',
            'ticket' => $ticket,
            'discount_percent' => $discountPercent,
        ];
    }

    /**
     * Izračunava iznos popusta na vrijednost robe.
     */
    public function calculateDiscount(
        float $subtotal,
        float $discountPercent
    ): float
    {
        if ($subtotal <= 0 || $discountPercent <= 0) {
            return 0.0;
        }

        $discountPercent = min($discountPercent, 100);

        return round(
            $subtotal * ($discountPercent / 100),
            2
        );
    }
}
