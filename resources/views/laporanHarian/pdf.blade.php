<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pendapatan</title>
    <style>
        .table {
            font-size: 10px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body>
    <h3 class="text-center" style="left: 250.558px; top: 70.4364px;">SUMBER REJEKI BAN</h3>
    <h3 class="text-center">Laporan Pendapatan</h3>
    <h4 class="text-center">
        Tanggal {{ tanggal_indonesia($awal, false) }}
        s/d
        Tanggal {{ tanggal_indonesia($akhir, false) }}
    </h4>
    <br><br>
<table class="table table-striped">
        <thead>
            <tr>
                 <!-- <th width="5%">No</th> -->
                <th>Tanggal</th>
                <th>Invoice</th>
                <th>Detail Transaksi</th>
                <th>Status</th>
                <th>Kondisi</th>                
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Modal</th>
                <th>Profit</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total = 0;
                $pemasukan = 0;
            @endphp
            @foreach ($data as $row)
            @php
                $row->transaction_date = tanggal_indonesia($row->transaction_date, false);
                $row->transaction_cost = number_format(($row->transaction_cost), 0, ',', '.');
                $row->total_transaction =number_format(($row->total_transaction), 0, ',', '.');
                $row->buying_price = number_format(($row->buying_price), 0, ',', '.');
                $row->profit =  number_format(($row->profit), 0, ',', '.');
            @endphp
                <tr>
                    @foreach ($row as $col)
                        <td>{{ $col }}</td>
                    @endforeach
                </tr>
            @php
                $row->profit = str_replace(".", "", $row->profit);
                $row->transaction_cost = str_replace(".","", $row->transaction_cost);
                $row->total_transaction = str_replace(".","", $row->total_transaction);
                $pemasukan = $pemasukan + $row->total_transaction;
                $total = $total +  $row->profit -  $row->transaction_cost;
            @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align:right">Total Pemasukan : Rp. {{number_format(($pemasukan),0,',','.')}}</th>
                 <th colspan="9" style="text-align:right">Keuntungan : Rp. {{number_format(($total), 0, ',', '.')}}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>