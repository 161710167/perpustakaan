<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Buku;
use Yajra\DataTables\Datatables;

class BukuController extends Controller
{
    public function list(){

        $bk = Buku::all();

        return Datatables::of($bk)
            ->addIndexColumn()
            ->addColumn('action', function($bk){
                return '<a onclick="edit('. $bk->id .')" class="btn btn-warning btn-xs"><i class="zmdi zmdi-edit"></i> Ubah</a> '.
                       '<a onclick="del('. $bk->id .')" class="btn btn-danger btn-xs"><i class="zmdi zmdi-delete"></i> Hapus</a>';
            })->make(true);
        }

    public function action(Request $request){
    	
        switch ($request->type) {
            case 'create':
                $this->validate($request,[
                    'judul' => 'required|unique:bukus,judul',
                    'pengarang' => 'required',
                    'tahun_terbit' => 'required',
                    'penerbit' => 'required',
                    'tersedia' => 'required',
                ],[
                    'judul.required' => 'Judul Buku Harus Diisi',
                    'judul.unique' => 'Judul Buku Telah Ada',
                    'pengarang.required' => 'pengarang Buku Harus Diisi',
                    'tahun_terbit.required' => 'tahun terbit Harus Diisi',
                    'penerbit.required' => 'penerbit Buku Harus Diisi',
                    'tersedia.required' => 'tersedia Buku Harus Diisi'
                ]);
                $bk = new Buku;
                $bk->judul = $request->judul;
                $bk->pengarang = $request->pengarang;
                $bk->tahun_terbit = $request->tahun_terbit;
                $bk->penerbit = $request->penerbit;
                $bk->tersedia = $request->tersedia;
                $bk->save();

                return response()->json([
                    'message' => 'Berhasil Menambah Data',
                ],200); 
            break;

            case 'update':
                $this->validate($request,[
                    'judul' => 'required|',
                    'pengarang' => 'required',
                    'tahun_terbit' => 'required',
                    'penerbit' => 'required',
                    'tersedia' => 'required',
                ],[
                    'judul.required' => 'Judul Buku Harus Diisi',
                    'pengarang.required' => 'pengarang Buku Harus Diisi',
                    'tahun_terbit.required' => 'tahun terbit Harus Diisi',
                    'penerbit.required' => 'penerbit Buku Harus Diisi',
                    'tersedia.required' => 'tersedia Buku Harus Diisi'
                ]);
                $bk = Buku::find($request->id);
                $bk->judul = $request->judul;
                $bk->pengarang = $request->pengarang;
                $bk->tahun_terbit = $request->tahun_terbit;
                $bk->penerbit = $request->penerbit;
                $bk->tersedia = $request->tersedia; 
                $bk->save();

                return response()->json([
                    'message' => 'Berhasil Mengubah Data',
                ],200); 
            break;
            
            case 'delete':
                $bk = Buku::find($request->id)->delete();

                return response()->json([
                    'message' => 'Berhasil Menghapus',
                ],200); 
            break;

             case 'show':
                $bk = Buku::find($request->id);

                return response()->json($bk,200); 
            break;

            default:
                return response()->json([
                    'error' => 'Bad Request',
                ],400);   
            break;
        }
    }
}
