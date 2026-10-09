<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FK Radnik Shop - Potvrda narudžbe</title>
</head>

@php
    // Email slike uvijek koriste javni HTTPS domen, nezavisno od lokalnog APP_URL-a.
    // Opcionalno se moze podesiti 'mail_asset_url' u config/shop.php.
    $assetBase = rtrim(config('shop.mail_asset_url') ?: 'https://fkradnikbijeljina.com', '/');

    $logoUrl = $assetBase . '/logo/FK_Radnik_logo.png';

    $money = static fn ($amount) =>
        number_format((float) $amount, 2, ',', '.') . ' KM';
@endphp

<body style="margin:0;padding:0;background:#eef1f8;font-family:Arial,Helvetica,sans-serif;color:#24304b;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#eef1f8;">
    <tr>
        <td align="center" style="padding:30px 12px;">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:620px;background:#ffffff;border-collapse:collapse;">

                <!-- HEADER -->
                <tr>
                    <td align="center" style="background:#3036cc;padding:32px 24px 30px;">

                        <img
                            src="{{ $logoUrl }}"
                            alt="FK Radnik Bijeljina"
                            width="96"
                            style="display:block;width:96px;max-width:100%;height:auto;margin:0 auto 16px;"
                        >

                        <h1 style="margin:0;color:#ffffff;font-size:27px;line-height:1.3;letter-spacing:1px;">
                            FK RADNIK SHOP
                        </h1>

                        <p style="margin:8px 0 0;color:#e4e7ff;font-size:11px;letter-spacing:2px;">
                            ZVANIČNA ONLINE PRODAVNICA
                        </p>
                    </td>
                </tr>

                <!-- SUCCESS -->
                <tr>
                    <td style="padding:36px 30px 22px;">

                        <p style="margin:0 0 12px;color:#3036cc;font-size:12px;font-weight:bold;letter-spacing:1px;">
                            NARUDŽBA JE USPJEŠNO ZAPRIMLJENA
                        </p>

                        <h2 style="margin:0 0 18px;color:#172554;font-size:27px;line-height:1.3;">
                            Hvala na narudžbi!
                        </h2>

                        <p style="margin:0 0 12px;font-size:15px;line-height:1.7;">
                            Poštovani {{ $order->first_name }},
                        </p>

                        <p style="margin:0;color:#64748b;font-size:14px;line-height:1.8;">
                            Uspješno smo zaprimili vašu narudžbu.
                            Hvala što ste izabrali zvanični webshop
                            FK Radnika i što ste dio naše plavo-bijele porodice.
                        </p>
                    </td>
                </tr>

                <!-- ORDER NUMBER -->
                <tr>
                    <td style="padding:5px 30px 30px;">

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:#eef0ff;border-left:4px solid #3036cc;">
                            <tr>
                                <td style="padding:20px;">

                                    <p style="margin:0 0 8px;color:#6670a0;font-size:11px;font-weight:bold;letter-spacing:1px;">
                                        BROJ NARUDŽBE
                                    </p>

                                    <p style="margin:0 0 9px;color:#3036cc;font-size:23px;font-weight:bold;overflow-wrap:anywhere;">
                                        {{ $order->order_number }}
                                    </p>

                                    <p style="margin:0;color:#59649a;font-size:12px;">
                                        Status: Zaprimljena — čeka obradu
                                    </p>

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- PRODUCTS -->
                <tr>
                    <td style="padding:0 30px 12px;">

                        <h3 style="margin:0 0 18px;color:#172554;font-size:19px;">
                            Vaši proizvodi
                        </h3>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="border-collapse:collapse;">

                            @foreach ($order->items as $item)

                                @php
                                    $imagePath = $item->product?->main_image;

                                    $imageUrl = null;

                                    if ($imagePath) {
                                        if (
                                            str_starts_with($imagePath, 'https://') ||
                                            str_starts_with($imagePath, 'http://')
                                        ) {
                                            $imageUrl = $imagePath;
                                        } else {
                                            $cleanPath = ltrim($imagePath, '/');

                                            if (str_starts_with($cleanPath, 'storage/')) {
                                                $imageUrl = $assetBase . '/' . $cleanPath;
                                            } else {
                                                $imageUrl = $assetBase . '/storage/' . $cleanPath;
                                            }
                                        }
                                    }
                                @endphp

                                <tr>
                                    <td style="padding:17px 0;border-bottom:1px solid #e8ebf2;">

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>

                                                <!-- PRODUCT IMAGE -->
                                                <td width="94" valign="top"
                                                    style="width:94px;padding:0;">

                                                    @if ($imageUrl)
                                                        <img
                                                            src="{{ $imageUrl }}"
                                                            alt="{{ $item->product_name }}"
                                                            width="86"
                                                            style="display:block;width:86px;height:auto;max-width:100%;border:1px solid #edf0f5;border-radius:6px;"
                                                        >
                                                    @else
                                                        <table role="presentation" width="86" cellpadding="0"
                                                               cellspacing="0"
                                                               style="background:#eef0ff;">
                                                            <tr>
                                                                <td align="center"
                                                                    style="height:86px;color:#3036cc;font-size:11px;">
                                                                    FK RADNIK
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    @endif

                                                </td>

                                                <!-- PRODUCT INFO -->
                                                <td valign="top" style="padding:0 0 0 12px;">

                                                    <p style="margin:0 0 7px;color:#172554;font-size:14px;font-weight:bold;line-height:1.5;">
                                                        {{ $item->product_name }}
                                                    </p>

                                                    @if ($item->size)
                                                        <p style="margin:0 0 5px;color:#64748b;font-size:12px;">
                                                            Veličina: {{ $item->size }}
                                                        </p>
                                                    @endif

                                                    @if ($item->customization_name || $item->customization_number)
                                                        <p style="margin:0 0 5px;color:#64748b;font-size:12px;line-height:1.5;">
                                                            Personalizacija:
                                                            {{ $item->customization_name }}
                                                            @if ($item->customization_number)
                                                                #{{ $item->customization_number }}
                                                            @endif
                                                        </p>
                                                    @endif

                                                    <p style="margin:0 0 7px;color:#64748b;font-size:12px;">
                                                        Količina: {{ $item->quantity }}
                                                    </p>

                                                    <p style="margin:0;color:#3036cc;font-size:15px;font-weight:bold;">
                                                        {{ $money($item->line_total) }}
                                                    </p>

                                                </td>

                                            </tr>
                                        </table>

                                    </td>
                                </tr>

                            @endforeach

                        </table>
                    </td>
                </tr>

                <!-- TOTALS -->
                <tr>
                    <td style="padding:12px 30px 30px;">

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">

                            <tr>
                                <td style="padding:8px 0;color:#64748b;font-size:13px;">
                                    Međuzbir
                                </td>
                                <td align="right" style="padding:8px 0;color:#172554;font-size:13px;">
                                    {{ $money($order->subtotal) }}
                                </td>
                            </tr>

                            @if ((float) $order->discount_amount > 0)
                                <tr>
                                    <td style="padding:8px 0;color:#16815c;font-size:13px;">
                                        Popust
                                        @if ($order->discount_percent)
                                            ({{ number_format((float) $order->discount_percent, 0) }}%)
                                        @endif
                                    </td>

                                    <td align="right" style="padding:8px 0;color:#16815c;font-size:13px;">
                                        -{{ $money($order->discount_amount) }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td style="padding:8px 0;color:#64748b;font-size:13px;">
                                    Dostava
                                </td>
                                <td align="right" style="padding:8px 0;color:#172554;font-size:13px;">
                                    @if ((float) $order->shipping_amount > 0)
                                        {{ $money($order->shipping_amount) }}
                                    @else
                                        Besplatno
                                    @endif
                                </td>
                            </tr>

                        </table>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="margin-top:16px;background:#3036cc;">
                            <tr>
                                <td style="padding:20px;color:#ffffff;font-size:14px;font-weight:bold;">
                                    UKUPNO ZA PLAĆANJE
                                </td>

                                <td align="right" style="padding:20px;color:#ffffff;font-size:21px;font-weight:bold;">
                                    {{ $money($order->total) }}
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- DELIVERY -->
                <tr>
                    <td style="padding:0 30px 28px;">

                        <h3 style="margin:0 0 15px;color:#172554;font-size:18px;">
                            Dostava i plaćanje
                        </h3>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:#f5f7fc;">
                            <tr>
                                <td style="padding:20px;">

                                    <p style="margin:0 0 10px;color:#24304b;font-size:13px;">
                                        <strong>Način plaćanja:</strong> Pouzećem
                                    </p>

                                    @if ($order->delivery_method === 'courier')

                                        <p style="margin:0 0 8px;color:#24304b;font-size:13px;">
                                            <strong>Način isporuke:</strong> Kurirska dostava
                                        </p>

                                        <p style="margin:0;color:#64748b;font-size:13px;line-height:1.7;">
                                            {{ $order->address }}<br>
                                            {{ $order->postal_code }} {{ $order->city }}
                                        </p>

                                    @else

                                        <p style="margin:0;color:#24304b;font-size:13px;">
                                            <strong>Način preuzimanja:</strong> Lično preuzimanje
                                        </p>

                                    @endif

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- NEXT STEPS -->
                <tr>
                    <td style="padding:0 30px 36px;">

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                               style="background:#eef0ff;">
                            <tr>
                                <td style="padding:19px 20px;color:#46527c;font-size:13px;line-height:1.8;">

                                    <strong style="color:#3036cc;">
                                        Šta slijedi?
                                    </strong>

                                    <br>

                                    Vaša narudžba je zaprimljena i čeka obradu.
                                    Ukoliko budu potrebne dodatne informacije,
                                    kontaktiraćemo vas putem podataka
                                    koje ste naveli prilikom kupovine.

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td align="center" style="background:#252bb0;padding:30px 22px;">

                        <p style="margin:0 0 10px;color:#ffffff;font-size:14px;font-weight:bold;letter-spacing:1px;">
                            IGRAJTE ZA NAS, ZA KLUB, ZA GRAD...
                        </p>

                        <p style="margin:0 0 10px;color:#dce0ff;font-size:12px;">
                            FK Radnik Bijeljina · Zvanični webshop
                        </p>

                        <p style="margin:0 0 14px;font-size:12px;">
                            <a href="mailto:shop.radnik@fkradnikbijeljina.com"
                               style="color:#ffffff;text-decoration:underline;">
                                shop.radnik@fkradnikbijeljina.com
                            </a>
                        </p>

                        <p style="margin:0;color:#bfc6f5;font-size:11px;line-height:1.6;">
                            Ova poruka je automatski generisana
                            nakon uspješno zaprimljene narudžbe.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
