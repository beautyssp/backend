<?php

namespace App\Http\Controllers\Bills;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Bills;
use App\Models\SoldProducts;
use App\Models\QuantityProducts;
use App\Models\HistoryChangeProducts;

class BillsController extends Controller
{
    public function create(Request $request){
        try {

            $dataBill = $request->only('total','discounts','subtotal','warehouse_id','client_id');
            $validator = $this->validator($dataBill);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages()
                ], 200);
            }

            $products = json_decode($request->input('products'));

            $id = $request->input('id');

            if(isset($id) && $id != 'null'){
                $bill = Bills::find($id);
                $data['last_update_by'] = $request->user()->id;
                $bill->update($dataBill);
            } else {
                $dataBill['create_by'] = $request->user()->id;
                $bill = Bills::create($dataBill);
            }

            foreach ($products as $product) {
                SoldProducts::create([
                    'product_id' => $product->id,
                    'bill_id' => $bill->id,
                    'quantity' => $product->quantity,
                    'total' => $product->total
                ]);
                $dataQuantity = QuantityProducts::where(['warehouse_id' => $bill->warehouse_id, 'product_id' => $product->id])->first();
                $dataQuantity->update([
                    'quantity'  =>  $dataQuantity->quantity - $product->quantity,
                    'last_update_by' => $request->user()->id
                ]);
                HistoryChangeProducts::create([
                    'quantity' => $product->quantity,
                    'product_id' => $product->id,
                    'warehouse_to' => NULL,
                    'warehouse_from' => $bill->warehouse_id,
                    'create_by' => $request->user()->id
                ]);
            }
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    protected function validator(array $data)
    {
        $rules = [
            'total' => ['required'],
            'discounts' => ['required'],
            'subtotal' => ['required'],
            'warehouse_id' => ['required','exists:warehouses,id'],
            'client_id' => ['required','exists:clients,id']
        ];
        return Validator::make($data, $rules);
    }
}
