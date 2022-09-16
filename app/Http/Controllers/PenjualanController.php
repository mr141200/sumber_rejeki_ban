<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PenjualanController extends Controller
{
    public function index()
    {

        return view('penjualan.index');
    }

    public function data()
    {

        Penjualan::where('total_item', 0)->delete();
        $penjualan = DB::table('vw_penjualan')
        ->orderBy('id_penjualan', 'desc')->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('no_invoice', function ($penjualan) {
                return $penjualan->no_nota;
            })
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('kode_member', function ($penjualan) {
                return $penjualan->kode_member ?? '';
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->kasir ?? '';
            })
            ->addColumn('aksi', function ($penjualan) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-dark btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="editForm(`'. route('penjualan.update', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_member'])
            ->make(true);
    }

    public function create()
    {
        $penjualan = new Penjualan();
        $penjualan->id_member = null;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->id_user = auth()->id();
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {

        DB::transaction(function () use ($request) { 
            $penjualan = Penjualan::findOrFail($request->id_penjualan);
            $penjualan->id_member = $request->id_member;
            $penjualan->total_item = $request->total_item;
            $penjualan->total_harga = $request->total;
            $penjualan->diskon = $request->diskon;
            $penjualan->bayar = $request->bayar;
            // $request['diterima'] = str_replace('.','',$request->diterima);
            $penjualan->diterima = $request->bayar;
            $penjualan->update();

            $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
            foreach ($detail as $item) {
                $item->diskon = $request->diskon;
                $item->update();

                $produk = Produk::find($item->id_produk);
                $produk->stok -= $item->jumlah;
                $produk->update();
            }
        });
        

        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_jual', function ($detail) {
                return 'Rp. '. format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. '. format_uang($detail->subtotal);
            })->addColumn('diterima', function ($detail) {
                return $detail->subtotal;
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        $detail    = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
        
        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
        $no_nota = "INV".str_pad($penjualan->id_penjualan,12,"0",STR_PAD_LEFT);

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail','no_nota'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }

    public function doUpdate(Request $request, $id)
    {
        $penjualan = Penjualan::find($id);
        $penjualan->total_harga = $request->total_harga;
        $penjualan->bayar = $request->total_harga;
        $penjualan->created_at = $request->tanggal;
        $penjualan->update();
        // return response(null, 204);
        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $penjualan = Penjualan::find($id);
        $respon = [
            'id_penjualan' => $penjualan->id_penjualan,
            'total_harga' => $penjualan->total_harga,
            'created_at' => $penjualan->created_at->format('Y-m-d')
        ];
        return response()->json($respon);
    }

}
