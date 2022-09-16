@extends('layouts.master')

@section('title')
    Daftar Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm('{{ route('produk.store') }}')" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
                    <button onclick="deleteSelected('{{ route('produk.delete_selected') }}')" class="btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                    <!-- <button onclick="cetakBarcode('{{ route('produk.cetak_barcode') }}')" class="btn btn-info btn-xs btn-flat"><i class="fa fa-barcode"></i> Cetak Barcode</button> -->
                </div>
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-produk">
                    @csrf
                    <table id="myTable" class="table table-bordered table-striped" >
                        <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <!-- <th width="5%">No</th> -->
                            <!-- <th>Kode</th> -->
                            <th>Nama</th>
                            <th>Stok</th>
                            <th>Ukuran</th>
                            <th>Ring</th>
                            <th>Harga Beli</th>
                            <!-- <th>Harga Jual</th> -->
                            <!-- <th>Diskon</th> -->
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th width="5%"><i class="fa fa-cog"></i></th>
                        </thead>
                        <tfoot>
                            <th width="5%">
                            </th>
                            <!-- <th></th> -->
                            <!-- <th>Kode</th> -->
                            <th>Nama</th>
                            <th>Stok</th>
                            <th>Ukuran</th>
                            <th>Ring</th>
                            <th>Harga Beli</th>
                            <!-- <th>Harga Jual</th> -->
                            <!-- <th>Diskon</th> -->
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th width="5%"></th>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('produk.form')
@endsection

@push('css')
<style>
    tfoot {
        display: table-row-group;
    }

    thead input {
        width: 100%;
    }
    tfoot input {
        width: 100%;
    }
    table.dataTable th,
    table.dataTable td {
    white-space: nowrap;
    }

</style>
@endpush

@push('scripts')
<script>
    let table;

    $(function () {
        $('#myTable tfoot th').each( function () {
            var title = $('#myTable thead th').eq( $(this).index() ).text();
            if(title.trim() !== '' && title.trim() !== 'No'){
                $(this).html( '<input type="text" placeholder="Cari '+title+'" />' );

            }
        } );
        table = $('#myTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('produk.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                // {data: 'DT_RowIndex', searchable: false, sortable: false},
                // {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'stok'},
                {data: 'ukuran'},
                {data: 'ring'},
                {data: 'harga_beli'},
                // {data: 'harga_jual'},
                // {data: 'diskon'},
                {data: 'nama_kategori'},
                {data: 'merk'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
        });
        table.columns().every( function () {
            var that = this;
            $( 'input', this.footer() ).on( 'keyup change', function () {
                that
                    .search( this.value )
                    .draw();
            } );
        } );
        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
                        return;
                    });
            }
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
        
        $('#harga_beli').keyup(function(e){
            $(this).val(format($(this).val()));
        });
        $('#harga_jual').keyup(function(e){
            $(this).val(format($(this).val()));
        });
        $('#ukuran').keyup(function(e){
            var temp = $(this).val();
            // if(temp.split('-').length < 2){
            //     $(this).val('');
            // }
            $(this).val(createMask(temp));
        });

    });

    function createMask(string){
        return string.replace(/(\d{3})(\d{2})(\d{2})/,"$1-$2-$3");
    }

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_produk]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_produk]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=nama_produk]').val(response.nama_produk);
                $('#modal-form [name=id_kategori]').val(response.id_kategori);
                $('#modal-form [name=merk]').val(response.merk);
                $('#modal-form [name=ukuran]').val(response.ukuran);
                $('#modal-form [name=ring]').val(response.ring);
                $('#modal-form [name=harga_beli]').val(response.harga_beli);
                $('#modal-form [name=harga_jual]').val(response.harga_jual);
                $('#modal-form [name=diskon]').val(response.diskon);
                $('#modal-form [name=stok]').val(response.stok);
            })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function deleteSelected(url) {
        if ($('input:checked').length > 1) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, $('.form-produk').serialize())
                    .done((response) => {
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        } else {
            alert('Pilih data yang akan dihapus');
            return;
        }
    }

    function cetakBarcode(url) {
        if ($('input:checked').length < 1) {
            alert('Pilih data yang akan dicetak');
            return;
        } else if ($('input:checked').length < 3) {
            alert('Pilih minimal 3 data untuk dicetak');
            return;
        } else {
            $('.form-produk')
                .attr('target', '_blank')
                .attr('action', url)
                .submit();
        }
    }
    var format = function(num){
        var str = num.toString().replace("$", ""), parts = false, output = [], i = 1, formatted = null;
        if(str.indexOf(",") > 0) {
            parts = str.split(",");
            str = parts[0];
        }
        str = str.split("").reverse();
        for(var j = 0, len = str.length; j < len; j++) {
            if(str[j] != ".") {
                output.push(str[j]);
                if(i%3 == 0 && j < (len - 1)) {
                    output.push(".");
                }
                i++;
            }
        }
        formatted = output.reverse().join("");
        return( formatted + ((parts) ? "," + parts[1].substr(0, 2) : ""));
    };
</script>
@endpush