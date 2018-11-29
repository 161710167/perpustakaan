<?php

namespace App\Http\Controllers;

use App\Pinjam_buku;
use App\Buku;
use App\Siswa;
use App\Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\DataTables;
use App\Traits\SessionFlash;
use DB;
use Excel;


class Pinjam_bukuController extends Controller
{
    use SessionFlash;
    public function jsonpinjam() {
        $pinjam = Pinjam_buku::where('tgl_kembali',null)->get();
        return Datatables::of($pinjam)
        ->addColumn('buku', function($pinjam){
            return $pinjam->Buku->judul;
        })
        ->addColumn('siswa', function($pinjam){
            return $pinjam->Siswa->nama;
        })
        ->addColumn('kelas', function($pinjam){
            return $pinjam->Kelas->kelas;
        })
        ->addIndexColumn()
        
        ->addColumn('action', function($pinjam){
            return '<a href="'.url('return_book/'.$pinjam->id).'" class="btn btn-xs btn-warning ">
            <i class=""></i> Kembalikan</a>';

            })
        ->rawColumns(['action','kelas','buku','siswa'])->make(true);
    }

    public function jsonpengembalian() {
        $pinjam = Pinjam_buku::where('tgl_kembali','!=',null);
        return Datatables::of($pinjam)
        ->addColumn('buku', function($pinjam){
            return $pinjam->Buku->judul;
        })
        ->addColumn('siswa', function($pinjam){
            return $pinjam->Siswa->nama;
        })
        ->addColumn('kelas', function($pinjam){
            return $pinjam->Kelas->kelas;
        })
        ->addIndexColumn()
        
        ->addColumn('status', function($pinjam){
            if ($pinjam->tgl_kembali > $pinjam->tgl_harus_kembali) {
                return '<font color="red"><b>TERLAMBAT</font></b>';
            }else{
                return '<font color="blue"><b>Tidak Terlambat</font></b>';
            }
         
            })
        ->rawColumns(['action','buku','siswa','kelas','status'])->make(true);
    }

    public function index() {
        $buku = Buku::all();
        $siswa = Siswa::all();
        $kelas = Kelas::all();
        return view('Pinjam_buku.index',compact('buku','siswa','kelas'));
    }

