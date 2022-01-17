<?php

namespace App\Http\Controllers\Bills;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use App\Models\Bills;
use App\Models\SoldProducts;
use App\Models\QuantityProducts;
use App\Models\HistoryChangeProducts;
use App\Models\Products;
use Carbon\Carbon;

class BillsController extends Controller
{

    public function index(Request $request)
    {
        try {
            $bills = Bills::all();
            foreach ($bills as &$bill) {
                $bill->warehouse;
                $bill->client;
            }
            return response()->json(['data' => $bills]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            $dataBill = $request->only('total', 'discounts', 'subtotal', 'warehouse_id', 'client_id', 'observations');
            $validator = $this->validator($dataBill);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages()
                ], 200);
            }

            $id = $request->input('id');

            return response()->json(['error' => $id]);

            if (isset($id) && $id != 'null') {
                $bill = Bills::find($id);
                $data['last_update_by'] = $request->user()->id;
                $bill->update($dataBill);
            } else {
                $dataBill['create_by'] = $request->user()->id;
                $bill = Bills::create($dataBill);
            }

            if(isset($request->products)){
                $products = json_decode($request->input('products'));
                foreach ($products as $product) {
                    SoldProducts::create([
                        'product_id' => $product->id,
                        'bill_id' => $bill->id,
                        'quantity' => $product->quantity,
                        'total' => $product->total,
                        'discount' => $product->discount
                    ]);
                    $dataQuantity = QuantityProducts::where(['warehouse_id' => $bill->warehouse_id, 'product_id' => $product->id])->first();
                    $dataQuantity->update([
                        'quantity'  =>  $dataQuantity->quantity - $product->quantity,
                        'last_update_by' => $request->user()->id
                    ]);
                    $productRow = Products::find($product->id);
                    $productRow->update([
                        'units'  =>  $productRow->units - $product->quantity,
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
            }
            return response()->json(['success' => 'OK']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    protected function validator(array $data)
    {
        $rules = [
            'total' => ['required'],
            'discounts' => ['required'],
            'subtotal' => ['required'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'client_id' => ['required', 'exists:clients,id']
        ];
        return Validator::make($data, $rules);
    }

    public function download(Request $request)
    {

        $bill = Bills::find($request->id);
        $bill->client;
        $bill->warehouse;
        $bill->products = $bill->products;
        $bill['total'] = number_format($bill['total'], 0, '.', '.');
        $bill['totalBruto'] = number_format($bill['subtotal'], 0, '.', '.');
        $bill['subtotal'] = intval($bill['subtotal']) - intval($bill['discounts']);
        $bill['subtotal'] = number_format($bill['subtotal'], 0, '.', '.');
        $bill['discounts'] = number_format($bill['discounts'], 0, '.', '.');
        $bill['date'] = Carbon::parse($bill['created_at'])->format('M d Y');
        $bill['now'] = Carbon::now()->format('M d Y');
        $bill['year'] = Carbon::now()->format('Y');

        foreach ($bill->products as &$product) {
            $product->product = $product->product;
            
            $product['product']['price'] = $product['total']/$product['quantity'];
            $product['product']['price'] = number_format($product['product']['price'], 0, '.', '.');
            $product['total'] = number_format($product['total'], 0, '.', '.');
            $product['discount'] = number_format($product['discount'], 0, '.', '.');
        }

        return \PDF::loadView('pdf.bill', $bill)->stream('archivo.pdf');
    }

    public function getLinkPdf(Request $request)
    {
        $rule = [
            'id' => ['require','exists:bills']
        ];
        
        $id = $request->input('id');
        if(!isset($id)){
            return response()->json([
                'error' => 'Field id is required'
            ]);
        }

        $url = URL::temporarySignedRoute('getPdfBill', now()->addMinutes(2), ['id' => $id]);

        return response()->json([ 'url' => $url ]);
    }

}
