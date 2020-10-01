<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use Uuid; //Generamos ID unico para cada registro

class ClientController extends Controller
{
    public function __construct()
    {
        //$this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {
            $collaborator = $this->getIdentity($request);

            //validar datos
            $validate = \Validator::make($params_array, [
                'identificationCard' => 'required',
                'name' => 'required',
                'surname' => 'required',
                'telephone' => 'required',
                'email' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El cliente no se a guardado',
                    'data' => $validate->errors()
                );
            } else {

                //validadcion si es correcta
                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addClient ?,?,?,?,?,?,?', [
                    $params_array['identificationCard'],
                    $params_array['name'],
                    $params_array['surname'],
                    $params_array['telephone'],
                    $params_array['email'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'data' => $params_array
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'Envia los datos correctamente'
            ];
        }

        //devolver respuesta
        return response()->json($data, $data['code']);
    }

    public function index()
    {
        $clients = DB::select('exec pa_readClients');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $clients
        ]);
    }

    public function show($Id)
    {
        $client = DB::select('exec pa_selectClients', $Id);
        if (is_object($direction)) {
            $data = [
                'code' => 200,
                'status' => 'Cliente encontrado correctamente',
                'data' => $client
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'Cliente no encontrado'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update(Request $request)
    {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $collaborator = $this->getIdentity($request);

            $validate = \Validator::make($params_array, [
                'identificationCard' => 'required',
                'name' => 'required',
                'surname' => 'required',
                'telephone' => 'required',
                'email' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha actualizado el cliente, faltan datos',
                    'data' => $validate->errors()
                ];
            } else {
                $id = $params_array['id'];
                unset($params_array['id']);
                unset($params_array['created_at']);
                $params_array['updated_at'] = new \DateTime();

                DB::update('exec pa_updateClient ?,?,?,?,?,?', [
                    $params_array['identificationCard'],
                    $params_array['name'],
                    $params_array['surname'],
                    $params_array['telephone'],
                    $params_array['email'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'data' => $params_array
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($idCard)
    {
        if (isset($id)) {
            $delete = DB::delete('exec pa_deleteClients ?', $idCard);
            if ($delete) {
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'data' => 'Se elimino correctamente'
                ];
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'data' => 'No se elimino correctamente'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'No se encontro el cliente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    private function getIdentity($request)
    {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $collaborator = $jwtAuth->checkToken($token, true);

        return $client;
    }
}