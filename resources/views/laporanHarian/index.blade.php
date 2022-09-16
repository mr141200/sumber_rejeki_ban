@extends('layouts.master')

@section('title')
    Laporan Pendapatan Harian {{ tanggal_indonesia($tanggalAwal, false) }} s/d {{ tanggal_indonesia($tanggalAkhir, false) }}
@endsection

@push('css')    
<link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Laporan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="updatePeriode()" class="btn btn-info btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Ubah Periode</button>
                <a href="{{ route('laporan-harian.export_pdf', [$tanggalAwal, $tanggalAkhir]) }}" target="_blank" class="btn btn-success btn-xs btn-flat"><i class="fa fa-file-excel-o"></i> Export PDF</a>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Detail Transaksi</th>
                        <th>Status</th>
                        <th>Kondisi</th>        
                        <th>Pemasukan</th>
                        <th>Pengeluaran</th>
                        <th>Modal</th>
                        <th>Profit</th>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="9" style="text-align:right">Net Profit :</th>
                            <th></th>
                        </tr>                  
                    </tfoot>
                        
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('laporanHarian.form')
@endsection

@push('scripts')
<script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    let table;

    $(function () {
        // table = $('.table-penjualan').DataTable({
        //     responsive: true,
        //     processing: true,
        //     serverSide: true,
        //     autoWidth: false,
        //     ajax: {
        //         url: '{{ route('penjualan.data') }}',
        //     },
        //     columns: [
        //         {data: 'DT_RowIndex', searchable: false, sortable: false},
        //         {data: 'tanggal'},
        //         {data: 'no_invoice'},
        //         {data: 'kode_member'},
        //         {data: 'total_item'},
        //         {data: 'total_harga'},
        //         {data: 'diskon'},
        //         {data: 'bayar'},
        //         {data: 'kasir'},
        //         {data: 'aksi', searchable: false, sortable: false},
        //     ]
        // });

        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('laporan-harian.data', [$tanggalAwal, $tanggalAkhir]) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'transaction_date'},
                {data: 'invoice_no'},
                {data: 'transaction_detail'},
                {data: 'transaction_type'},
                {data: 'product_condition'},                
                {data: 'total_transaction'},
                {data: 'transaction_cost'},
                {data: 'buying_price'},
                {data: 'profit'}
            ],
            // dom: 'Brt',
            // bSort: false,
            bPaginate: false,
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\Rp.,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
    
                // Total over all pages
                totalProfit = api
                    .column( 9 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Total over this page
                pageTotalProfit = api
                    .column( 9, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                 // Total over all pages
                 totalPengeluaran = api
                    .column( 7 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Total over this page
                pageTotalPengeluaran = api
                    .column( 7, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Update footer
                console.log(pageTotalProfit);
                console.log(totalProfit);
                console.log(pageTotalPengeluaran);
                console.log(totalPengeluaran);
                $( api.column( 9 ).footer() ).html(
                    'Rp '+(pageTotalProfit-pageTotalPengeluaran).toLocaleString().replace(/\./, 'a').replace(/,/g, '.')
                    // +' ( Rp '+ (totalProfit - totalPengeluaran) +' total)'
                );

            }
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    function updatePeriode() {
        $('#modal-form').modal('show');
    }
</script>
@endpush