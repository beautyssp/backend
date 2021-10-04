<?php

namespace App\Http\Controllers\Scaner;

use App\Events\RegisterScanerEvent;
use App\Events\ScanerEvent;
use App\Http\Controllers\Controller;
use App\Models\Scaners;
use Illuminate\Http\Request;

class ScanerController extends Controller
{
    public function register(Request $request){
        try {
            $scanerID = (int) $request->input('scanerID');
            $userID = $request->user()->id;
            $data = [
                'scaner' => $scanerID,
                'biller' => $userID
            ];
            broadcast(new RegisterScanerEvent($data))->toOthers();
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function sendEan(Request $request){
        try {
            $ean13 = $request->input('ean13');
            $scanerID = $request->user()->id;
            broadcast(new ScanerEvent($ean13, $scanerID))->toOthers();
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

}
