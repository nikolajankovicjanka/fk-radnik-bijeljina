<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Potvrda narudžbe</title>
</head>

<body style="margin:0;padding:0;background:#f4f7f9;font-family:Arial,sans-serif;color:#313537;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f9;padding:30px 15px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:650px;background:#ffffff;border-radius:8px;overflow:hidden;">

                <tr>
                    <td style="background:#031426;padding:28px;text-align:center;">
                        <h1 style="margin:0;color:#ffffff;font-size:26px;">
                            FK Radnik Bijeljina
                        </h1>

                        <p style="margin:8px 0 0;color:#edc06a;font-size:15px;">
                            Potvrda webshop narudžbe
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:30px;">

                        <h2 style="margin-top:0;color:#072846;">
                            Hvala na narudžbi!
                        </h2>

                        <p>
                            Poštovani {{ $order->first_name }},
                        </p>

                        <p>
                            Vaša narudžba je uspješno zaprimljena.
                            Nakon obrade narudžbe bićete kontaktirani ukoliko
                            budu potrebne dodatne informacije.
                        </p>

                        <div style="background:#f4f7f9;padding:18px;margin:25px 0;text-align:center;">
                            <div style="font-size:13px;color:#666;">
                                BROJ NARUDŽBE
                            </div>

                            <strong style="display:block;margin-top:6px;font-size:22px;color:#072846;">
                                {{ $order->order_number }}
                            </strong>
                        </div>

                        <h3 style="color:#072846;">
                            Pregled narudžbe
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

                        <table width="100%" cellpadding="6" cellspacing="0"
                               style="margin-top:20px;">

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
                                    @if ((float) $order->shipping_amount > 0)
                                        {{ number_format((float) $order->shipping_amount, 2, ',', '.') }} KM
                                    @else
                                        Besplatno
                                    @endif
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

                        <p style="margin-top:30px;">
                            Način plaćanja:
                            <strong>pouzećem</strong>
                        </p>

                        @if ($order->delivery_method === 'courier')
                            <p>
                                Dostava:<br>
                                <strong>
                                    {{ $order->address }},
                                    {{ $order->postal_code }}
                                    {{ $order->city }}
                                </strong>
                            </p>
                        @else
                            <p>
                                Način preuzimanja:
                                <strong>lično preuzimanje</strong>
                            </p>
                        @endif

                        <p style="margin-top:30px;">
                            Hvala što podržavate FK Radnik Bijeljina.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="background:#031426;padding:20px;text-align:center;color:#ffffff;font-size:12px;">
                        FK Radnik Bijeljina
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
