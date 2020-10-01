<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use Uuid; //Generamos ID unico para cada registro

class DirectionController extends Controller
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
                'country' => 'required',
                'province' => 'required',
                'city' => 'required',
                'direction' => 'required',
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La direccion no se a guardado',
                    'data' => $validate->errors()
                );
            } else {

                //validadcion si es correcta
                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addDirection ?,?,?,?,?,?,?,?', [
                    $params_array['clientId'],
                    $params_array['country'],
                    $params_array['province'],
                    $params_array['city'],
                    $params_array['zipCode'],
                    $params_array['direction'],
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
        $directions = DB::select('exec pa_readDirections');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $directions
        ]);
    }

    public function show($clientId)
    {
        $direction = DB::select('exec pa_selectDirections ?', $clientId);
        if (is_object($direction)) {
            $data = [
                'code' => 200,
                'status' => 'Direcion encontrada correctamente',
                'data' => $direction
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'Cliente no cuenta con direciones'
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
                'id' => 'required',
                'country' => 'required',
                'province' => 'required',
                'city' => 'required',
                'direction' => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha actualizado la direccion, faltan datos',
                    'data' => $validate->errors()
                ];
            } else {
                $id = $params_array['id'];
                unset($params_array['id']);
                unset($params_array['created_at']);
                $params_array['updated_at'] = new \DateTime();

                DB::update('exec pa_updateDirection ?,?,?,?,?,?,?', [
                    $params_array['clientId'],
                    $params_array['country'],
                    $params_array['province'],
                    $params_array['city'],
                    $params_array['zipCode'],
                    $params_array['direction'],
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

    public function destroy($id)
    {
        if (isset($id)) {
            $delete = DB::delete('exec pa_deleteDirections ?', $id);
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
                'data' => 'No se encontro la direccion'
            ];
        }

        return response()->json($data, $data['code']);
    }

    private function getIdentity($request)
    {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $client = $jwtAuth->checkToken($token, true);

        return $client;
    }
}
