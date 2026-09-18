<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CalculateShopPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],

            'items.*.variant_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:50',
            ],

            'season_ticket_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'voucher_code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'delivery_method' => [
                'required',
                'in:courier,pickup',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $seasonTicketNumber = trim(
                    (string)$this->input('season_ticket_number', '')
                );

                $voucherCode = trim(
                    (string)$this->input('voucher_code', '')
                );

                if (
                    $seasonTicketNumber !== ''
                    && $voucherCode !== ''
                ) {
                    $validator->errors()->add(
                        'voucher_code',
                        'Sezonska karta i promo vaučer se ne mogu kombinovati.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' =>
                'Korpa je prazna.',

            'items.array' =>
                'Stavke korpe nisu ispravne.',

            'items.min' =>
                'Korpa mora sadržavati najmanje jedan proizvod.',

            'items.max' =>
                'Korpa sadrži previše stavki.',

            'items.*.variant_id.required' =>
                'Varijanta proizvoda je obavezna.',

            'items.*.variant_id.integer' =>
                'Varijanta proizvoda nije ispravna.',

            'items.*.variant_id.min' =>
                'Varijanta proizvoda nije ispravna.',

            'items.*.quantity.required' =>
                'Količina proizvoda je obavezna.',

            'items.*.quantity.integer' =>
                'Količina proizvoda nije ispravna.',

            'items.*.quantity.min' =>
                'Količina mora biti najmanje 1.',

            'items.*.quantity.max' =>
                'Maksimalna količina jedne stavke je 50.',

            'season_ticket_number.string' =>
                'Broj sezonske karte nije ispravan.',

            'season_ticket_number.max' =>
                'Broj sezonske karte je predugačak.',

            'voucher_code.string' =>
                'Promo kod nije ispravan.',

            'voucher_code.max' =>
                'Promo kod je predugačak.',

            'delivery_method.required' =>
                'Način dostave je obavezan.',

            'delivery_method.in' =>
                'Odabrani način dostave nije ispravan.',
        ];
    }
}
