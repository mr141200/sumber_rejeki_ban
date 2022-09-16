<div class="modal fade" id="modal-produk" tabindex="-1" role="dialog" aria-labelledby="modal-produk">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Produk</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-produk">
                    <thead >
                        <th width="5%" style="text-align:center;">No</th>
                        <th style="text-align:center;">Kode</th>
                        <th style="text-align:center;">Nama</th>
                        <th style="text-align:center;">Merk</th>
                        <th style="text-align:center;">Ukuran</th>
                        <th style="text-align:center;">Ring</th>
                        <th style="text-align:center;">Harga Beli</th>
                        <th style="text-align:center;"><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @foreach ($produk as $key => $item)
                        @php
                             $item->harga_beli = number_format(($item->harga_beli), 0, ',', '.');
                        @endphp
                            <tr>
                                <td width="5%" style="text-align:center;">{{ $key+1 }}</td>
                                <td style="text-align:center;><span class="label label-success">{{ $item->kode_produk }}</span></td>
                                <td>{{ $item->nama_produk }}</td>
                                <td style="text-align:center;">{{ $item->merk }}</td>
                                <td style="text-align:center;">{{ $item->ukuran }}</td>
                                <td style="text-align:center;">{{ $item->ring }}</td>
                                <td style="text-align:right;">{{ $item->harga_beli }}</td>
                                <td style="text-align:center;">
                                    <a href="#" class="btn btn-primary btn-xs btn-flat"
                                        onclick="pilihProduk('{{ $item->id_produk }}', '{{ $item->kode_produk }}')">
                                        <i class="fa fa-check-circle"></i>
                                        Pilih
                                    </a>
                                </td>
                            </tr>
                            @php
                                $item->harga_beli = str_replace(".", "", $item->harga_beli);
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>