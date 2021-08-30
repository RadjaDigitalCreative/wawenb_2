<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use DateTime;

class UserApi extends Controller
{
    public function user_status(Request $request)
    {
        try {
            $bayar = DB::table('users')
            ->join('role_pay', 'users.id', '=', 'role_pay.user_id')
            ->where('users.id', $request->id)
            ->first();

            $download = DB::table('harga')
            ->where('hari', $bayar->hari)
            ->select('jumlah_nomor', 'database_nomor')
            ->first();

            $fdate = $bayar->dibayar;
            $tdate = now()->format('Y-m-d');
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime2->diff($datetime1);
            $days = $interval->format('%a');

            if(now()->format('Y-m-d') > $bayar->dibayar){
                return response()->json([
                    'user_notifikasi' => $bayar,
                    'masa_pemakaian' => 'tidak_aktif',
                    'downloadable_content' => $download,
                    'status_code'   => 200,
                    'msg'           => 'success',
                ], 200);
            }elseif(now()->format('Y-m-d') < $bayar->dibayar){
                return response()->json([
                    'user_notifikasi' => $bayar,
                    'masa_pemakaian' => $days,
                    'downloadable_content' => $download,
                    'status_code'   => 200,
                    'msg'           => 'success',
                ], 200);
            }

        }catch (\Exception $exception) {
            return response()->json([
                'user_notifikasi' => 'Not Found',
                'status_code'   => 403,
                'msg'           => 'Not Found',
            ], 403);
        }
    }
    public function notif_user(Request $request)
    {
        $bayar = DB::table('users')
        ->join('role_pay', 'users.id', '=', 'role_pay.user_id')
        ->where('role_pay.pay', 1)
        ->where('role_pay.is_read', NULL)
        ->where('users.id', $request->id)
        ->get();
        return response()->json([
            'user_notifikasi' => $bayar,
            'status_code'   => 200,
            'msg'           => 'success',
        ], 200);
    }
    public function index(Request $request)
    {
        $data = DB::table('users')
        ->join('role_pay', 'users.id','=', 'role_pay.user_id')
        ->where('users.id', $request->id)
        ->paginate(10);
        return response()->json([
            'users' => $data,
            'status_code'   => 200,
            'msg'           => 'success',
        ], 200);
    }
    public function notif(Request $request)
    {
        $bayar = DB::table('users')
        ->join('role_pay', 'users.id', '=', 'role_pay.user_id')
        ->where('role_pay.pay', 1)
        ->where('role_pay.is_read', NULL)
        ->where('users.id_data', $request->role)
        ->get();
        return response()->json([
            'notifikasi' => $bayar,
            'status_code'   => 200,
            'msg'           => 'success',
        ], 200);
    }
    public function hapus(Request $request)
    {
        $bayar = DB::table('users')
        ->join('role_pay', 'users.id', '=', 'role_pay.user_id')
        ->where('role_pay.user_id', $request->id)
        ->update([
            'role_pay.is_read' => 1
        ]);
        return response()->json([
            'notif_hapus' => $bayar,
            'status_code'   => 200,
            'msg'           => 'success',
        ], 200);
    }
    public function notif_user_belum_bayar(Request $request)
    {
        $data = DB::table('users')
        ->join('role_pay', 'users.id','=', 'role_pay.user_id')
        ->where('users.id', $request->id)
        ->select([
            'role_pay.pay AS sudah_bayar'
        ])
        ->paginate(10);
        return response()->json([
            'users' => $data,
            'status_code'   => 200,
            'msg'           => 'success',
        ], 200);
    }
    public function bayar(Request $request)
    {
     $image = $request->file('image');
     $image_name = rand() . '.' . $image->getClientOriginalExtension();
     $image->move(public_path('images'), $image_name);

     $ami = DB::table('harga')
     ->where('hari', $request->jumlah_hari)
     ->first();

     $data = DB::table('users')
     ->join('role_pay', 'users.id','=', 'role_pay.user_id')
     ->where('users.id', $request->id)
     ->update([
       'role_pay.tgl_bayar'  => now(),
       'role_pay.hari'  => $request->jumlah_hari,
       'role_pay.harga'  => $ami->harga,
       'role_pay.image'  => $image_name,
       'role_pay.pay' => 1,
       'role_pay.created_at'  => now(),
       'role_pay.updated_at'  => now(),
   ]);
     return response()->json([
       'bayar' => $data,
       'status_code'   => 200,
       'msg'           => 'success',
   ], 200);

 }
 public function bayar_konfirmasi(Request $request)
 {
   $asu = DB::table('harga')
   ->where('harga', $request->harga)
   ->first();
   $dibayar = '+'.$asu->hari.' days';
   $data = DB::table('role_pay')
   ->where('user_id', $request->id)
   ->update([
      'dibayar'  => date('Y-m-d', strtotime($dibayar, strtotime(now()))),
      'pay' => 2,
      'created_at' => now(),
      'updated_at' => now(),
  ]);
   return response()->json([
      'bayar_konfirmasi' => $data,
      'status_code'   => 200,
      'msg'           => 'success',
  ], 200);
}
public function daftar_harga(Request $request)
{
    $data = DB::table('harga')
    ->paginate(10);
    return response()->json([
        'daftar_harga' => $data,
        'status_code'   => 200,
        'msg'           => 'success',
    ], 200);
}
}
