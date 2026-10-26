<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Penyelesaian Rental - PokePartner</title>

    <style>
        @page {
            margin: 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #253252;
            font-size: 11px;
            background: #ffffff;
        }

        .header {
            background: #1d294f;
            color: white;
            padding: 25px;
            border-radius: 10px;
        }

        .brand {
            color: #ffd94b;
            font-size: 25px;
            font-weight: bold;
        }

        .subtitle {
            color: #dce5ff;
            font-size: 10px;
            margin-top: 6px;
            letter-spacing: 1px;
        }

        .receipt-number {
            font-size: 19px;
            font-weight: bold;
            margin-top: 20px;
        }

        .status {
            background: #def7e8;
            color: #167343;
            padding: 7px 12px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
            margin-top: 10px;
        }

        .section-title {
            color: #1d294f;
            font-size: 13px;
            font-weight: bold;
            margin: 22px 0 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f4d64e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #e8eaf0;
            vertical-align: top;
        }

        .label {
            color: #71809d;
            width: 40%;
        }

        .pokemon-card {
            background: #f5f7fc;
            padding: 15px;
            margin-bottom: 12px;
            border: 1px solid #e5e9f2;
        }

        .pokemon-photo {
            width: 90px;
            height: 90px;
            object-fit: contain;
        }

        .pokemon-name {
            font-size: 17px;
            font-weight: bold;
            color: #1d294f;
        }

        .pokemon-desc {
            color: #64718c;
            line-height: 1.7;
            margin-top: 7px;
        }

        .payment-table td {
            padding: 11px 8px;
            border-bottom: 1px solid #e8eaf0;
        }

        .amount {
            text-align: right;
            font-weight: bold;
        }

        .total-box {
            background: #1d294f;
            color: white;
            padding: 18px;
            margin-top: 15px;
        }

        .total-price {
            color: #ffd94b;
            font-size: 20px;
            font-weight: bold;
            text-align: right;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            color: #71809d;
            font-size: 10px;
            border-top: 1px dashed #cbd2df;
            padding-top: 18px;
            line-height: 1.7;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="brand">PokePartner</div>
        <div class="subtitle">OFFICIAL RENTAL RECEIPT</div>

        <div class="receipt-number">
            #RNT-{{ str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}
        </div>

        <div class="status">
            TRANSAKSI SELESAI
        </div>
    </div>

    <div class="section-title">
        Informasi Transaksi
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Trainer</td>
            <td>{{ $rental->trainer->full_name }}</td>
        </tr>
        <tr>
            <td class="label">Email Trainer</td>
            <td>{{ $rental->trainer->email }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Mulai</td>
            <td>{{ $rental->start_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jatuh Tempo</td>
            <td>{{ $rental->due_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Selesai</td>
            <td>{{ $rental->returned_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Tujuan Petualangan</td>
            <td>{{ $rental->destination }}</td>
        </tr>
    </table>

    <div class="section-title">
        Partner Pokémon yang Disewa
    </div>

    @foreach($rental->rentalDetails as $detail)
        @php
            $pokemon = $detail->pokemon;

            // Gunakan atribut deskripsi jika tersedia.
            $description = $pokemon
                ? ($pokemon->description
                    ?? $pokemon->pokemon_description
                    ?? null)
                : null;
        @endphp

        @if($pokemon)
            <div class="pokemon-card">
                <div class="pokemon-name">
                    {{ $pokemon->pokemon_name }}
                </div>

                <p>
                    Kode Pokémon:
                    {{ $pokemon->pokemon_code ?? '-' }}
                </p>

                <div class="pokemon-desc">
                    {{ $description ?: 'Partner Pokémon yang menemani petualangan Trainer.' }}
                </div>
            </div>
        @endif
    @endforeach

    <div class="section-title">
        Rincian Pembayaran
    </div>

    <table class="payment-table">
        <tr>
            <td>Biaya Sewa</td>
            <td class="amount">
                Rp {{ number_format($rental->total_price, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>Denda Keterlambatan</td>
            <td class="amount">
                Rp {{ number_format($rental->late_fee ?? 0, 0, ',', '.') }}
            </td>
        </tr>
    </table>

    <div class="total-box">
        <table>
            <tr>
                <td style="font-weight: bold;">
                    TOTAL AKHIR
                </td>
                <td class="total-price">
                    Rp {{ number_format(
                        $rental->total_price + ($rental->late_fee ?? 0),
                        0, ',', '.'
                    ) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <strong>Terima kasih telah berpetualang bersama PokePartner!</strong>
        <div>
            Bukti ini dibuat secara otomatis setelah
            pengembalian diverifikasi oleh Admin.
        </div>
        <div>
            PokePartner · Pokémon Rental Management System
        </div>
    </div>

</body>
</html>
