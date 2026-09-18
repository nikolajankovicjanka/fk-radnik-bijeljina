<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Nova webshop narudžba</title>
</head>

<body style="margin:0;padding:0;background:#f4f7f9;font-family:Arial,sans-serif;color:#313537;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f9;padding:30px 15px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:650px;background:#ffffff;border-radius:8px;overflow:hidden;">

                <tr>
                    <td style="background:#031426;padding:28px;text-align:center;">
                        <h1 style="margin:0;color:#ffffff;font-size:24px;">
                            Nova webshop narudžba
                        </h1>

                        <p style="margin:8px 0 0;color:#edc06a;">
                            {{ $order->order_number }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:30px;">

                        <h3 style="color:#072846;">Kupac</h3>

                        <p>
                            <strong>{{ $order->first_name }} {{ $order->last_name }}</strong><br>
                            {{ $order->email }}<br>
                            {{ $order->phone }}
                        </p>

                        @if ($order->delivery_method === 'courier')
                            <p>
                                <strong>Adresa dostave:</strong><br>
                                {{ $order->address }}<br>
                                {{ $order->postal_code }} {{ $order->city }}
                            </p>
                        @else
                            <p>
                                <strong>Preuzimanje:</strong> lično preuzimanje
                            </p>
                        @endif

                        <h3 style="color:#072846;margin-top:30px;">
                            Stavke narudžbe
                        </h3>

                        <table width="100%" cellpadding="8" cellspacing="0"
                               style="border-collapse:collapse;">

                            @foreach ($order->items as $item)
                                <tr style="border-bottom:1px solid #e5e5e5;">
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>

                                        @if ($item->size)
                                            <br>
                                            <span style="font-size:13px;color:#777;">
                                                Veličina: {{ $item->size }}
                                            </span>
                                        @endif

                                        @if ($item->customization_name || $item->customization_number)
                                            <br>
                                            <span style="font-size:13px;color:#777;">
                                                Personalizacija:
                                                {{ $item->customization_name }}

                                                @if ($item->customization_number)
                                                    #{{ $item->customization_number }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>

                                    <td align="center">
                                        {{ $item->quantity }}x
                                    </td>

                                    <td align="right">
                                        {{ number_format((float) $item->line_total, 2, ',', '.') }} KM
                                    </td>
                                </tr>
                            @endforeach

                        </table>

                        @if ($order->season_ticket_number)
                            <p style="margin-top:25px;">
                                <strong>Sezonska karta:</strong>
                                {{ $order->season_ticket_number }}
                            </p>
                        @endif

                        @if ($order->voucher_code)
                            <p style="margin-top:25px;">
                                <strong>Promo vaučer:</strong>
                                {{ $order->voucher_code }}
                            </p>
                        @endif

                        <table width="100%" cellpadding="6" cellspacing="0"
                               style="margin-top:25px;">

                            <tr>
                                <td>Međuzbir:</td>
                                <td align="right">
                                    {{ number_format((float) $order->subtotal, 2, ',', '.') }} KM
                                </td>
                            </tr>

                            @if ((float) $order->discount_amount > 0)
                                <tr>
                                    <td>
                                        Popust
                                        @if ($order->discount_percent)
                                            ({{ number_format((float) $order->discount_percent, 0) }}%)
                                        @endif
                                    </td>

                                    <td align="right">
                                        -{{ number_format((float) $order->discount_amount, 2, ',', '.') }} KM
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td>Dostava:</td>
                                <td align="right">
                                    {{ number_format((float) $order->shipping_amount, 2, ',', '.') }} KM
                                </td>
                            </tr>

                            <tr>
                                <td style="font-size:18px;">
                                    <strong>Ukupno:</strong>
                                </td>

                                <td align="right" style="font-size:18px;color:#072846;">
                                    <strong>
                                        {{ number_format((float) $order->total, 2, ',', '.') }} KM
                                    </strong>
                                </td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            <strong>Plaćanje:</strong> pouzećem
                        </p>

                        @if ($order->notes)
                            <div style="margin-top:25px;background:#f4f7f9;padding:15px;">
                                <strong>Napomena kupca:</strong><br>
                                {{ $order->notes }}
                            </div>
                        @endif

                    </td>
                </tr>

                <tr>
                    <td style="background:#031426;padding:20px;text-align:center;color:#ffffff;font-size:12px;">
                        FK Radnik Bijeljina — Webshop
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
