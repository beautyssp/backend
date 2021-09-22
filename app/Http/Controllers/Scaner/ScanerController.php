<?php

namespace App\Http\Controllers\Scaner;

use App\Events\ScanerEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScanerController extends Controller
{
    public function register(){
        try {
            $event = event(new ScanerEvent('Hello Test'));
            return response()->json([ 'error' => $event ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }
}
