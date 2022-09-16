<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota PDF</title>

    <style>
        table td {
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 14px;
        }
        table.data td,
        table.data th {
            border: 1px solid #ccc;
            padding: 5px;
        }
        table.data {
            border-collapse: collapse;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .center {
  text-align: center;
  padding: 10px;
}
p,h1,h3 {
    margin: 0px;
    /* margin-block-start: 0em; */
}
    </style>
</head>
<body>
    <table width="100%">
        <tr style="vertical-align:top;">
            <td width="65%">
                <div class="center">
                    <H1>SUMBER REJEKI BAN</H1>
                    <h3>MENJUAL ANEKA BAN & VELG</h3>
                    <p>{{ $setting->alamat }}</p>
                    <p>(021) 5500759</p>
                </div>
            </td>
            <td style="padding: 10px;">
                <div>
                    <p>Tanggal  : {{ tanggal_indonesia(date('Y-m-d')) }}</p>
                    <p>Nota No : {{ $no_nota ?? '' }}</p>
                </div>
            </td>
        </tr>
    </table>
<br>
    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <!-- <th>Kode</th> -->
                <th>Nama Barang</th>
                <th>Harga Satuan</th>
                <th>QTY</th>
                <!-- <th>Diskon</th> -->
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $key => $item)
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <!-- <td>{{ $item->produk->kode_produk }}</td> -->
                    <td class="text-right">{{ format_uang($item->harga_jual) }}</td>
                    <td class="text-center">{{ format_uang($item->jumlah) }}</td>
                    <!-- <td class="text-right">{{ $item->diskon }}</td> -->
                    <td class="text-right">{{ format_uang($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Terbilang : <b>{{ucwords(terbilang($penjualan->total_harga))}} Rupiah</b></td>
                <td class="text-right"><b>Jumlah</b></td>
                <td class="text-right"><b>Rp. {{ format_uang($penjualan->total_harga) }}</b></td>
            </tr>
            <!-- <tr>
                <td colspan="6" class="text-right"><b>Diskon</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diskon) }}</b></td>
            </tr> -->
            <!-- <tr>
                <td colspan="4" class="text-right"><b>Total Bayar</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->bayar) }}</b></td>
            </tr> -->
            <!-- <tr>
                <td colspan="6" class="text-right"><b>Diterima</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima) }}</b></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><b>Kembali</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</b></td>
            </tr> -->
        </tfoot>
    </table>

    <table width="100%">
        <tr style="vertical-align:top;">
            <td width="30%">
                <div class="center">
                    <p>Tanda Terima</p>
                </div>
            </td>

            <td width="40%">
                <div class="center">
                    <p><b><u>PERHATIAN !!!</u></b></p>
                    <p><i>Barang yang sudah dibeli tidak dapat dikembalikan</i></p>
                </div>
            </td>

            <td width="30%">
                 <div class="center">
                     <P>Hormat Kami,</p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>