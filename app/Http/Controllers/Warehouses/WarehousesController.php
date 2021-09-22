<?php

namespace App\Http\Controllers\Warehouses;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Warehouses;

class WarehousesController extends Controller
{
    public function index(Request $request){
        try {
            $warehouses = Warehouses::all();
            foreach ($warehouses as &$warehouse) {
                $warehouse->UserCreator;
                $warehouse->UserUpdate;
                $warehouse->UserDelete;
                $warehouse->QuantityProducts = $warehouse->QuantityProducts;
                foreach ($warehouse->QuantityProducts as &$QuantityProduct) {
                    $QuantityProduct->product;
                }
            }
            return response()->json([ 'data' => $warehouses ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function search(Request $request){
        try {
            $id = $request->input('id');
            $sart = $request->input('startDate');
            $end = $request->input('finishDate');
            $warehouse = Warehouses::find($id);
            $warehouse->UserCreator;
            $warehouse->UserUpdate;
            $warehouse->UserDelete;
            $warehouse->QuantityProducts = $warehouse->QuantityProducts;
            $warehouse->OutHistory->whereBetween('created_at',[$sart,$end]);
            $warehouse->InHistory->whereBetween('created_at',[$sart,$end]);
            foreach ($warehouse->QuantityProducts as &$QuantityProduct) {
                $QuantityProduct->product = $QuantityProduct->product;
                $QuantityProduct->product->supplier;
                $QuantityProduct->product->subcategory;
                $QuantityProduct->product->subcategory->category;
            }
            return response()->json([ 'data' => $warehouse ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function create(Request $request){
        try {
            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->messages()
                ], 200);
            }

            $data = $request->only('description','increase');

            $id = $request->input('id');

            if(isset($id) && $id != 'null'){
                $warehouse = Warehouses::find($id);
                $warehouse->update([
                    'description' => $data['description'],
                    'percent_to_change' => $data['increase'],
                    'last_update_by' => $request->user()->id
                ]);
            } else {
                $data['create_by'] = $request->user()->id;
                $warehouse = Warehouses::create($data);
            }

            return response()->json([ 'data' => $warehouse ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function delete($id){
        try {
            Warehouses::destroy($id);
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    protected function validator(array $data)
    {
        $rules = [
            'description' => ['required','string','max:2500']
        ];

        return Validator::make($data, $rules);
    }
}