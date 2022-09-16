<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class LaporanHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporanHarian.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        // dd($awal.'--------------'.$akhir);
        $awalDate =!empty($awal) ? Carbon::createFromFormat('Y-m-d',  $awal) : Carbon::now();
        $akhirDate = !empty($akhir) ? Carbon::createFromFormat('Y-m-d',  $akhir) : Carbon::now();
        $dataView = DB::table('vw_transaksi_harian')
            ->whereDate('transaction_date', '>=',  $awalDate)
            ->whereDate('transaction_date', '<=',  $akhirDate)
            ->get();
        return $dataView;
    }

    public function data($awal, $akhir)
    {
        $dataView = $this->getData($awal, $akhir);
        return datatables()
        ->of($dataView)
        ->addIndexColumn()
        ->addColumn('transaction_date', function ($dataView) {
            return tanggal_indonesia($dataView->transaction_date, false);
        })
        ->addColumn('invoice_no', function ($dataView) {
            return $dataView->invoice_no;
        })
        ->addColumn('transaction_detail', function ($dataView) {
            return $dataView->transaction_detail;
        })
        ->addColumn('transaction_type', function ($dataView) {
            return $dataView->transaction_type;
        })
        ->addColumn('product_condition', function ($dataView) {
            return $dataView->product_condition;
        })
        ->addColumn('total_transaction', function ($dataView) {
            return 'Rp. '. format_uang($dataView->total_transaction);
        })
        ->addColumn('transaction_cost', function ($dataView) {
            return 'Rp. '. format_uang($dataView->transaction_cost);
        })
        ->addColumn('buying_price', function ($dataView) {
            return 'Rp. '. format_uang($dataView->buying_price);
        })
        ->addColumn('profit', function ($dataView) {
            return 'Rp. '. format_uang($dataView->profit);
        })
        ->make(true);
    }

    public function exportPDF($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);
        // dd($data);
        
        $pdf = null;

        try {
            $pdf  = PDF::loadView('laporanHarian.pdf', compact('awal', 'akhir', 'data'));
            // $pdf  = PDF::loadView('laporanHarian.pdf', compact('tesPasram'));
        } catch (\Throwable $th) {
            dd($th);
        }
        if(!empty($pdf)){
            set_time_limit(300);
            $pdf->setPaper('a4', 'potrait');
        }else{
            dd('babiii');
        }
        
        return $pdf->stream('Laporan-pendapatan-harian-'. date('Y-m-d-his') .'.pdf');
    }
}
