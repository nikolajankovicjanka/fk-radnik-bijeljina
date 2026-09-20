<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class CreateShopOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
                'required_if:delivery_method,courier',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
                'required_if:delivery_method,courier',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'delivery_method' => [
                'required',
                'in:courier,pickup',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'season_ticket_number' => [
                'nullable',
                'string',
                'max:100',
                'prohibited_with:voucher_code',
            ],

            'voucher_code' => [
                'nullable',
                'string',
                'max:100',
                'prohibited_with:season_ticket_number',
            ],

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

            'items.*.customization' => [
                'nullable',
                'array',
            ],

            'items.*.customization.name' => [
                'nullable',
                'string',
                'max:50',
            ],

            'items.*.customization.number' => [
                'nullable',
                'string',
                'max:3',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' =>
                'Ime je obavezno.',

            'last_name.required' =>
                'Prezime je obavezno.',

            'email.required' =>
                'Email adresa je obavezna.',

            'email.email' =>
                'Email adresa nije ispravna.',

            'phone.required' =>
                'Broj telefona je obavezan.',

            'address.required_if' =>
                'Adresa je obavezna za kurirsku dostavu.',

            'city.required_if' =>
                'Grad je obavezan za kurirsku dostavu.',

            'items.required' =>
                'Korpa je prazna.',

            'items.min' =>
                'Korpa mora sadržavati najmanje jedan proizvod.',

            'season_ticket_number.prohibited_with' =>
                'Sezonska karta i promo vaučer se ne mogu kombinovati.',

            'voucher_code.prohibited_with' =>
                'Promo vaučer i sezonska karta se ne mogu kombinovati.',
        ];
    }
}
