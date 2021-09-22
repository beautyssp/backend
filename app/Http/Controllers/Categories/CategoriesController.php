<?php

namespace App\Http\Controllers\Categories;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Categories;

class CategoriesController extends Controller
{
    public function index(Request $request){
        try {
            $categories = Categories::all();
            foreach ($categories as &$category) {
                $category->supplier;
                $category->subcategories;
            }
            return response()->json([ 'data' => $categories ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function search(Request $request){
        try {
            $id = $request->input('id');
            $category = Categories::find($id);
            return response()->json([ 'data' => $category ]);
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

            $data = $request->only('name','supplier_id');

            $id = $request->input('id');

            if(isset($id) && $id != 'null'){
                $category = Categories::find($id);
                $data['last_update_by'] = $request->user()->id;
                $category->update($data);
            } else {
                $data['create_by'] = $request->user()->id;
                $category = Categories::create($data);
            }

            return response()->json([ 'data' => $category ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function delete($id){
        try {
            Categories::destroy($id);
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required','string','max:100'],
            'supplier_id' => ['required','exists:suppliers,id']
        ];

        return Validator::make($data, $rules);
    }

}
