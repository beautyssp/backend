<?php

namespace App\Http\Controllers\Clients;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Client;

class ClientsController extends Controller
{
    public function index(Request $request){
        try {
            $clients = Client::all();
            return response()->json([ 'data' => $clients ]);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage() ]);
        }
    }

    public function search(Request $request){
        try {
            $id = $request->input('id');
            $client = Client::find($id);
            return response()->json([ 'data' => $client ]);
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
            $data = $request->only('name', 'lastname', 'email', 'cellphone', 'type_person', 'number_document');
            $id = $request->input('id');
            if(isset($id) && $id != 'null'){
                $client = Client::find($id);
                $data['last_update_by'] = $request->user()->id;
                $client->update($data);
            } else {
                $data['create_by'] = $request->user()->id;
                $client = Client::create($data);
            }
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    public function delete($id){
        try {
            $client = Client::find($id);
            Client::destroy($id);
            return response()->json([ 'success' => 'OK' ]);
        } catch (\Throwable $th) {
            return response()->json(['Error' => $th->getMessage()]);
        }
    }

    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required','string','max:200'],
            'lastname' => ['required','string','max:200'],
            'number_document' => ['required','string'],
            'email' => ['required','email'],
            'cellphone' => ['required','string','max:50'],
            'type_person' => ['required','string']
        ];

        $messages = [
            'name.required' => 'Es obligatorio que registres tu nombre',
            'name.max' => 'Tu nombre debe ser de máximo 200 caracteres',
            'name.regex' => 'Tu nombre sólo debe contener letras y espacios',
            'lastname.required' => 'Es obligatorio que registres tu apellido',
            'lastname.max' => 'Tu apellido debe ser de máximo 200 caracteres',
            'lastname.regex' => 'Tu apellido sólo debe contener letras y espacios',
            'email.required' => 'Es obligatorio que registres una dirección de correo',
            'email.max' => 'La dirección de correo no debe contener más de 50 caracteres',
        ];

        return Validator::make($data, $rules, $messages);
    }
}