    public function index2() {
        $kelas = Kelas::all();
        $siswa = Siswa::all();
        $buku = Buku::all();
        return view('Pinjam_buku.index2');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'buku_id' => 'required',
            'kelas_id' => 'required',
            'siswa_id' => 'required',
            'tgl_pinjam' => 'required',
            'tgl_harus_kembali' => 'required',
            
        ],[
            'buku_id.required'=>':Buku harus diisi',
            'kelas_id.required'=>':Kelas harus diisi',
            'siswa_id.required'=>':Nama Siswa harus diisi',
            'tgl_pinjam.required'=>':Tanggal harus diisi',
            'tgl_harus_kembali.required'=>':Tanggal harus diisi'
            
        ]);
        $data = new Pinjam_buku;
        $data->buku_id = $request->buku_id;
        $data->kelas_id = $request->kelas_id;
        $data->siswa_id = $request->siswa_id;
        $data->tgl_harus_kembali = $request->tgl_harus_kembali;
        $data->tgl_pinjam = $request->tgl_pinjam;
        $data->save();

        $stok = Buku::where('id', $data->buku_id)->first();
        $stok->tersedia = $stok->tersedia -1;
        $stok->save();

        // $sss = Buku::where('id', $data->buku_id)->first();
        // $sss->tersedia = $sss->tersedia +1;
        // $sss->save();

        return response()->json(['success'=>true]);
    }

    public function store2(Request $request) {
        $this->validate($request, [
            'buku_id' => 'required',
            'siswa_id' => 'required',
            'kelas_id' => 'required',
            'tgl_pinjam' => 'required',
            'tgl_kembali' => 'required',
            'tgl_harus_kembali' => 'required', 
        ],[
            'buku_id.required'=>'Buku harus diisi',
            'siswa_id.required'=>'Nama Siswa harus diisi',
            'kelas_id.required'=>'Kelas harus diisi',
            'tgl_pinjam.required'=>'Tanggal harus diisi',
            'tgl_kembali.required'=>'Tanggal harus diisi',
            'tgl_harus_kembali.required'=>'Tanggal harus diisi'  
        ]);
        $data = new Pinjam_buku;
        $data->buku_id = $request->buku_id;
        $data->siswa_id = $request->siswa_id;
        $data->kelas_id = $request->kelas_id;
        $data->tgl_pinjam = $request->tgl_pinjam;
        $data->tgl_kembali = $request->tgl_kembali;
        $data->tgl_harus_kembali = $request->tgl_harus_kembali;
        $data->save();
        return response()->json(['success'=>true]);
    }
    
    public function edit($id) {
        $pinjam = Pinjam_buku::findOrFail($id);
        return $pinjam;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PinjamBuku  $pinjamBuku
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id) {
        $this->validate($request, [
            'buku_id' => 'required',
            'siswa_id' => 'required',
            'kelas_id' => 'required',
            'tgl_harus_kembali' => 'required',
            'tgl_pinjam' => 'required',
        ],[
            'buku_id.required'=>':Buku harus diisi',
            'siswa_id.required'=>':Nama Siswa harus diisi',
            'kelas_id.required'=>':Kelas harus diisi',
            'tgl_harus_kembali.required'=>':Tanggal harus diisi',
            'tgl_pinjam.required'=>':Tanggal harus diisi',   
        ]);
        $data = Pinjam_buku::findOrFail($id);
        $data->buku_id = $request->buku_id;
        $data->siswa_id = $request->siswa_id;
        $data->kelas_id = $request->kelas_id;
        $data->tgl_harus_kembali = $request->tgl_harus_kembali;
        $data->tgl_pinjam = $request->tgl_pinjam;
        $data->save();
        return response()->json(['success'=>true]);
    }
    public function get_siswa($kelas_id){
        $siswa = Siswa::where('kelas_id',$kelas_id)->get();
        return $siswa;
    }
    public function return_book($id){
        $kembali = Pinjam_buku::findOrFail($id);
        if ($kembali && $kembali->tgl_kembali == null) { 
            $kembali->tgl_kembali = date('Y-m-d');
            $kembali->save();
             $this->NotifFlash('success','','Berhasil ','pengembalian');
            return redirect('/pengembalian');

        }else{
            $this->NotifFlash('danger','','Gagal ','pengembalian');
            return redirect()->back();
        }
    }

    public function export_pengembalian(){
        $pengembalians = DB::table('pinjam_bukus')->get();

        $datapengembalians = "";
            if(count($pengembalians) >0 ){
                $datapengembalians .='<table border="1">
                <tr>
                    <th>Buku</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Tanggal Harus Kembali</th>  
                    <th>Status</th>
                </tr>';
            foreach ($pengembalians as $pengembalian) {
                $datapengembalians .= '
                <tr>
                    <td>'.$pengembalian->buku_id.'</td>
                    <td>'.$pengembalian->siswa_id.'</td>
                    <td>'.$pengembalian->kelas_id.'</td>
                    <td>'.$pengembalian->tgl_pinjam.'</td>
                    <td>'.$pengembalian->tgl_kembali.'</td>
                    <td>'.$pengembalian->tgl_harus_kembali.'</td>
                    <td>'.$pengembalian->hukuman.'</td>
                    
                </tr>';
            }
            $datapengembalians .='</table>';
        }
        header('Content-Type: application/xls');
        header('Content-Disposition: attachment; filename=File pengembalian.xls');
        echo $datapengembalians;
        // return $pengujungs;
    }

    public function export_peminjaman(){
        $peminjamans = DB::table('pinjam_bukus')->get();

        $datapeminjamans = "";
            if(count($peminjamans) >0 ){
                $datapeminjamans .='<table border="1">
                <tr>
                    <th>Judul Buku</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Harus Kembali</th>  
                </tr>';
            foreach ($peminjamans as $peminjaman) {
                $datapeminjamans .= '
                <tr>
                    <td>'.$peminjaman->buku_id.'</td>
                    <td>'.$peminjaman->siswa_id.'</td>
                    <td>'.$peminjaman->kelas_id.'</td>
                    <td>'.$peminjaman->tgl_pinjam.'</td>
                    <td>'.$peminjaman->tgl_harus_kembali.'</td>
                </tr>';
            }
            $datapeminjamans .='</table>';
        }
        header('Content-Type: application/xls');
        header('Content-Disposition: attachment; filename=File peminjaman.xls');
        echo $datapeminjamans;
        // return $pengujungs;
    }
}
