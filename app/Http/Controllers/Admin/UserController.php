<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
     $role_pay = DB::table('users')
     ->join('role_pay', 'users.id', 'role_pay.user_id')
     ->where('role_pay.user_id', auth()->user()->id)
     ->first();
     $data = DB::table('users')
     ->join('role_pay', 'users.id', 'role_pay.user_id')

     ->select([  
        'users.id',
        'users.name',
        'users.email',
        'users.password',
        'users.image',
        'users.bio',
        'users.notelp',
        'users.id_data',
        'users.level',
        'users.created_at',
        'users.updated_at',
        'role_pay.user_id',
        'role_pay.dibayar',
        'role_pay.tgl_bayar',
        'role_pay.harga',
        'role_pay.hari',
        'role_pay.pay',
        'role_pay.image AS bukti',
        'role_pay.is_read',

    ])
     ->get();
     $data2 = DB::table('harga')->get();
     return view('admin.user.index', compact( 'data', 'data2', 'role_pay'));
 }
 public function index_filter(Request $request)
 {
    $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
    $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
    
    if (request()->date != '') {
      $date = explode(' - ' ,request()->date);
      $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
      $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';
  }
  $role_pay = DB::table('users')
  ->join('role_pay', 'users.id', 'role_pay.user_id')
  ->where('role_pay.user_id', auth()->user()->id)
  ->first();

  $data = DB::table('users')->whereBetween('role_pay.tgl_bayar', [$start, $end])
  ->where('role_pay.pay', $request->role_pay)
  ->join('role_pay', 'users.id', 'role_pay.user_id')
  ->select([  
    'users.id',
    'users.name',
    'users.email',
    'users.password',
    'users.image',
    'users.bio',
    'users.notelp',
    'users.id_data',
    'users.level',
    'role_pay.hari',
    'users.created_at',
    'users.updated_at',
    'role_pay.user_id',
    'role_pay.dibayar',
    'role_pay.tgl_bayar',
    'role_pay.harga',
    'role_pay.pay',
    'role_pay.image AS bukti',
    'role_pay.is_read',
])
  ->get();
  $data2 = DB::table('harga')->get();
  return view('admin.user.index', compact( 'data', 'data2', 'role_pay'));
}
public function payment()
{
 $role_pay = DB::table('users')
 ->join('role_pay', 'users.id', 'role_pay.user_id')
 ->where('role_pay.user_id', auth()->user()->id)
 ->first();
 $data = DB::table('harga')
 ->get();
 return view('admin.user.payment', compact('data', 'role_pay'));
}
public function payment_delete($id)
{
    $data = DB::table('harga')
    ->where('id', $id)
    ->delete();
    return redirect('/user/payment')->with('success', 'Data harga berhasil dihapus');

}
public function payment_store(Request $request)
{
    try {
        $count = count($request->harga);
        for ($i=0; $i < $count  ; $i++) { 
            if (empty($request->file('image')[$i]) && empty($request->jumlah_nomor[$i]) ) {
                DB::table('harga')
                ->insert([  
                    'harga' => $request->harga[$i],
                    'bulan' => $request->bulan[$i],
                    'promo' => $request->promo[$i],
                    'hari' => $request->bulan[$i] * 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }elseif($request->file('image')[$i] && $request->jumlah_nomor[$i]){
                $image = $request->file('image')[$i];
                $new_name = rand() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $new_name);
                DB::table('harga')
                ->insert([  
                    'harga' => $request->harga[$i],
                    'bulan' => $request->bulan[$i],
                    'promo' => $request->promo[$i],
                    'jumlah_nomor' => $request->jumlah_nomor[$i],
                    'database_nomor' => $new_name,
                    'hari' => $request->bulan[$i] * 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

        }
        return redirect('/user/payment')->with('success', 'Data harga berhasil ditambahkan');
        
    } catch (\Exception $exception) {
        return back()->with('warning', 'Import Downloadable Content Gagal. File Download harus dimasukkan!!!');
    }
}
public function payment_update(Request $request, $id)
{
    try {
        if (empty($request->file('image'))  ) {
            DB::table('harga')
            ->where('id', $id)
            ->update([  
                'harga' => $request->harga,
                'bulan' => $request->bulan,
                'promo' => $request->promo,
                'hari' => $request->bulan * 30,
                'jumlah_nomor' => $request->jumlah_nomor,
                'created_at' => now(),
                'updated_at' => now(),

            ]);
        }elseif($request->file('image')){
            $image = $request->file('image');
            $new_name = rand() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $new_name);
            DB::table('harga')
            ->where('id', $id)
            ->update([  
                'harga' => $request->harga,
                'bulan' => $request->bulan,
                'promo' => $request->promo,
                'jumlah_nomor' => $request->jumlah_nomor,
                'database_nomor' => $new_name,
                'hari' => $request->bulan * 30,
                'created_at' => now(),
                'updated_at' => now(),

            ]);
        }
        return redirect('/user/payment')->with('success', 'Data harga berhasil diupdate');

    }catch (\Exception $exception) {
        return back()->with('warning', 'Import Downloadable Content Gagal. File Download harus dimasukkan!!!');
    }
}
public function payment_bayar(Request $request)
{
    $image = $request->file('image');
    $new_name = rand() . '.' . $image->getClientOriginalExtension();
    $image->move(public_path('images'), $new_name);

    DB::table('role_pay')
    ->where('user_id', auth()->user()->id)
    ->update([  
        'tgl_bayar' => now(),
        'harga' => $request->harga,
        'image' => $new_name,
        'pay' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return redirect('/user/payment')->with('success', 'Data harga berhasil ditambahkan');
}
public function payment_bayar2(Request $request)
{
    $image = $request->file('image');
    $new_name = rand() . '.' . $image->getClientOriginalExtension();
    $image->move(public_path('images'), $new_name);

    DB::table('role_pay')
    ->where('user_id', auth()->user()->id)
    ->update([  
        'tgl_bayar' => now(),
        'harga' => $request->harga,
        'image' => $new_name,
        'pay' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return redirect('/bayar')->with('success', 'Pembayaran Berhasil, silahkan tunggu konfirmasi dari admin');
}
public function payment_konfirmasi(Request $request)
{
    $dibayar = '+'.$request->dibayar.' days';
    DB::table('role_pay')
    ->where('user_id', $request->user_id)
    ->update([  
        'dibayar' => date('Y-m-d', strtotime($dibayar, strtotime(now()))),
        'pay' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return redirect('/user')->with('success', 'Bukti Berhasil Dikonfirmasi');
}
public function destroy($id)
{
    DB::table('users')
    ->where('id', $id)
    ->delete();
    return redirect('/user')->with('success', 'User Berhasil dibunuh');
}
public function payment_read($id)
{
    DB::table('role_pay')
    ->where('user_id', $id)
    ->update([
        'is_read' => 1
    ]);
    return redirect('/notification')->with('success', 'User Berhasil dibunuh');
}
}
