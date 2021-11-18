<?php

namespace App\Http\Controllers\Suppliers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Supplier;
use App\Models\FilesModel;


class SuppliersController extends Controller
{

    public function index(Request $request){

        try {
            $suppliers = Supplier::all();
            foreach ($suppliers as &$supplier) {
                $supplier->certificate;
                $supplier->categories = $supplier->categories;
                foreach ($supplier->categories as &$category) {
                    $category->subcategories;
                }
            }
            return response()->json([ 'data' => $suppliers ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }

    }

    public function search(Request $request){
        try {
            $id = $request->input('id');
            $supplier = Supplier::find($id);
            $supplier->file = $supplier->certificate;
            return response()->json([ 'data' => $supplier ]);
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

            $data = $request->only(
                'name',
                'nit',
                'email',
                'telephone',
                'cellphone',
                'address',
                'city',
                'country',
                'legal_representative',
                'type_person',
                'economic_activity',
                'banco'
            );

            $id = $request->input('id');
            $file = $request->file('image');
            if(isset($id) && $id != 'null'){
                $supplier = Supplier::find($id);
                $data['last_update_by'] = $request->user()->id;
                $supplier->update($data);
                if(isset($file)){
                    $certifiaction = FilesModel::find($supplier->bank_certificate);
                    if(isset($certifiaction)){
                        //delete file
                        $file_path = public_path().'/storage/suppliers/'.$supplier->id.'/'.$certifiaction->name;
                        unlink($file_path);

                        //create new file
                        $name = time().$file->getClientOriginalName();
                        $extension = $file->getClientMimeType();
                        $path = $file->move(public_path().'/storage/suppliers/'.$supplier->id.'/',$name);

                        //Update file record 
                        $certifiaction->update([
                            'name' => $name,
                            'type' => $extension,
                            'observations' => 'certificación bancaria'
                        ]);
                    } else {
                        $this->createFile($file,$supplier);
                    }
                }
            } else {
                $data['create_by'] = $request->user()->id;
                $supplier = Supplier::create($data);
                if(isset($file)){
                    $this->createFile($file,$supplier);
                }
            }

            $supplier->file = $supplier->bank_certificate;

            return response()->json([ 'success' => 'OK' ]);

        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    private function createFile($file,$supplier){
        $name = time().$file->getClientOriginalName();
        $extension = $file->getClientMimeType();
        $path = $file->move(public_path().'/storage/suppliers/'.$supplier->id.'/',$name);
        $fileRow = FilesModel::create([
            'name' => $name,
            'type' => $extension,
            'observations' => 'certificación bancaria'
        ]);
        $supplier->update(['bank_certificate' => $fileRow->id]);
    }

    public function delete($id){
        try {
            $supplier = Supplier::find($id);
            $certifiaction = FilesModel::find($supplier->bank_certificate);
            if($certifiaction){
                $file_path = public_path().'/storage/suppliers/'.$supplier->id.'/'.$certifiaction->name;
                unlink($file_path);
                FilesModel::destroy($certifiaction->id);
            }
            Supplier::destroy($id);
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required','string','max:100'],
            'nit' => ['required','string'],
            'email' => ['required','email'],
            'telephone' => ['required','string','max:50'],
            'cellphone' => ['required','string','max:50'],
            'address' => ['required','string','max:200'],
            'city' => ['required','string','max:200'],
            'country' => ['required','string','max:200'],
            'legal_representative' => ['required','string','max:255'],
            'type_person' => ['required','string'],
            'economic_activity' => ['required','string','max:255'],
            'banco' => ['required','string','max:255']
        ];

        $messages = [
            'name.required' => 'Es obligatorio que registres tu nombre',
            'name.max' => 'Tu nombre debe ser de máximo 50 caracteres',
            'name.regex' => 'Tu nombre sólo debe contener letras y espacios',
            'email.required' => 'Es obligatorio que registres una dirección de correo',
            'email.max' => 'La dirección de correo no debe contener más de 50 caracteres',
        ];

        return Validator::make($data, $rules, $messages);
    }
}
