<?php

namespace App\Http\Controllers\Subcategories;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subcategories;

class SubcategoriesController extends Controller
{
    public function index(Request $request){
        try {
            $subcategories = Subcategories::all();
            foreach ($subcategories as &$subcategory) {
                $subcategory->category = $subcategory->category;
            }
            return response()->json([ 'data' => $subcategories ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function search(Request $request){
        try {
            $id = $request->input('id');
            $subcategory = Subcategories::find($id);
            return response()->json([ 'data' => $subcategory ]);
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

            $data = $request->only('name','category_id');

            $id = $request->input('id');

            if(isset($id) && $id != 'null'){
                $subcategory = Subcategories::find($id);
                $data['last_update_by'] = $request->user()->id;
                $subcategory->update($data);
            } else {
                $data['create_by'] = $request->user()->id;
                $subcategory = Subcategories::create($data);
            }

            return response()->json([ 'data' => $subcategory ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function delete($id){
        try {
            Subcategories::destroy($id);
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required','string','max:100'],
            'category_id' => ['required','exists:categories,id']
        ];

        return Validator::make($data, $rules);
    }

}
