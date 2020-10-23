<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Uuid;

class ClientController extends Controller {
    public function __construct(){ $this->middleware('api.auth'); }

    //GET ALL
    public function index(){
        $clients = DB::select('exec pa_readClients');

        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'data'      => $clients
        ]);
    }

    //GET ONE
    public function show($id) {
        $client = DB::select('exec pa_selectClient ?', [$id]);

        if (count($client) > 0) {
            $data = [
                'code'      => 200,
                'status'    => 'success',
                'data'      => $client
            ];
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Error, el cliente no existe.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //POST
    public function store(Request $request){

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

              $validate = \Validator::make($params_array, [
                'identificationCard'    => 'required|unique:clients',
                'name'                  => 'required',
                'surname'               => 'required',
                'telephone'             => 'required',
                'email'                 => 'required|email|unique:clients',
            ]);

            if ($validate->fails()) {

                $data = array(
                    'status'    => 'error',
                    'code'      => 404,
                    'message'   => 'Error, hay campos vacíos o no cumplen los requisitos.',
                    'data'      => $validate->errors()
                );

            } else {

                $params_array['id'] = Uuid::generate()->string;
                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addClient ?,?,?,?,?,?,?,?', [
                    $params_array['id'],
                    $params_array['identificationCard'],
                    $params_array['name'],
                    $params_array['surname'],
                    $params_array['telephone'],
                    $params_array['email'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Cliente registrado correctamente.',
                    'data'      => $params_array
                ];
            }
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'No has ingresado ningún dato.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //UPDATE
    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            $validate = Validator::make($params_array, [
                'identificationCard'    => 'required',
                'name'                  => 'required',
                'surname'               => 'required',
                'telephone'             => 'required',
                'email'                 => 'required|email',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'No se ha actualizado el cliente, faltan datos',
                    'data'      => $validate->errors()
                ];
            } else {

                $unique = DB::select('exec pa_selectIdentificationCardClient ?, ?', [$params->identificationCard, $params->email]);

                if ((count($unique) > 0) && (strtoupper($id) != $unique[0]->id)) {

                    $data = array(
                        'code'      => 404,
                        'status'    => 'error',
                        'message'   => 'El cliente ya existe'
                    );
                    return response()->json($data, $data['code']);
                }

                unset($params_array['id']);
                unset($params_array['created_at']);

                $params_array['id'] = $id;
                $params_array['updated_at'] = new \DateTime();

             $f =    DB::update('exec pa_updateClient ?,?,?,?,?,?,?', [
                    $params_array['id'],
                    $params_array['identificationCard'],
                    $params_array['name'],
                    $params_array['surname'],
                    $params_array['telephone'],
                    $params_array['email'],
                    $params_array['updated_at'],

                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Cliente registrado correctamente.',
                    'data'      => $params_array
                ];
            }
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'No has ingresado ningún dato.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //DELETE
    public function destroy($id)
    {
        if (isset($id)) {

            $client = DB::select('exec pa_selectClient ?', [$id]);

            if (count($client) > 0) {

                DB::delete('exec pa_deleteClient ?', [$id]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Cliente eliminado correctamente.'
                ];
            } else {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Error, el cliente no puede ser eliminado, tiene pedidos asociados.'
                ];
            }
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Cliente no encontrado.'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
