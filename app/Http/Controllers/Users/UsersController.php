<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function index(Request $request){
        try {
            $users = User::where('id','!=',1)->get();
            return response()->json([ 'data' => $users ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function find(Request $request){
        try {
            $id = $request->input('id');
            $user = User::find($id);
            return response()->json(['data' => $user ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function findEmail(Request $request){
        try {
            $email = $request->input('email');
            $where = ['email' => $email];
            $id = $request->input('id');
            if(isset($id)){
                array_push($where, ['id','!=',$id]);
            }
            $user = User::where($where)->first();
            return response()->json(['userExists' => isset($user) ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function create(Request $request){

        try {
            $validator = $this->validator($request->all());
    
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages()
                ], 200);
            }
    
            $id = $request->input('id');
            $pass = $request->input('password');
            $data = $request->only('name','lastname','type','email','permissions');
            if(isset($pass)){
                $data['password'] = Hash::make($pass);
            }
    
            if(isset($id)){
                $user = User::find($id);
                $user->update($data);
            } else {
                $user = User::create($data);
            }
    
            return response()->json(['proccess' => 'ok']);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }

    }

    protected function validator(array $data)
    {
        $rules = [
            'id' => ['nullable','exists:users'],
            'name' => ['required','string','max:255'],
            'lastname' => ['required','string','max:255'],
            'password' => ['nullable','string','confirmed'],
            'email' => ['required','email','confirmed','unique:users,email,'.$data['id'].",id",'max:255'],
            'permissions' => ['required','string','max:2000']
        ];

        return Validator::make($data, $rules);
    }


}
