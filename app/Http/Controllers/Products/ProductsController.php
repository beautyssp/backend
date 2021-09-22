<?php

namespace App\Http\Controllers\Products;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Products;
use App\Models\FilesModel;
use App\Models\Warehouses;
use App\Models\Subcategories;
use App\Models\QuantityProducts;
use App\Models\HistoryChangeProducts;

use Excel;
use DNS2D;
use DNS1D;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        return $row;
    }
    
    public function headingRow(): int
    {
        return 1;
    }
}

class ProductsController extends Controller
{
    public function index(Request $request){
        try {
            $products = Products::all();
            foreach ($products as &$product) {
                $product->health_register;
                $product->supplier;
                $product->subcategory = $product->subcategory;
                $product->subcategory->category;
            }
            return response()->json([ 'data' => $products ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function search(Request $request){
        try {
            $id = $request->input('id');
            $product = Products::find($id);
            $product->subcategory;
            $product->health_register;
            return response()->json([ 'data' => $product ]);
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

            $data = $request->only('ean','sku_plu','name','price','supplier_id','subcategory_id','brand','units');

            $id = $request->input('id');
            $file = $request->file('image');
            if(isset($id) && $id != 'null'){
                $product = Products::find($id);
                $data['last_update_by'] = $request->user()->id;
                $product->update($data);
                if(isset($file)){
                    $health_register = FilesModel::find($product->health_register_file_id);
                    if(isset($health_register)){
                        //delete file
                        $file_path = public_path().'/storage/products/'.$product->id.'/'.$health_register->name;
                        unlink($file_path);

                        //create new file
                        $name = time().$file->getClientOriginalName();
                        $extension = $file->getClientMimeType();
                        $path = $file->move(public_path().'/storage/products/'.$product->id.'/',$name);

                        //Update file record 
                        $health_register->update([
                            'name' => $name,
                            'type' => $extension,
                            'observations' => 'registro sanitario'
                        ]);
                    } else {
                        $this->createFile($file,$product);
                    }
                }
            } else {
                //$data['warehouse_id'] = 1; // Bodega general
                $data['create_by'] = $request->user()->id;
                $product = $this->createProduct($data);

                if(isset($file)){
                    $this->createFile($file,$product);
                }
            }

            return response()->json([ 'data' => $product ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    private function createProduct($data){
        $product = Products::create($data);

        QuantityProducts::create([
            'product_id' => $product->id,
            'warehouse_id' => 1,
            'quantity' => $product->units,
            'price' => $product->price,
            'create_by' => $data['create_by']
        ]);

        HistoryChangeProducts::create([
            'quantity' => $product->units,
            'product_id' => $product->id,
            'warehouse_to' => 1,
            'create_by' => $data['create_by']
        ]);

        return $product;
    }

    public function delete($id){
        try {
            Products::destroy($id);
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    public function changeWarehouse(Request $request){
        $data = $request->only('product_id','warehouse_to','warehouse_from','quantity');
        $warehouse = Warehouses::find($data['warehouse_from']);
        $product = Products::find($data['product_id']);
        $percent = $warehouse->percent_to_change;
        $quantity = QuantityProducts::where([
            ['product_id', '=', $data['product_id']],
            ['warehouse_id', '=', $data['warehouse_to']]
        ])->first();
        if(isset($quantity)){
            $quantity = QuantityProducts::find($quantity->id);
            $quantity->update([
                'quantity' => $data['quantity'] + $quantity['quantity'],
                'last_update_by' => $request->user()->id
            ]);
            HistoryChangeProducts::create([
                'quantity' => $data['quantity'],
                'product_id' => $data['product_id'],
                'warehouse_to' => $data['warehouse_to'],
                'warehouse_from' => $data['warehouse_from'],
                'create_by' => $request->user()->id
            ]);
        } else {
            $quantity = QuantityProducts::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_to'],
                'quantity' => $data['quantity'],
                'price' => $percent? ($product->price + ($product->price * ($percent/100))) : $product->price,
                'create_by' => $request->user()->id
            ]);
            HistoryChangeProducts::create([
                'quantity' => $data['quantity'],
                'product_id' => $data['product_id'],
                'warehouse_to' => $data['warehouse_to'],
                'warehouse_from' => $data['warehouse_from'],
                'create_by' => $request->user()->id
            ]);
        }
        $quantity = QuantityProducts::where([
            ['product_id', '=', $data['product_id']],
            ['warehouse_id', '=', $data['warehouse_from']]
        ])->first();
        $quantity->update([
            'quantity' => $quantity['quantity'] - $data['quantity'],
            'last_update_by' => $request->user()->id
        ]);
        /*if($quantity['quantity'] - $data['quantity'] == 0){
            $quantity->delete();
        } else {
            $quantity->update([
                'quantity' => $quantity['quantity'] - $data['quantity']
            ]);
        }*/
        return response()->json($quantity);
    }

    public function masive(Request $request)
    {
        try {
            if(!$request->hasFile('template')){
                return response()->json(['error' => 'No se subio ninguna plantilla']);
            }
            $file = $request->file('template');
            $destinationPath = public_path().'/storage/temp_templates';
            $path = $file->move($destinationPath,$file->getClientOriginalName());
            $collection = Excel::toCollection(new UsersImport, $path)[0];
            unlink($path);
            $errors = [];
            $success = [];
            foreach ($collection as $product) {
                try {
                    $subcategory = Subcategories::where('name','=',$product['subcategoria'])->first();
                    $data = [
                        'ean' => $product['ean'],
                        'sku_plu' => $product['sku_plu'],
                        'name' => $product['nombre'],
                        'price' => $product['precio'],
                        'supplier_id' => $product['proveedor_id'],
                        'subcategory_id' => $subcategory->id,
                        'brand' => $product['marca'],
                        'units' => $product['unidades'],
                        'health_register_file_id' => null
                    ];
                    $this->createProduct($data);
                    array_push($success, [ 'row' => $product ]);
                } catch (\Throwable $th) {
                    array_push($errors, [
                        'error' => $th->getMessage(),
                        'row' => $product
                    ]);
                }
            }
            return response()->json(['errors' => $errors, 'success' => $success]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }

    }

    protected function validator(array $data)
    {
        $rules = [
            'ean' => ['required','string','max:255'],
            'sku_plu' => ['required','string','max:255'],
            'name' => ['required'],
            'price' => ['required','string','max:100'],
            'subcategory_id' => ['required','integer','exists:subcategories,id'],
            'supplier_id' => ['required','integer','exists:suppliers,id'],
            'brand' => ['required','string','max:100'],
            'units' => ['required','string','max:100']
        ];

        return Validator::make($data, $rules);
    }

    private function createFile($file,$product){
        $name = time().$file->getClientOriginalName();
        $extension = $file->getClientMimeType();
        $path = $file->move(public_path().'/storage/products/'.$product->id.'/',$name);
        $fileRow = FilesModel::create([
            'name' => $name,
            'type' => $extension,
            'observations' => 'registro sanitario'
        ]);
        $product->update(['health_register_file_id' => $fileRow->id]);
    }

    public function barcode($ean){
        $image = base64_decode(DNS1D::getBarcodePNG($ean, 'EAN13'));
        return response($image)->header('Content-Type', 'image/png');
    }

}
