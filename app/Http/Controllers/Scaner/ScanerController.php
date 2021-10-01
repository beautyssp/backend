<?php

namespace App\Http\Controllers\Scaner;

use App\Events\RegisterScanerEvent;
use App\Http\Controllers\Controller;
use App\Models\Scaners;
use Illuminate\Http\Request;

class ScanerController extends Controller
{
    public function register(Request $request){
        try {
            $data = [
                'user_id' => $request->user()->id,
                'socket' => $request->input('socket')
            ];
            $device = Scaners::create($data);
            $event = event(new RegisterScanerEvent($device));
            return response()->json([ 'error' => $event, 'here' =>'asdad' ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }
}
